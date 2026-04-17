<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Maintenance Record') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <h3 class="font-bold mb-2">{{ __('Please fix the following errors:') }}</h3>
                    <ul class="list-disc list-inside">@foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach</ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8">
                <form action="{{ route('maintenanceRecord.store') }}" method="POST" id="maintenanceForm">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('Equipment') }} *</label>
                        <select name="equipment_id"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white @error('equipment_id') border-red-500 @enderror">
                            <option value="">{{ __('Select Equipment') }}</option>
                            @foreach(\App\Models\Equipment::all() as $eq)
                                <option
                                    value="{{ $eq->id }}" {{ old('equipment_id') == $eq->id ? 'selected' : '' }}>{{ $eq->brand }} {{ $eq->model }}</option>
                            @endforeach
                        </select>
                        @error('equipment_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('Cost ($)') }} *</label>
                        <input type="number" name="cost"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white @error('cost') border-red-500 @enderror"
                               value="{{ old('cost') }}" min="0">
                        @error('cost') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div id="descriptionContainer" class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('Descriptions') }} *</label>
                        <div id="descriptions">
                            <input type="text" name="description[]"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white mb-2"
                                   placeholder="Enter description" value="{{ old('description.0') }}">
                        </div>
                        <button type="button" onclick="addDescription()"
                                class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-semibold">{{ __('+Add Description') }}
                        </button>
                        @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div id="dateContainer" class="mb-6">
                        <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('Maintenance Dates') }} *</label>
                        <div id="dates">
                            <input type="date" name="maintenance_date[]"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white mb-2"
                                   value="{{ old('maintenance_date.0') }}">
                        </div>
                        <button type="button" onclick="addDate()"
                                class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-semibold">{{ __('+Add Date') }}
                        </button>
                        @error('maintenance_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-4">
                        <button type="submit"
                                class="flex-1 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                            {{ __('Create Maintenance Record') }}
                        </button>
                        <a href="{{ route('maintenanceRecord.index') }}"
                           class="flex-1 bg-gray-300 dark:bg-gray-600 text-gray-900 dark:text-gray-200 px-6 py-2 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-700 transition font-semibold text-center">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function addDescription() {
            const container = document.getElementById('descriptions');
            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'description[]';
            input.className = 'w-full px-4 py-2 border border-gray-300 rounded-lg mb-2';
            input.placeholder = 'Enter description';
            container.appendChild(input);
        }

        function addDate() {
            const container = document.getElementById('dates');
            const input = document.createElement('input');
            input.type = 'date';
            input.name = 'maintenance_date[]';
            input.className = 'w-full px-4 py-2 border border-gray-300 rounded-lg mb-2';
            container.appendChild(input);
        }
    </script>
        </div>
    </div>
</x-app-layout>
