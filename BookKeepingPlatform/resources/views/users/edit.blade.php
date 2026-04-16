@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Edit User</h1>

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <h3 class="font-bold mb-2">Please fix the following errors:</h3>
                    <ul class="list-disc list-inside">@foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach</ul>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow p-8">
                <form action="{{ route('users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">First Name *</label>
                        <input type="text" name="name"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('name') border-red-500 @enderror"
                               value="{{ old('name', $user->name) }}">
                        @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Surname *</label>
                        <input type="text" name="surname"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('surname') border-red-500 @enderror"
                               value="{{ old('surname', $user->surname) }}">
                        @error('surname') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Date of Birth *</label>
                        <input type="text" name="dob"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('dob') border-red-500 @enderror"
                               placeholder="DD/MM/YYYY" value="{{ old('dob', $user->dob) }}">
                        @error('dob') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">Email *</label>
                        <input type="email" name="email"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('email') border-red-500 @enderror"
                               value="{{ old('email', $user->email) }}">
                        @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">Role *</label>
                        <select name="role"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('role') border-red-500 @enderror">
                            <option value="Manager" {{ old('role', $user->role) == 'Manager' ? 'selected' : '' }}>
                                Manager
                            </option>
                            <option value="Employee" {{ old('role', $user->role) == 'Employee' ? 'selected' : '' }}>
                                Employee
                            </option>
                        </select>
                        @error('role') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-4">
                        <button type="submit"
                                class="flex-1 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                            Update User
                        </button>
                        <a href="{{ route('users.show', $user) }}"
                           class="flex-1 bg-gray-300 text-gray-900 px-6 py-2 rounded-lg hover:bg-gray-400 transition font-semibold text-center">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

