@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900">Users Management</h1>
            @if(auth()->check() && auth()->user()->isManager())
                <a href="{{ route('users.create') }}"
                   class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">+ New User</a>
            @endif
        </div>

        @if(session('success'))
            <div
                class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">{{ session('success') }}</div>
        @endif

        <div class="mb-6">
            <form method="GET" action="{{ route('users.index') }}" class="flex gap-2">
                <input type="text" name="search" placeholder="Search by name, surname, or email..."
                       value="{{ request('search') }}"
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                <button type="submit" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition">
                    Search
                </button>
            </form>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full">
                <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Name</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Email</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">DOB</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Role</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Equipment Assigned</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $user->name }} {{ $user->surname }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $user->dob }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold {{ $user->role === 'Manager' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $user->equipment->count() }}</td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex gap-2">
                                <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:text-blue-800">View</a>
                                @if(auth()->check() && auth()->user()->isManager())
                                    <a href="{{ route('users.edit', $user) }}"
                                       class="text-green-600 hover:text-green-800">Edit</a>
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Are you sure?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No users found</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $users->links() }}</div>
    </div>
@endsection

