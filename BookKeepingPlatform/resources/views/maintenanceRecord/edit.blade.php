<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Maintenance Record') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <h3 class="font-bold mb-2">Please fix the following errors:</h3>
                    <ul class="list-disc list-inside">@foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach</ul>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow p-8">
                <form action="{{ route('maintenanceRecord.update', $maintenanceRecord) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Equipment *</label>
                        <select name="equipment_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('equipment_id') border-red-500 @enderror">
                            @foreach(\App\Models\Equipment::all() as $eq)
                                <option
                                    value="{{ $eq->id }}" {{ old('equipment_id', $maintenanceRecord->equipment_id) == $eq->id ? 'selected' : '' }}>{{ $eq->brand }} {{ $eq->model }}</option>
                            @endforeach
                        </select>
                        @error('equipment_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Cost ($) *</label>
                        <input type="number" name="cost"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('cost') border-red-500 @enderror"
                               value="{{ old('cost', $maintenanceRecord->cost) }}" min="0">
                        @error('cost') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Descriptions *</label>
                        <div id="descriptions">
                            @foreach(old('description', $maintenanceRecord->description ?? []) as $desc)
                                <input type="text" name="description[]"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-2"
                                       value="{{ $desc }}">
                            @endforeach
                        </div>
                        <button type="button" onclick="addDescription()"
                                class="text-blue-600 hover:text-blue-800 text-sm font-semibold">+ Add Description
                        </button>
                        @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">Maintenance Dates *</label>
                        <div id="dates">
                            @foreach(old('maintenance_date', $maintenanceRecord->maintenance_date ?? []) as $date)
                                <input type="date" name="maintenance_date[]"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-2"
                                       value="{{ $date }}">
                            @endforeach
                        </div>
                        <button type="button" onclick="addDate()"
                                class="text-blue-600 hover:text-blue-800 text-sm font-semibold">+ Add Date
                        </button>
                        @error('maintenance_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-4">
                        <button type="submit"
                                class="flex-1 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                            Update Record
                        </button>
                        <a href="{{ route('maintenanceRecord.show', $maintenanceRecord) }}"
                           class="flex-1 bg-gray-300 text-gray-900 px-6 py-2 rounded-lg hover:bg-gray-400 transition font-semibold text-center">Cancel</a>
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

