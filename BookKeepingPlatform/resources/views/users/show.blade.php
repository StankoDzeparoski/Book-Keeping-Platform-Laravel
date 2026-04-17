<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('User Details') }}
            </h2>
            <div class="flex gap-2">
                @if(auth()->check() && auth()->user()->isManager())
                    <a href="{{ route('users.edit', $user) }}"
                       class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">{{ __('Edit') }}</a>
                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline"
                          onsubmit="return confirm('Are you sure?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                            {{ __('Delete') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-200 mb-4">{{ __('Personal Information') }}</h2>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-gray-600 dark:text-gray-400 font-semibold">{{ __('First Name') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-200 text-lg">{{ $user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-600 dark:text-gray-400 font-semibold">{{ __('Surname') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-200 text-lg">{{ $user->surname }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-600 dark:text-gray-400 font-semibold">{{ __('Date of Birth') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-200">{{ $user->dob }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-600 dark:text-gray-400 font-semibold">{{ __('Email') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-200">{{ $user->email }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-200 mb-4">{{ __('Account Information') }}</h2>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-gray-600 dark:text-gray-400 font-semibold">{{ __('Role') }}</dt>
                            <dd>
                            <span
                                class="px-3 py-1 rounded-full text-sm font-semibold {{ $user->role === 'Manager' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $user->role }}
                            </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-600 dark:text-gray-400 font-semibold">{{ __('Equipment Assigned') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-200 text-lg">{{ $user->equipment->count() }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-600 dark:text-gray-400 font-semibold">{{ __('Joined At') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-200">{{ $user->created_at->format('Y-m-d H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            @if($user->equipment->count() > 0)
                <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-200 mb-4">{{ __('Assigned Equipment') }} ({{ $user->equipment->count() }})</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-100 dark:bg-gray-700 border-b dark:border-gray-600">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">{{ __('Brand') }}</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">{{ __('Model') }}</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">{{ __('Status') }}</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">{{ __('Loan Date') }}</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y dark:divide-gray-600">
                            @foreach($user->equipment as $eq)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-200">{{ $eq->brand }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-200">{{ $eq->model }}</td>
                                    <td class="px-4 py-2 text-sm"><span
                                            class="px-2 py-1 rounded text-xs font-semibold bg-purple-100 text-purple-800">{{ $eq->status?->value }}</span>
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-200">{{ $eq->loan_date?->format('Y-m-d') ?? '-' }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <div class="mt-6">
                <a href="{{ route('users.index') }}"
                   class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition">{{ __('Back to Users') }}</a>
            </div>
        </div>
    </div>
</x-app-layout>

