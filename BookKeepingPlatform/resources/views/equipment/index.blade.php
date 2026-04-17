<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Equipment Management') }}
            </h2>
            @if(auth()->check() && auth()->user()->isManager())
                <a href="{{ route('equipment.create') }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    {{ __('+ New Equipment') }}
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-6">
                <form method="GET" action="{{ route('equipment.index') }}" class="flex gap-2">
                    <input type="text" name="search" placeholder="Search by brand or model..."
                           value="{{ request('search') }}"
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    <button type="submit" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition">
                        {{ __('Search') }}
                    </button>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-100 dark:bg-gray-700 border-b dark:border-gray-600">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">{{ __('Brand') }}</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">{{ __('Model') }}</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">{{ __('Category') }}</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">{{ __('Cost') }}</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">{{ __('Condition') }}</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">{{ __('Assigned To') }}</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">{{ __('Actions') }}</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-600">
                        @forelse($equipment as $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200">{{ $item->brand }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200">{{ $item->model }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->category?->value ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200">${{ number_format($item->cost) }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        {{ $item->condition?->value === 'new' ? 'bg-green-100 text-green-800' : ($item->condition?->value === 'used' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $item->condition?->value ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        {{ $item->status?->value === 'Available' ? 'bg-blue-100 text-blue-800' : ($item->status?->value === 'Assigned' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ $item->status?->value ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $item->user ? $item->user->name . ' ' . $item->user->surname : '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex gap-2 flex-wrap">
                                        <a href="{{ route('equipment.show', $item) }}"
                                           class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">{{ __('View') }}</a>
                                        @if(auth()->check() && auth()->user()->isManager())
                                            <a href="{{ route('equipment.edit', $item) }}"
                                               class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300">{{ __('Edit') }}</a>
                                            <form action="{{ route('equipment.destroy', $item) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">{{ __('Delete') }}</button>
                                            </form>
                                            @if($item->condition?->value === 'broken' && $item->status?->value !== 'Repair')
                                                <button type="button" class="text-orange-600 hover:text-orange-800 dark:text-orange-400 dark:hover:text-orange-300"
                                                        onclick="openRepairModal({{ $item->id }})">{{ __('Repair') }}</button>
                                            @endif
                                            @if($item->status?->value === 'Repair')
                                                <form action="{{ route('equipment.finishRepair', $item) }}" method="POST" class="inline"
                                                      onsubmit="return confirm('Are you sure you want to finish this repair?')">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300">{{ __('Finish Repair') }}</button>
                                                </form>
                                            @endif
                                        @endif
                                        @if(auth()->check() && (auth()->user()->isManager() || auth()->user()->isEmployee()))
                                            @if($item->status?->value === 'Available')
                                                <button type="button" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300"
                                                        onclick="openLoanModal({{ $item->id }})">{{ __('Loan') }}
                                                </button>
                                            @elseif($item->status?->value === 'Assigned' && (auth()->user()->isManager() || $item->user_id === auth()->id()))
                                                <button type="button" class="text-orange-600 hover:text-orange-800 dark:text-orange-400 dark:hover:text-orange-300"
                                                        onclick="openReturnModal({{ $item->id }})">{{ __('Return') }}
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">{{ __('No equipment found') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                {{ $equipment->links() }}
            </div>
        </div>
    </div>

    <!-- Loan Equipment Modal -->
    <div id="loanModal" class="hidden fixed inset-0 bg-gray-600 dark:bg-gray-900 bg-opacity-50 dark:bg-opacity-75 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-200 mb-4">{{ __('Loan Equipment') }}</h3>
            <form id="loanForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('User') }} *</label>
                    <select name="user_id"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            required>
                        <option value="">{{ __('Select User') }}</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} {{ $user->surname }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('Loan Date') }} *</label>
                    <input type="date" name="loan_date"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                           required>
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('Loan Expiration Date') }} *</label>
                    <input type="date" name="loan_expire_date"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                           required>
                </div>
                <div class="flex gap-4">
                    <button type="submit"
                            class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition font-semibold">
                        {{ __('Loan Equipment') }}
                    </button>
                    <button type="button"
                            class="flex-1 bg-gray-300 dark:bg-gray-600 text-gray-900 dark:text-gray-200 px-4 py-2 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-700 transition font-semibold"
                            onclick="closeLoanModal()">{{ __('Cancel') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Return Equipment Modal -->
    <div id="returnModal" class="hidden fixed inset-0 bg-gray-600 dark:bg-gray-900 bg-opacity-50 dark:bg-opacity-75 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-200 mb-4">{{ __('Return Equipment') }}</h3>
            <form id="returnForm" method="POST">
                @csrf
                <div class="mb-6">
                    <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('Return Date') }} *</label>
                    <input type="date" name="return_date"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                           required>
                </div>
                <div class="flex gap-4">
                    <button type="submit"
                            class="flex-1 bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition font-semibold">
                        {{ __('Return Equipment') }}
                    </button>
                    <button type="button"
                            class="flex-1 bg-gray-300 dark:bg-gray-600 text-gray-900 dark:text-gray-200 px-4 py-2 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-700 transition font-semibold"
                            onclick="closeReturnModal()">{{ __('Cancel') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Repair Equipment Modal -->
    <div id="repairModal" class="hidden fixed inset-0 bg-gray-600 dark:bg-gray-900 bg-opacity-50 dark:bg-opacity-75 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200">{{ __('Log Equipment Repair') }}</h3>

            <form id="repairForm" method="POST" class="mt-4 space-y-4">
                @csrf

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('Repair Description') }}
                    </label>
                    <textarea id="description" name="description" required
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                              rows="3" placeholder="Describe the repair work done..."></textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Cost -->
                <div>
                    <label for="cost" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('Repair Cost ($)') }}
                    </label>
                    <input type="number" id="cost" name="cost" required min="1"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                           placeholder="0">
                    @error('cost')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Maintenance Date -->
                <div>
                    <label for="maintenance_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('Maintenance Date') }}
                    </label>
                    <input type="date" id="maintenance_date" name="maintenance_date" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('maintenance_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-2 mt-6">
                    <button type="submit"
                            class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">
                        {{ __('Log Repair') }}
                    </button>
                    <button type="button" onclick="closeRepairModal()"
                            class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500">
                        {{ __('Cancel') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openLoanModal(equipmentId) {
            document.getElementById('loanModal').classList.remove('hidden');
            document.getElementById('loanForm').action = `/equipment/${equipmentId}/loan`;
        }

        function closeLoanModal() {
            document.getElementById('loanModal').classList.add('hidden');
        }

        function openReturnModal(equipmentId) {
            document.getElementById('returnModal').classList.remove('hidden');
            document.getElementById('returnForm').action = `/equipment/${equipmentId}/return`;
        }

        function closeReturnModal() {
            document.getElementById('returnModal').classList.add('hidden');
        }

        function openRepairModal(equipmentId) {
            document.getElementById('repairModal').classList.remove('hidden');
            document.getElementById('repairForm').action = `/equipment/${equipmentId}/repair`;
            // Clear previous form values
            document.getElementById('description').value = '';
            document.getElementById('cost').value = '';
            document.getElementById('maintenance_date').value = '';
        }

        function closeRepairModal() {
            document.getElementById('repairModal').classList.add('hidden');
        }

        // Close modals when clicking outside
        document.getElementById('loanModal')?.addEventListener('click', function (e) {
            if (e.target === this) closeLoanModal();
        });

        document.getElementById('returnModal')?.addEventListener('click', function (e) {
            if (e.target === this) closeReturnModal();
        });

        document.getElementById('repairModal')?.addEventListener('click', function (e) {
            if (e.target === this) closeRepairModal();
        });
    </script>
</x-app-layout>

