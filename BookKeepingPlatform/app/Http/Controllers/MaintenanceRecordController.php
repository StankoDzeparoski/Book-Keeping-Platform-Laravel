<?php

namespace App\Http\Controllers;

use App\Http\Requests\MaintenanceRecord\MaintenanceRecordStoreRequest;
use App\Http\Requests\MaintenanceRecord\MaintenanceRecordUpdateRequest;
use App\Models\MaintenanceRecord;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class MaintenanceRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|Factory|Application
    {
        // Only managers can view maintenance records
        if (!auth()->check() || !auth()->user()->isManager()) {
            abort(403, 'Unauthorized. Only managers can view maintenance records.');
        }

        $maintenanceRecords = MaintenanceRecord::query()
            ->with('equipment')
            ->when($request->has('search'),
                fn($q) => $q->whereHas('equipment', fn($eq) => $eq->where('brand', 'like', '%'.$request->get('search').'%'))
            )
            ->latest()
            ->paginate(10);

        return view('maintenanceRecord.index', compact('maintenanceRecords'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|Factory|Application
    {
        // Only managers can create maintenance records
        if (!auth()->check() || !auth()->user()->isManager()) {
            abort(403, 'Unauthorized. Only managers can create maintenance records.');
        }

        return view('maintenanceRecord.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MaintenanceRecordStoreRequest $request): RedirectResponse
    {
        // Only managers can create maintenance records
        if (!auth()->check() || !auth()->user()->isManager()) {
            abort(403, 'Unauthorized. Only managers can create maintenance records.');
        }

        MaintenanceRecord::query()->create($request->validated());

        return redirect()
            ->route('maintenanceRecord.index')
            ->with('success', 'Maintenance record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MaintenanceRecord $maintenanceRecord): View|Factory|Application
    {
        // Only managers can view maintenance records
        if (!auth()->check() || !auth()->user()->isManager()) {
            abort(403, 'Unauthorized. Only managers can view maintenance records.');
        }

        $maintenanceRecord->loadMissing('equipment');

        return view('maintenanceRecord.show', compact('maintenanceRecord'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MaintenanceRecord $maintenanceRecord): View|Factory|Application
    {
        // Only managers can edit maintenance records
        if (!auth()->check() || !auth()->user()->isManager()) {
            abort(403, 'Unauthorized. Only managers can edit maintenance records.');
        }

        return view('maintenanceRecord.edit', compact('maintenanceRecord'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MaintenanceRecordUpdateRequest $request, MaintenanceRecord $maintenanceRecord): RedirectResponse
    {
        // Only managers can update maintenance records
        if (!auth()->check() || !auth()->user()->isManager()) {
            abort(403, 'Unauthorized. Only managers can update maintenance records.');
        }

        $maintenanceRecord->update($request->validated());

        return redirect()
            ->route('maintenanceRecord.show', $maintenanceRecord)
            ->with('success', 'Maintenance record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MaintenanceRecord $maintenanceRecord): RedirectResponse
    {
        // Only managers can delete maintenance records
        if (!auth()->check() || !auth()->user()->isManager()) {
            abort(403, 'Unauthorized. Only managers can delete maintenance records.');
        }

        $maintenanceRecord->delete();

        return redirect()
            ->route('maintenanceRecord.index')
            ->with('success', 'Maintenance record deleted successfully.');
    }
}


