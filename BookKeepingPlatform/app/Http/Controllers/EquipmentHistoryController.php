<?php

namespace App\Http\Controllers;

use App\Http\Requests\EquipmentHistory\EquipmentHistoryStoreRequest;
use App\Http\Requests\EquipmentHistory\EquipmentHistoryUpdateRequest;
use App\Models\EquipmentHistory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class EquipmentHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|Factory|Application
    {
        // Only managers can view equipment history
        if (!auth()->check() || !auth()->user()->isManager()) {
            abort(403, 'Unauthorized. Only managers can view equipment history.');
        }

        $equipmentHistories = EquipmentHistory::query()
            ->with('equipment')
            ->when($request->has('search'),
                fn($q) => $q->whereHas('equipment', fn($eq) => $eq->where('brand', 'like', '%'.$request->get('search').'%'))
            )
            ->latest()
            ->paginate(10);

        return view('equipmentHistory.index', compact('equipmentHistories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|Factory|Application
    {
        // Only managers can create equipment history
        if (!auth()->check() || !auth()->user()->isManager()) {
            abort(403, 'Unauthorized. Only managers can create equipment history.');
        }

        return view('equipmentHistory.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EquipmentHistoryStoreRequest $request): RedirectResponse
    {
        // Only managers can create equipment history
        if (!auth()->check() || !auth()->user()->isManager()) {
            abort(403, 'Unauthorized. Only managers can create equipment history.');
        }

        EquipmentHistory::query()->create($request->validated());

        return redirect()
            ->route('equipmentHistory.index')
            ->with('success', 'Equipment history created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EquipmentHistory $equipmentHistory): View|Factory|Application
    {
        $equipmentHistory->loadMissing('equipment');

        return view('equipmentHistory.show', compact('equipmentHistory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EquipmentHistory $equipmentHistory): View|Factory|Application
    {
        // Only managers can edit equipment history
        if (!auth()->check() || !auth()->user()->isManager()) {
            abort(403, 'Unauthorized. Only managers can edit equipment history.');
        }

        return view('equipmentHistory.edit', compact('equipmentHistory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EquipmentHistoryUpdateRequest $request, EquipmentHistory $equipmentHistory): RedirectResponse
    {
        // Only managers can update equipment history
        if (!auth()->check() || !auth()->user()->isManager()) {
            abort(403, 'Unauthorized. Only managers can update equipment history.');
        }

        $equipmentHistory->update($request->validated());

        return redirect()
            ->route('equipmentHistory.show', $equipmentHistory)
            ->with('success', 'Equipment history updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EquipmentHistory $equipmentHistory): RedirectResponse
    {
        // Only managers can delete equipment history
        if (!auth()->check() || !auth()->user()->isManager()) {
            abort(403, 'Unauthorized. Only managers can delete equipment history.');
        }

        $equipmentHistory->delete();

        return redirect()
            ->route('equipmentHistory.index')
            ->with('success', 'Equipment history deleted successfully.');
    }
}
