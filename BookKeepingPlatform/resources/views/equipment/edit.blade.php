@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Edit Equipment</h1>

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <h3 class="font-bold mb-2">Please fix the following errors:</h3>
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow p-8">
                <form action="{{ route('equipment.update', $equipment) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Brand *</label>
                        <input type="text" name="brand"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('brand') border-red-500 @enderror"
                               value="{{ old('brand', $equipment->brand) }}">
                        @error('brand') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Model *</label>
                        <input type="text" name="model"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('model') border-red-500 @enderror"
                               value="{{ old('model', $equipment->model) }}">
                        @error('model') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Category *</label>
                        <select name="category"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('category') border-red-500 @enderror">
                            <option
                                value="Laptop" {{ old('category', $equipment->category?->value) == 'Laptop' ? 'selected' : '' }}>
                                Laptop
                            </option>
                            <option
                                value="Computer" {{ old('category', $equipment->category?->value) == 'Computer' ? 'selected' : '' }}>
                                Computer
                            </option>
                            <option
                                value="Peripherals" {{ old('category', $equipment->category?->value) == 'Peripherals' ? 'selected' : '' }}>
                                Peripherals
                            </option>
                            <option
                                value="Ergonomics" {{ old('category', $equipment->category?->value) == 'Ergonomics' ? 'selected' : '' }}>
                                Ergonomics
                            </option>
                        </select>
                        @error('category') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Cost ($) *</label>
                        <input type="number" name="cost"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('cost') border-red-500 @enderror"
                               value="{{ old('cost', $equipment->cost) }}" min="0">
                        @error('cost') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Condition *</label>
                        <select name="condition"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('condition') border-red-500 @enderror">
                            <option
                                value="new" {{ old('condition', $equipment->condition?->value) == 'new' ? 'selected' : '' }}>
                                New
                            </option>
                            <option
                                value="used" {{ old('condition', $equipment->condition?->value) == 'used' ? 'selected' : '' }}>
                                Used
                            </option>
                            <option
                                value="broken" {{ old('condition', $equipment->condition?->value) == 'broken' ? 'selected' : '' }}>
                                Broken
                            </option>
                        </select>
                        @error('condition') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Acquisition Date *</label>
                        <input type="date" name="acquisition_date"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('acquisition_date') border-red-500 @enderror"
                               value="{{ old('acquisition_date', $equipment->acquisition_date?->format('Y-m-d')) }}">
                        @error('acquisition_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">Storage Location *</label>
                        <input type="text" name="storage_location"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('storage_location') border-red-500 @enderror"
                               value="{{ old('storage_location', $equipment->storage_location) }}">
                        @error('storage_location') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-4">
                        <button type="submit"
                                class="flex-1 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                            Update Equipment
                        </button>
                        <a href="{{ route('equipment.show', $equipment) }}"
                           class="flex-1 bg-gray-300 text-gray-900 px-6 py-2 rounded-lg hover:bg-gray-400 transition font-semibold text-center">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

