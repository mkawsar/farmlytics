<?php

namespace App\Http\Controllers;

use App\Http\Requests\Animal\StoreAnimalRequest;
use App\Http\Requests\Animal\UpdateAnimalRequest;
use App\Models\Shed;
use App\Services\AnimalService;
use App\Services\ShedService;
use App\Services\TransactionService;
use App\Support\BreedCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Inertia\Inertia;
use Inertia\Response;

class AnimalController extends Controller
{
    public function __construct(
        protected AnimalService $animalService,
        protected ShedService $shedService,
        protected TransactionService $transactionService
    ) {}

    /**
     * Return the next animal_id for a given breed (for live preview on create form).
     * GET /animals/next-id?breed=Holstein → {"animal_id": "HF-202602-1"}
     */
    public function nextAnimalId(Request $request): \Illuminate\Http\JsonResponse
    {
        $breed = trim((string) $request->query('breed', ''));
        $animalId = $breed !== ''
            ? $this->animalService->generateAnimalId($breed)
            : '';

        return response()->json(['animal_id' => $animalId]);
    }

    /**
     * Display a paginated list of all animals (top-level nav).
     */
    public function indexAll(Request $request): Response
    {
        $perPage = (int) $request->get('per_page', 15);
        $search = $request->get('search', '');
        $animals = $this->animalService->getPaginated($search ?: null, $perPage);
        $animals->appends($request->only('search'));

        return Inertia::render('animals/List', [
            'animals' => $animals,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Show shed selector for adding a new animal (from global animals index).
     */
    public function selectShedForCreate(): Response
    {
        $sheds = Shed::with('farm')->orderBy('name')->get();

        return Inertia::render('animals/SelectShed', [
            'sheds' => $sheds,
        ]);
    }

    /**
     * Display a paginated list of animals for a shed.
     */
    public function index(Request $request, int $farm, int $shed): Response
    {
        $shedModel = $this->shedService->getById($shed)->load('farm');
        $perPage = (int) $request->get('per_page', 15);
        $search = $request->get('search', '');
        $animals = $this->animalService->getPaginatedByShed($shed, $search ?: null, $perPage);
        $animals->appends($request->only('search'));

        return Inertia::render('animals/Index', [
            'farm' => $shedModel->farm,
            'shed' => $shedModel,
            'animals' => $animals,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Show the form for creating a new animal.
     */
    public function create(int $farm, int $shed): Response
    {
        $shedModel = $this->shedService->getById($shed)->load('farm');

        return Inertia::render('animals/Create', [
            'farm' => $shedModel->farm,
            'shed' => $shedModel,
            'breedOptions' => BreedCode::optionsForSelect(),
        ]);
    }

    /**
     * Display the specified animal.
     */
    public function show(int $animal): Response
    {
        $animalModel = $this->animalService->getById($animal);
        $end = Carbon::today();
        $startYear = $end->copy()->subYear();
        $profitLossYear = $this->transactionService->getProfitLossForAnimal($animal, $startYear, $end);
        $profitLossLifetime = $this->transactionService->getProfitLossForAnimal(
            $animal,
            $animalModel->created_at->copy()->startOfDay(),
            $end
        );

        return Inertia::render('animals/Show', [
            'animal' => $animalModel->load(['shed.farm', 'farm']),
            'profitLossYear' => $profitLossYear,
            'profitLossLifetime' => $profitLossLifetime,
        ]);
    }

    /**
     * Download PDF report: full lifecycle (purchase, total invest, selling price, profit/loss) for this cow.
     */
    public function downloadLifecycleReport(int $animal): HttpResponse
    {
        $animalModel = $this->animalService->getById($animal);
        $lifecycle = $this->transactionService->getLifecycleSummaryForAnimal($animal);

        $pdf = Pdf::loadView('reports.cow-lifecycle-pdf', [
            'animal' => $animalModel,
            'lifecycle' => $lifecycle,
        ]);

        $filename = 'cow-lifecycle-'.preg_replace('/[^a-zA-Z0-9_-]/', '-', $animalModel->animal_id).'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Show the form for editing the specified animal.
     */
    public function edit(int $animal): Response
    {
        $animalModel = $this->animalService->getById($animal);

        return Inertia::render('animals/Edit', [
            'animal' => $animalModel->load(['shed.farm', 'farm']),
        ]);
    }

    /**
     * Store a newly created animal.
     */
    public function store(StoreAnimalRequest $request, int $farm, int $shed): RedirectResponse
    {
        $animal = $this->animalService->create(
            $shed,
            $request->validated(),
            $request->user()?->id
        );

        $validated = $request->validated();
        if (isset($validated['purchase_price']) && (float) $validated['purchase_price'] > 0) {
            $this->transactionService->createExpense([
                'animal_id' => $animal->id,
                'expense_type' => \App\Enums\ExpenseType::COW_PURCHASE->value,
                'amount' => (float) $validated['purchase_price'],
                'transaction_date' => isset($validated['purchase_date']) && $validated['purchase_date']
                    ? $validated['purchase_date']
                    : Carbon::today()->toDateString(),
            ], $request->user()?->id);
        }

        return redirect()->route('farms.sheds.animals.index', [$farm, $shed])->with('success', 'Animal created successfully.');
    }

    /**
     * Update the specified animal.
     */
    public function update(UpdateAnimalRequest $request, int $animal): RedirectResponse
    {
        $animalModel = $this->animalService->getById($animal);
        $updated = $this->animalService->update(
            $animal,
            $request->validated(),
            $request->user()?->id
        );

        if ($updated === null) {
            return redirect()->back()->with('error', 'Animal not found.');
        }

        return redirect()->route('animals.show', $animal)->with('success', 'Animal updated successfully.');
    }

    /**
     * Remove the specified animal (soft delete).
     */
    public function destroy(int $animal): RedirectResponse
    {
        $animalModel = $this->animalService->getById($animal);
        $shedId = $animalModel->shed_id;
        $farmId = $animalModel->farm_id;
        $deleted = $this->animalService->delete($animal);

        if (! $deleted) {
            return redirect()->back()->with('error', 'Animal not found.');
        }

        return redirect()->back()->with('success', 'Animal deleted successfully.');
    }

    /**
     * Remove multiple animals (soft delete) for a shed.
     */
    public function bulkDestroy(Request $request, int $farm, int $shed): RedirectResponse
    {
        $ids = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'min:1'],
        ])['ids'];

        $deleted = $this->animalService->deleteMany($ids);

        if ($deleted === 0) {
            return redirect()->back()->with('error', 'No animals were deleted.');
        }

        $message = $deleted === 1
            ? 'Animal deleted successfully.'
            : "{$deleted} animals deleted successfully.";

        return redirect()->route('farms.sheds.animals.index', [$farm, $shed])->with('success', $message);
    }

    /**
     * Remove multiple animals (soft delete) from the global animals index.
     */
    public function bulkDestroyAll(Request $request): RedirectResponse
    {
        $ids = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'min:1'],
        ])['ids'];

        $deleted = $this->animalService->deleteMany($ids);

        if ($deleted === 0) {
            return redirect()->back()->with('error', 'No animals were deleted.');
        }

        $message = $deleted === 1
            ? 'Animal deleted successfully.'
            : "{$deleted} animals deleted successfully.";

        return redirect()->route('animals.index')->with('success', $message);
    }
}
