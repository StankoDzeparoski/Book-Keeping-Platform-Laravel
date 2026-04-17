<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|Factory|Application
    {
        $users = User::query()
            ->when($request->has('search'),
                fn($q) => $q->where('name', 'like', '%'.$request->get('search').'%')
                    ->orWhere('surname', 'like', '%'.$request->get('search').'%')
                    ->orWhere('email', 'like', '%'.$request->get('search').'%')
            )
            ->latest()
            ->paginate(10);

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|Factory|Application
    {
        // Only managers can create users
        if (!auth()->check() || !auth()->user()->isManager()) {
            abort(403, 'Unauthorized. Only managers can create users.');
        }

        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request): RedirectResponse
    {
        // Only managers can create users
        if (!auth()->check() || !auth()->user()->isManager()) {
            abort(403, 'Unauthorized. Only managers can create users.');
        }

        User::query()->create($request->validated());

        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): View|Factory|Application
    {
        $user->loadMissing('equipment');

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View|Factory|Application
    {
        // Only managers can edit users
        if (!auth()->check() || !auth()->user()->isManager()) {
            abort(403, 'Unauthorized. Only managers can edit users.');
        }

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        // Only managers can update users
        if (!auth()->check() || !auth()->user()->isManager()) {
            abort(403, 'Unauthorized. Only managers can update users.');
        }

        $user->update($request->validated());

        return redirect()
            ->route('users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Only managers can delete users
        if (!auth()->check() || !auth()->user()->isManager()) {
            abort(403, 'Unauthorized. Only managers can delete users.');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
