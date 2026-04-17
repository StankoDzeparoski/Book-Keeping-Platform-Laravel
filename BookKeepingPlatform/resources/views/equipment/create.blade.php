<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Equipment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <h3 class="font-bold mb-2">{{ __('Please fix the following errors:') }}</h3>
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8">
                <form action="{{ route('equipment.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('Brand') }} *</label>
                        <input type="text" name="brand"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('brand') border-red-500 @enderror"
                               value="{{ old('brand') }}" placeholder="e.g., Dell, HP, Lenovo">
                        @error('brand') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('Model') }} *</label>
                        <input type="text" name="model"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('model') border-red-500 @enderror"
                               value="{{ old('model') }}" placeholder="e.g., Latitude 5420">
                        @error('model') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('Category') }} *</label>
                        <select name="category"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('category') border-red-500 @enderror">
                            <option value="">{{ __('Select a category') }}</option>
                            <option value="Laptop" {{ old('category') == 'Laptop' ? 'selected' : '' }}>{{ __('Laptop') }}</option>
                            <option value="Computer" {{ old('category') == 'Computer' ? 'selected' : '' }}>{{ __('Computer') }}</option>
                            <option value="Peripherals" {{ old('category') == 'Peripherals' ? 'selected' : '' }}>{{ __('Peripherals') }}</option>
                            <option value="Ergonomics" {{ old('category') == 'Ergonomics' ? 'selected' : '' }}>{{ __('Ergonomics') }}</option>
                        </select>
                        @error('category') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('Cost ($)') }} *</label>
                        <input type="number" name="cost"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('cost') border-red-500 @enderror"
                               value="{{ old('cost') }}" placeholder="e.g., 1200" min="0">
                        @error('cost') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('Condition') }} *</label>
                        <select name="condition"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('condition') border-red-500 @enderror">
                            <option value="">{{ __('Select condition') }}</option>
                            <option value="new" {{ old('condition') == 'new' ? 'selected' : '' }}>{{ __('New') }}</option>
                            <option value="used" {{ old('condition') == 'used' ? 'selected' : '' }}>{{ __('Used') }}</option>
                            <option value="broken" {{ old('condition') == 'broken' ? 'selected' : '' }}>{{ __('Broken') }}</option>
                        </select>
                        @error('condition') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('Acquisition Date') }} *</label>
                        <input type="date" name="acquisition_date"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('acquisition_date') border-red-500 @enderror"
                               value="{{ old('acquisition_date') }}">
                        @error('acquisition_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('Storage Location') }} *</label>
                        <input type="text" name="storage_location"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('storage_location') border-red-500 @enderror"
                               value="{{ old('storage_location') }}" placeholder="e.g., Warehouse A">
                        @error('storage_location') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-4">
                        <button type="submit"
                                class="flex-1 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                            {{ __('Create Equipment') }}
                        </button>
                        <a href="{{ route('equipment.index') }}"
                           class="flex-1 bg-gray-300 dark:bg-gray-600 text-gray-900 dark:text-gray-200 px-6 py-2 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-700 transition font-semibold text-center">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

