<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Equipment History') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Edit Equipment History</h1>

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <h3 class="font-bold mb-2">Please fix the following errors:</h3>
                    <ul class="list-disc list-inside">@foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach</ul>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow p-8">
                <form action="{{ route('equipmentHistory.update', $equipmentHistory) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Equipment *</label>
                        <select name="equipment_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('equipment_id') border-red-500 @enderror">
                            @foreach(\App\Models\Equipment::all() as $eq)
                                <option
                                    value="{{ $eq->id }}" {{ old('equipment_id', $equipmentHistory->equipment_id) == $eq->id ? 'selected' : '' }}>{{ $eq->brand }} {{ $eq->model }}</option>
                            @endforeach
                        </select>
                        @error('equipment_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Users *</label>
                        <div id="users">
                            @foreach(old('user_ids', $equipmentHistory->user_ids ?? []) as $userId)
                                <select name="user_ids[]"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-2">
                                    @foreach(\App\Models\User::all() as $user)
                                        <option
                                            value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>{{ $user->name }} {{ $user->surname }}</option>
                                    @endforeach
                                </select>
                            @endforeach
                        </div>
                        <button type="button" onclick="addUser()"
                                class="text-blue-600 hover:text-blue-800 text-sm font-semibold">+ Add User
                        </button>
                        @error('user_ids') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Loan Dates *</label>
                        <div id="loanDates">
                            @foreach(old('loan_date', $equipmentHistory->loan_date ?? []) as $date)
                                <input type="date" name="loan_date[]"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-2"
                                       value="{{ $date }}">
                            @endforeach
                        </div>
                        <button type="button" onclick="addLoanDate()"
                                class="text-blue-600 hover:text-blue-800 text-sm font-semibold">+ Add Loan Date
                        </button>
                        @error('loan_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">Expiration Dates *</label>
                        <div id="expireDates">
                            @foreach(old('loan_expire_date', $equipmentHistory->loan_expire_date ?? []) as $date)
                                <input type="date" name="loan_expire_date[]"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-2"
                                       value="{{ $date }}">
                            @endforeach
                        </div>
                        <button type="button" onclick="addExpireDate()"
                                class="text-blue-600 hover:text-blue-800 text-sm font-semibold">+ Add Expiration Date
                        </button>
                        @error('loan_expire_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-4">
                        <button type="submit"
                                class="flex-1 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                            Update History
                        </button>
                        <a href="{{ route('equipmentHistory.show', $equipmentHistory) }}"
                           class="flex-1 bg-gray-300 text-gray-900 px-6 py-2 rounded-lg hover:bg-gray-400 transition font-semibold text-center">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function addUser() {
            const container = document.getElementById('users');
            const select = document.createElement('select');
            select.name = 'user_ids[]';
            select.className = 'w-full px-4 py-2 border border-gray-300 rounded-lg mb-2';
            select.innerHTML = document.querySelector('select[name="user_ids[]"]').innerHTML;
            container.appendChild(select);
        }

        function addLoanDate() {
            const container = document.getElementById('loanDates');
            const input = document.createElement('input');
            input.type = 'date';
            input.name = 'loan_date[]';
            input.className = 'w-full px-4 py-2 border border-gray-300 rounded-lg mb-2';
            container.appendChild(input);
        }

        function addExpireDate() {
            const container = document.getElementById('expireDates');
            const input = document.createElement('input');
            input.type = 'date';
            input.name = 'loan_expire_date[]';
            input.className = 'w-full px-4 py-2 border border-gray-300 rounded-lg mb-2';
            container.appendChild(input);
        }
    </script>
        </div>
    </div>
</x-app-layout>

