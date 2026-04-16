<?php

namespace App\Http\Controllers;

use App\Actions\LoanEquipmentAction;
use App\Actions\ReturnEquipmentAction;
use App\Http\Requests\Equipment\EquipmentStoreRequest;
use App\Http\Requests\Equipment\EquipmentUpdateRequest;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|Factory|Application
    {
        $equipment = Equipment::query()
            ->with('user')
            ->when($request->has('search'),
                fn($q) => $q->where('brand', 'like', '%'.$request->get('search').'%')
                    ->orWhere('model', 'like', '%'.$request->get('search').'%')
            )
            ->latest()
            ->paginate(10);

        $users = User::all();

        return view('equipment.index', compact('equipment', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|Factory|Application
    {
        // Only managers can create equipment
        if (!auth()->check() || !auth()->user()->isManager()) {
            abort(403, 'Unauthorized. Only managers can create equipment.');
        }

        return view('equipment.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EquipmentStoreRequest $request): RedirectResponse
    {
        // Only managers can create equipment
        if (!auth()->check() || !auth()->user()->isManager()) {
            abort(403, 'Unauthorized. Only managers can create equipment.');
        }

        Equipment::query()->create($request->validated());

        return redirect()
            ->route('equipment.index')
            ->with('success', 'Equipment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Equipment $equipment): View|Factory|Application
    {
        $equipment->loadMissing('user', 'maintenanceRecords', 'history');

        return view('equipment.show', compact('equipment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Equipment $equipment): View|Factory|Application
    {
        // Only managers can edit equipment
        if (!auth()->check() || !auth()->user()->isManager()) {
            abort(403, 'Unauthorized. Only managers can edit equipment.');
        }

        return view('equipment.edit', compact('equipment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EquipmentUpdateRequest $request, Equipment $equipment): RedirectResponse
    {
        // Only managers can update equipment
        if (!auth()->check() || !auth()->user()->isManager()) {
            abort(403, 'Unauthorized. Only managers can update equipment.');
        }

        $equipment->update($request->validated());

        return redirect()
            ->route('equipment.show', $equipment)
            ->with('success', 'Equipment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Equipment $equipment): RedirectResponse
    {
        // Only managers can delete equipment
        if (!auth()->check() || !auth()->user()->isManager()) {
            abort(403, 'Unauthorized. Only managers can delete equipment.');
        }

        $equipment->delete();

        return redirect()
            ->route('equipment.index')
            ->with('success', 'Equipment deleted successfully.');
    }

    /**
     * Loan equipment to a user.
     */
    public function loan(Request $request, Equipment $equipment): RedirectResponse
    {
        // Both managers and employees can loan equipment
        if (!auth()->check() || (!auth()->user()->isManager() && !auth()->user()->isEmployee())) {
            abort(403, 'Unauthorized. Only managers and employees can loan equipment.');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'loan_date' => 'required|date_format:Y-m-d|today_or_after',
            'loan_expire_date' => 'required|date_format:Y-m-d|after:loan_date',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $action = new LoanEquipmentAction();
        $action->execute($equipment, $user, $validated['loan_date'], $validated['loan_expire_date']);

        return redirect()
            ->route('equipment.index')
            ->with('success', 'Equipment loaned successfully.');
    }

    /**
     * Return equipment from a user.
     */
    public function return(Request $request, Equipment $equipment): RedirectResponse
    {
        // Both managers and employees can return equipment
        if (!auth()->check() || (!auth()->user()->isManager() && !auth()->user()->isEmployee())) {
            abort(403, 'Unauthorized. Only managers and employees can return equipment.');
        }

        $validated = $request->validate([
            'return_date' => 'required|date_format:Y-m-d|today_or_before',
        ]);

        $action = new ReturnEquipmentAction();
        $action->execute($equipment, $validated['return_date']);

        return redirect()
            ->route('equipment.index')
            ->with('success', 'Equipment returned successfully.');
    }
}
