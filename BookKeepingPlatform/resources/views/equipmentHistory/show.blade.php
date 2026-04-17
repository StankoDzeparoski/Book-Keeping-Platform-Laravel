<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Equipment History Details') }}
            </h2>
            <div class="flex gap-2">
                @if(auth()->check() && auth()->user()->isManager())
                    <a href="{{ route('equipmentHistory.edit', $equipmentHistory) }}"
                       class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">{{ __('Edit') }}</a>
                    <form action="{{ route('equipmentHistory.destroy', $equipmentHistory) }}" method="POST"
                          class="inline" onsubmit="return confirm('Are you sure?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
                                Delete
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-200 mb-4">{{ __('Equipment') }}</h2>
                <p class="text-gray-900 dark:text-gray-200 text-lg">{{ $equipmentHistory->equipment->brand }} {{ $equipmentHistory->equipment->model }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-200 mb-4">{{ __('Users') }}</h2>
                <div class="space-y-2">
                    @foreach($equipmentHistory->user_ids ?? [] as $userId)
                        <span class="inline-block px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full text-sm font-semibold">{{ __('User ID:') }} {{ $userId }}</span>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-200 mb-4">{{ __('Loan Dates') }}</h2>
                    <ul class="space-y-2">
                        @foreach($equipmentHistory->loan_date ?? [] as $date)
                            <li class="text-gray-900 dark:text-gray-200 bg-green-50 dark:bg-green-900 p-2 rounded">{{ $date }}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-200 mb-4">{{ __('Expiration Dates') }}</h2>
                    <ul class="space-y-2">
                        @foreach($equipmentHistory->loan_expire_date ?? [] as $date)
                            <li class="text-gray-900 dark:text-gray-200 bg-red-50 dark:bg-red-900 p-2 rounded">{{ $date }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-200 mb-4">{{ __('Additional Information') }}</h2>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-gray-600 dark:text-gray-400 font-semibold">{{ __('Created At') }}</dt>
                        <dd class="text-gray-900 dark:text-gray-200">{{ $equipmentHistory->created_at->format('Y-m-d H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-600 dark:text-gray-400 font-semibold">{{ __('Updated At') }}</dt>
                        <dd class="text-gray-900 dark:text-gray-200">{{ $equipmentHistory->updated_at->format('Y-m-d H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <div class="mt-6">
                <a href="{{ route('equipmentHistory.index') }}"
                   class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition">{{ __('Back to History') }}</a>
            </div>
        </div>
    </div>
</x-app-layout>

