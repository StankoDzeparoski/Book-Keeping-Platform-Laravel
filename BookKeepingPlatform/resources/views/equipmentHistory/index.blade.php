<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Equipment History') }}
            </h2>
            @if(auth()->check() && auth()->user()->isManager())
                <a href="{{ route('equipmentHistory.create') }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">{{ __('+ New History') }}</a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">{{ session('success') }}</div>
            @endif

            <div class="mb-6">
                <form method="GET" action="{{ route('equipmentHistory.index') }}" class="flex gap-2">
                    <input type="text" name="search" placeholder="Search by equipment brand..."
                           value="{{ request('search') }}"
                           class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 dark:bg-gray-800 dark:text-white">
                    <button type="submit" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition">
                        {{ __('Search') }}
                    </button>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-100 dark:bg-gray-700 border-b dark:border-gray-600">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">{{ __('Equipment') }}</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">{{ __('Users') }}</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">{{ __('Loan Dates') }}</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">{{ __('Expiration Dates') }}</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">{{ __('Actions') }}</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-600">
                        @forelse($equipmentHistories as $history)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200">{{ $history->equipment->brand }} {{ $history->equipment->model }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ implode(', ', $history->user_ids ?? []) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ implode(', ', $history->loan_date ?? []) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ implode(', ', $history->loan_expire_date ?? []) }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex gap-2">
                                        <a href="{{ route('equipmentHistory.show', $history) }}"
                                           class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">{{ __('View') }}</a>
                                        @if(auth()->check() && auth()->user()->isManager())
                                            <a href="{{ route('equipmentHistory.edit', $history) }}"
                                               class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300">{{ __('Edit') }}</a>
                                            <form action="{{ route('equipmentHistory.destroy', $history) }}" method="POST"
                                                  class="inline" onsubmit="return confirm('Are you sure?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">{{ __('Delete') }}</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">{{ __('No equipment history found') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">{{ $equipmentHistories->links() }}</div>
        </div>
    </div>
</x-app-layout>

