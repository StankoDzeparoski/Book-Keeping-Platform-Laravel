<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create User') }}
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
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('First Name') }} *</label>
                        <input type="text" name="name"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('name') border-red-500 @enderror"
                               value="{{ old('name') }}">
                        @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('Surname') }} *</label>
                        <input type="text" name="surname"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('surname') border-red-500 @enderror"
                               value="{{ old('surname') }}">
                        @error('surname') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('Date of Birth') }} *</label>
                        <input type="text" name="dob"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('dob') border-red-500 @enderror"
                               placeholder="DD/MM/YYYY" value="{{ old('dob') }}">
                        @error('dob') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('Email') }} *</label>
                        <input type="email" name="email"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('email') border-red-500 @enderror"
                               value="{{ old('email') }}">
                        @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('Password') }} *</label>
                        <input type="password" name="password"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('password') border-red-500 @enderror">
                        @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('Confirm Password') }} *</label>
                        <input type="password" name="password_confirmation"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('Role') }} *</label>
                        <select name="role"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('role') border-red-500 @enderror">
                            <option value="">{{ __('Select Role') }}</option>
                            <option value="Manager" {{ old('role') == 'Manager' ? 'selected' : '' }}>{{ __('Manager') }}</option>
                            <option value="Employee" {{ old('role') == 'Employee' ? 'selected' : '' }}>{{ __('Employee') }}</option>
                        </select>
                        @error('role') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-4">
                        <button type="submit"
                                class="flex-1 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                            {{ __('Create User') }}
                        </button>
                        <a href="{{ route('users.index') }}"
                           class="flex-1 bg-gray-300 dark:bg-gray-600 text-gray-900 dark:text-gray-200 px-6 py-2 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-700 transition font-semibold text-center">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

