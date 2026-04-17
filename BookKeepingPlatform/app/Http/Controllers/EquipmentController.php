<?php

namespace App\Http\Controllers;

use App\Actions\LoanEquipmentAction;
use App\Actions\RepairEquipmentAction;
use App\Actions\FinishRepairAction;
use App\Actions\ReturnEquipmentAction;
use App\Enums\Condition;
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
        $query = Equipment::query()->with('user');

        // Filter equipment based on user role
        if (auth()->check() && auth()->user()->isEmployee() && !auth()->user()->isManager()) {
            // Employees can only see equipment assigned to them or available equipment (exclude broken)
            $query->where(function ($q) {
                $q->where('user_id', auth()->id())
                  ->orWhere('status', 'Available');
            })->where('condition', '!=', Condition::BROKEN->value);
        }

        // Apply search filter
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('brand', 'like', '%'.$request->get('search').'%')
                  ->orWhere('model', 'like', '%'.$request->get('search').'%');
            });
        }

        $equipment = $query->latest()->paginate(10);

        // Get users for loan modal - employees only see themselves
        if (auth()->check() && auth()->user()->isEmployee() && !auth()->user()->isManager()) {
            $users = collect([auth()->user()]);
        } else {
            $users = User::all();
        }

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
            'loan_date' => 'required|date_format:Y-m-d|after_or_equal:today',
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
        // Only managers or the assigned user can return equipment
        if (!auth()->check()) {
            abort(403, 'Unauthorized. You must be logged in.');
        }

        $isManager = auth()->user()->isManager();
        $isAssignedUser = $equipment->user_id === auth()->id();

        if (!$isManager && !$isAssignedUser) {
            abort(403, 'Unauthorized. Only the assigned user or managers can return this equipment.');
        }

        // Build validation rules - allow return dates up to the original loan expiration date
        $maxReturnDate = $equipment->loan_expire_date ? $equipment->loan_expire_date->format('Y-m-d') : now()->format('Y-m-d');

        $validated = $request->validate([
            'return_date' => 'required|date_format:Y-m-d|before_or_equal:' . $maxReturnDate,
        ]);

        $action = new ReturnEquipmentAction();
        $action->execute($equipment, $validated['return_date']);

        return redirect()
            ->route('equipment.index')
            ->with('success', 'Equipment returned successfully.');
    }

    /**
     * Repair equipment - create or update maintenance record.
     */
    public function repair(Request $request, Equipment $equipment): RedirectResponse
    {
        // Only managers can repair equipment
        if (!auth()->check() || !auth()->user()->isManager()) {
            abort(403, 'Unauthorized. Only managers can repair equipment.');
        }

        $validated = $request->validate([
            'description' => 'required|string|min:3',
            'cost' => 'required|integer|min:1',
            'maintenance_date' => 'required|date_format:Y-m-d',
        ]);

        $action = new RepairEquipmentAction();
        $action->execute($equipment, $validated['description'], $validated['cost'], $validated['maintenance_date']);

        return redirect()
            ->route('equipment.show', $equipment)
            ->with('success', 'Equipment repair recorded successfully.');
    }

    /**
     * Finish repair on equipment.
     */
    public function finishRepair(Equipment $equipment): RedirectResponse
    {
        // Only managers can finish repairs
        if (!auth()->check() || !auth()->user()->isManager()) {
            abort(403, 'Unauthorized. Only managers can finish repairs.');
        }

        $action = new FinishRepairAction();
        $action->execute($equipment);

        return redirect()
            ->route('equipment.show', $equipment)
            ->with('success', 'Equipment repair finished successfully.');
    }
}
