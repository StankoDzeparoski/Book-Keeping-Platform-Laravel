<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Users Management') }}
            </h2>
            @if(auth()->check() && auth()->user()->isManager())
                <a href="{{ route('users.create') }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    {{ __('+ New User') }}
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-6">
                <form method="GET" action="{{ route('users.index') }}" class="flex gap-2">
                    <input type="text" name="search" placeholder="Search by name, surname, or email..."
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
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">{{ __('Name') }}</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">{{ __('Email') }}</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">{{ __('DOB') }}</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">{{ __('Role') }}</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">{{ __('Equipment Assigned') }}</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">{{ __('Actions') }}</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-600">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200">{{ $user->name }} {{ $user->surname }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $user->email }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $user->dob }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $user->role === 'Manager' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200">{{ $user->equipment->count() }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex gap-2">
                                        <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">{{ __('View') }}</a>
                                        @if(auth()->check() && auth()->user()->isManager())
                                            <a href="{{ route('users.edit', $user) }}"
                                               class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300">{{ __('Edit') }}</a>
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Are you sure?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">{{ __('Delete') }}</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">{{ __('No users found') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">{{ $users->links() }}</div>
        </div>
    </div>
</x-app-layout>

