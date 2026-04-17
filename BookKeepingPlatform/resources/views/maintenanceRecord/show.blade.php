<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Maintenance Record Details') }}
            </h2>
            <div class="flex gap-2">
                @if(auth()->check() && auth()->user()->isManager())
                    <a href="{{ route('maintenanceRecord.edit', $maintenanceRecord) }}"
                       class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">{{ __('Edit') }}</a>
                    <form action="{{ route('maintenanceRecord.destroy', $maintenanceRecord) }}" method="POST"
                          class="inline" onsubmit="return confirm('Are you sure?')">
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
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-200 mb-4">{{ __('Equipment') }}</h2>
                <p class="text-gray-900 dark:text-gray-200 text-lg">{{ $maintenanceRecord->equipment->brand }} {{ $maintenanceRecord->equipment->model }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-200 mb-4">{{ __('Maintenance Cost') }}</h2>
                <p class="text-gray-900 dark:text-gray-200 text-2xl font-bold">${{ number_format($maintenanceRecord->cost) }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-200 mb-4">{{ __('Descriptions') }}</h2>
                <ul class="list-disc list-inside space-y-2">
                    @foreach($maintenanceRecord->description ?? [] as $desc)
                        <li class="text-gray-900 dark:text-gray-200">{{ $desc }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-200 mb-4">{{ __('Maintenance Dates') }}</h2>
                <ul class="space-y-2">
                    @foreach($maintenanceRecord->maintenance_date ?? [] as $date)
                        <li class="text-gray-900 dark:text-gray-200 bg-blue-50 dark:bg-blue-900 p-2 rounded">{{ $date }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="mt-6">
                <a href="{{ route('maintenanceRecord.index') }}"
                   class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition">{{ __('Back to Records') }}</a>
            </div>
        </div>
    </div>
</x-app-layout>

