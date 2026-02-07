<?php

namespace App\Http\Controllers;

use App\Http\Requests\Shed\StoreShedRequest;
use App\Http\Requests\Shed\UpdateShedRequest;
use App\Services\FarmService;
use App\Services\ShedService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShedController extends Controller
{
    public function __construct(
        protected ShedService $shedService,
        protected FarmService $farmService
    ) {}

    /**
     * Display a paginated list of sheds for a farm.
     */
    public function index(Request $request, int $farm): Response
    {
        $farmModel = $this->farmService->getById($farm);
        $perPage = (int) $request->get('per_page', 15);
        $search = $request->get('search', '');
        $sheds = $this->shedService->getPaginatedByFarm($farm, $search ?: null, $perPage);
        $sheds->appends($request->only('search'));

        return Inertia::render('sheds/Index', [
            'farm' => $farmModel,
            'sheds' => $sheds,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Show the form for creating a new shed.
     */
    public function create(int $farm): Response
    {
        $farmModel = $this->farmService->getById($farm);

        return Inertia::render('sheds/Create', [
            'farm' => $farmModel,
        ]);
    }

    /**
     * Display the specified shed.
     */
    public function show(int $shed): Response
    {
        $shedModel = $this->shedService->getById($shed);

        return Inertia::render('sheds/Show', [
            'shed' => $shedModel->load('farm'),
        ]);
    }

    /**
     * Show the form for editing the specified shed.
     */
    public function edit(int $shed): Response
    {
        $shedModel = $this->shedService->getById($shed);

        return Inertia::render('sheds/Edit', [
            'shed' => $shedModel->load('farm'),
        ]);
    }

    /**
     * Store a newly created shed.
     */
    public function store(StoreShedRequest $request, int $farm): RedirectResponse
    {
        $this->shedService->create(
            $farm,
            $request->validated(),
            $request->user()?->id
        );

        return redirect()->route('farms.sheds.index', $farm)->with('success', 'Shed created successfully.');
    }

    /**
     * Update the specified shed.
     */
    public function update(UpdateShedRequest $request, int $shed): RedirectResponse
    {
        $shedModel = $this->shedService->getById($shed);
        $updated = $this->shedService->update(
            $shed,
            $request->validated(),
            $request->user()?->id
        );

        if ($updated === null) {
            return redirect()->back()->with('error', 'Shed not found.');
        }

        return redirect()->route('sheds.show', $shed)->with('success', 'Shed updated successfully.');
    }

    /**
     * Remove the specified shed (soft delete).
     */
    public function destroy(int $shed): RedirectResponse
    {
        $shedModel = $this->shedService->getById($shed);
        $farmId = $shedModel->farm_id;
        $deleted = $this->shedService->delete($shed);

        if (! $deleted) {
            return redirect()->back()->with('error', 'Shed not found.');
        }

        return redirect()->route('farms.sheds.index', $farmId)->with('success', 'Shed deleted successfully.');
    }

    /**
     * Remove multiple sheds (soft delete) for a farm.
     */
    public function bulkDestroy(Request $request, int $farm): RedirectResponse
    {
        $ids = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'min:1'],
        ])['ids'];

        $deleted = $this->shedService->deleteMany($ids);

        if ($deleted === 0) {
            return redirect()->back()->with('error', 'No sheds were deleted.');
        }

        $message = $deleted === 1
            ? 'Shed deleted successfully.'
            : "{$deleted} sheds deleted successfully.";

        return redirect()->route('farms.sheds.index', $farm)->with('success', $message);
    }
}
