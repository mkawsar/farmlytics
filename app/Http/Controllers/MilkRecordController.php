<?php

namespace App\Http\Controllers;

use App\Http\Requests\MilkRecord\StoreMilkRecordRequest;
use App\Http\Requests\MilkRecord\UpdateMilkRecordRequest;
use App\Services\AnimalService;
use App\Services\MilkRecordService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MilkRecordController extends Controller
{
    public function __construct(
        protected MilkRecordService $milkRecordService,
        protected AnimalService $animalService
    ) {}

    public function index(Request $request, int $animal): Response
    {
        $animalModel = $this->animalService->getById($animal);
        $perPage = (int) $request->get('per_page', 15);
        $milkRecords = $this->milkRecordService->getPaginatedByAnimal($animal, $perPage);
        $milkRecords->appends($request->only('per_page'));

        return Inertia::render('animals/milk/Index', [
            'animal' => $animalModel->load(['shed.farm']),
            'milkRecords' => $milkRecords,
        ]);
    }

    public function create(int $animal): Response
    {
        $animalModel = $this->animalService->getById($animal);

        return Inertia::render('animals/milk/Create', [
            'animal' => $animalModel->load(['shed.farm']),
        ]);
    }

    public function store(StoreMilkRecordRequest $request, int $animal): RedirectResponse
    {
        $this->milkRecordService->createForAnimal($animal, $request->validated(), $request->user()?->id);

        return redirect()->route('animals.milk.index', $animal)->with('success', 'Milk record added successfully.');
    }

    public function edit(int $animal, int $milkRecord): Response
    {
        $animalModel = $this->animalService->getById($animal);
        $record = $this->milkRecordService->getById($milkRecord);
        if ($record->animal_id !== $animal) {
            abort(404);
        }

        return Inertia::render('animals/milk/Edit', [
            'animal' => $animalModel->load(['shed.farm']),
            'milkRecord' => $record,
        ]);
    }

    public function update(UpdateMilkRecordRequest $request, int $animal, int $milkRecord): RedirectResponse
    {
        $record = $this->milkRecordService->getById($milkRecord);
        if ($record->animal_id !== $animal) {
            abort(404);
        }
        $this->milkRecordService->update($milkRecord, $request->validated());

        return redirect()->route('animals.milk.index', $animal)->with('success', 'Milk record updated successfully.');
    }

    public function destroy(int $animal, int $milkRecord): RedirectResponse
    {
        $record = $this->milkRecordService->getById($milkRecord);
        if ($record->animal_id !== $animal) {
            abort(404);
        }
        $this->milkRecordService->delete($milkRecord);

        return redirect()->route('animals.milk.index', $animal)->with('success', 'Milk record deleted successfully.');
    }
}
