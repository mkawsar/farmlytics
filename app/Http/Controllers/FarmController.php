<?php

namespace App\Http\Controllers;

use App\Http\Requests\Farm\StoreFarmRequest;
use App\Http\Requests\Farm\UpdateFarmRequest;
use App\Services\FarmService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FarmController extends Controller
{
    public function __construct(
        protected FarmService $farmService
    ) {}

    /**
     * Display a paginated list of farms.
     */
    public function index(Request $request): Response
    {
        $perPage = (int) $request->get('per_page', 15);
        $farms = $this->farmService->getAllPaginated([], $perPage);

        return Inertia::render('farms/Index', [
            'farms' => $farms,
        ]);
    }

    /**
     * Show the form for creating a new farm.
     */
    public function create(): Response
    {
        return Inertia::render('farms/Create');
    }

    /**
     * Display the specified farm.
     */
    public function show(int $farm): Response
    {
        $farm = $this->farmService->getById($farm);

        return Inertia::render('farms/Show', [
            'farm' => $farm,
        ]);
    }

    /**
     * Show the form for editing the specified farm.
     */
    public function edit(int $farm): Response
    {
        $farm = $this->farmService->getById($farm);

        return Inertia::render('farms/Edit', [
            'farm' => $farm,
        ]);
    }

    /**
     * Store a newly created farm.
     */
    public function store(StoreFarmRequest $request): RedirectResponse
    {
        $this->farmService->create(
            $request->validated(),
            $request->user()?->id
        );

        return redirect()->route('farms.index')->with('success', 'Farm created successfully.');
    }

    /**
     * Update the specified farm.
     */
    public function update(UpdateFarmRequest $request, int $farm): RedirectResponse
    {
        $updated = $this->farmService->update(
            $farm,
            $request->validated(),
            $request->user()?->id
        );

        if ($updated === null) {
            return redirect()->back()->with('error', 'Farm not found.');
        }

        return redirect()->route('farms.show', $farm)->with('success', 'Farm updated successfully.');
    }

    /**
     * Remove the specified farm (soft delete).
     */
    public function destroy(int $farm): RedirectResponse
    {
        $deleted = $this->farmService->delete($farm);

        if (! $deleted) {
            return redirect()->back()->with('error', 'Farm not found.');
        }

        return redirect()->route('farms.index')->with('success', 'Farm deleted successfully.');
    }
}
