<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Equipment History') }}
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
                <form action="{{ route('equipmentHistory.store') }}" method="POST" id="historyForm">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Equipment *</label>
                        <select name="equipment_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('equipment_id') border-red-500 @enderror">
                            <option value="">Select Equipment</option>
                            @foreach(\App\Models\Equipment::all() as $eq)
                                <option
                                    value="{{ $eq->id }}" {{ old('equipment_id') == $eq->id ? 'selected' : '' }}>{{ $eq->brand }} {{ $eq->model }}</option>
                            @endforeach
                        </select>
                        @error('equipment_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div id="userContainer" class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Users *</label>
                        <div id="users">
                            <select name="user_ids[]"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-2 @error('user_ids') border-red-500 @enderror">
                                <option value="">Select User</option>
                                @foreach(\App\Models\User::all() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} {{ $user->surname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" onclick="addUser()"
                                class="text-blue-600 hover:text-blue-800 text-sm font-semibold">+ Add User
                        </button>
                        @error('user_ids') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div id="loanDateContainer" class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Loan Dates *</label>
                        <div id="loanDates">
                            <input type="date" name="loan_date[]"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-2"
                                   value="{{ old('loan_date.0') }}">
                        </div>
                        <button type="button" onclick="addLoanDate()"
                                class="text-blue-600 hover:text-blue-800 text-sm font-semibold">+ Add Loan Date
                        </button>
                        @error('loan_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div id="expireDateContainer" class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">Expiration Dates *</label>
                        <div id="expireDates">
                            <input type="date" name="loan_expire_date[]"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-2"
                                   value="{{ old('loan_expire_date.0') }}">
                        </div>
                        <button type="button" onclick="addExpireDate()"
                                class="text-blue-600 hover:text-blue-800 text-sm font-semibold">+ Add Expiration Date
                        </button>
                        @error('loan_expire_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-4">
                        <button type="submit"
                                class="flex-1 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                            Create History
                        </button>
                        <a href="{{ route('equipmentHistory.index') }}"
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
            select.innerHTML = `<option value="">Select User</option>` + document.querySelector('select[name="user_ids[]"]').innerHTML.substring(document.querySelector('select[name="user_ids[]"]').innerHTML.indexOf('<option'));
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

