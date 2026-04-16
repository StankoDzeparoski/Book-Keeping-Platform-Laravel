@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900">Equipment History</h1>
            @if(auth()->check() && auth()->user()->isManager())
                <a href="{{ route('equipmentHistory.create') }}"
                   class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">+ New History</a>
            @endif
        </div>

        @if(session('success'))
            <div
                class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">{{ session('success') }}</div>
        @endif

        <div class="mb-6">
            <form method="GET" action="{{ route('equipmentHistory.index') }}" class="flex gap-2">
                <input type="text" name="search" placeholder="Search by equipment brand..."
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
                    <th class="px-6 py-3 text-left text-sm font-semibold">Equipment</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Users</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Loan Dates</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Expiration Dates</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y">
                @forelse($equipmentHistories as $history)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $history->equipment->brand }} {{ $history->equipment->model }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ implode(', ', $history->user_ids ?? []) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ implode(', ', $history->loan_date ?? []) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ implode(', ', $history->loan_expire_date ?? []) }}</td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex gap-2">
                                <a href="{{ route('equipmentHistory.show', $history) }}"
                                   class="text-blue-600 hover:text-blue-800">View</a>
                                @if(auth()->check() && auth()->user()->isManager())
                                    <a href="{{ route('equipmentHistory.edit', $history) }}"
                                       class="text-green-600 hover:text-green-800">Edit</a>
                                    <form action="{{ route('equipmentHistory.destroy', $history) }}" method="POST"
                                          class="inline" onsubmit="return confirm('Are you sure?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No equipment history found</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $equipmentHistories->links() }}</div>
    </div>
@endsection

