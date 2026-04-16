@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900">Equipment Management</h1>
            @if(auth()->check() && auth()->user()->isManager())
                <a href="{{ route('equipment.create') }}"
                   class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">+ New Equipment</a>
            @endif
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-6">
            <form method="GET" action="{{ route('equipment.index') }}" class="flex gap-2">
                <input type="text" name="search" placeholder="Search by brand or model..."
                       value="{{ request('search') }}"
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                <button type="submit" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition">
                    Search
                </button>
            </form>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full">
                <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Brand</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Model</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Category</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Cost</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Condition</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Assigned To</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y">
                @forelse($equipment as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $item->brand }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $item->model }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->category?->value ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">${{ number_format($item->cost) }}</td>
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
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $item->user ? $item->user->name . ' ' . $item->user->surname : '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex gap-2 flex-wrap">
                                <a href="{{ route('equipment.show', $item) }}"
                                   class="text-blue-600 hover:text-blue-800">View</a>
                                @if(auth()->check() && auth()->user()->isManager())
                                    <a href="{{ route('equipment.edit', $item) }}"
                                       class="text-green-600 hover:text-green-800">Edit</a>
                                    <form action="{{ route('equipment.destroy', $item) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                    </form>
                                @endif
                                @if(auth()->check() && (auth()->user()->isManager() || auth()->user()->isEmployee()))
                                    @if($item->status?->value === 'Available')
                                        <button type="button" class="text-indigo-600 hover:text-indigo-800"
                                                onclick="openLoanModal({{ $item->id }})">Loan
                                        </button>
                                    @else
                                        <button type="button" class="text-orange-600 hover:text-orange-800"
                                                onclick="openReturnModal({{ $item->id }})">Return
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">No equipment found</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $equipment->links() }}
        </div>
    </div>

    <!-- Loan Equipment Modal -->
    <div id="loanModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Loan Equipment</h3>
            <form id="loanForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">User *</label>
                    <select name="user_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                            required>
                        <option value="">Select User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} {{ $user->surname }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Loan Date *</label>
                    <input type="date" name="loan_date"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                           required>
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Loan Expiration Date *</label>
                    <input type="date" name="loan_expire_date"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                           required>
                </div>
                <div class="flex gap-4">
                    <button type="submit"
                            class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition font-semibold">
                        Loan Equipment
                    </button>
                    <button type="button"
                            class="flex-1 bg-gray-300 text-gray-900 px-4 py-2 rounded-lg hover:bg-gray-400 transition font-semibold"
                            onclick="closeLoanModal()">Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Return Equipment Modal -->
    <div id="returnModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Return Equipment</h3>
            <form id="returnForm" method="POST">
                @csrf
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Return Date *</label>
                    <input type="date" name="return_date"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                           required>
                </div>
                <div class="flex gap-4">
                    <button type="submit"
                            class="flex-1 bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition font-semibold">
                        Return Equipment
                    </button>
                    <button type="button"
                            class="flex-1 bg-gray-300 text-gray-900 px-4 py-2 rounded-lg hover:bg-gray-400 transition font-semibold"
                            onclick="closeReturnModal()">Cancel
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

        // Close modals when clicking outside
        document.getElementById('loanModal')?.addEventListener('click', function (e) {
            if (e.target === this) closeLoanModal();
        });

        document.getElementById('returnModal')?.addEventListener('click', function (e) {
            if (e.target === this) closeReturnModal();
        });
    </script>
@endsection

