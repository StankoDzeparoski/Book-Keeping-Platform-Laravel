<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Equipment Details') }}
            </h2>
            <div class="flex gap-2">
                @if(auth()->check() && auth()->user()->isManager())
                    <!-- Edit button -->
                    <a href="{{ route('equipment.edit', $equipment) }}"
                       class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                        {{ __('Edit') }}
                    </a>

                    <!-- Repair button - only if condition is broken -->
                    @if($equipment->condition?->value === 'broken' && $equipment->status?->value !== 'Repair')
                        <button onclick="openRepairModal()"
                                class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition">
                            {{ __('Repair') }}
                        </button>
                    @endif

                    <!-- Finish Repair button - only if status is Repair -->
                    @if($equipment->status?->value === 'Repair')
                        <form action="{{ route('equipment.finishRepair', $equipment) }}" method="POST" class="inline"
                              onsubmit="return confirm('Are you sure you want to finish this repair?')">
                            @csrf
                            <button type="submit"
                                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                                {{ __('Finish Repair') }}
                            </button>
                        </form>
                    @endif

                    <!-- Delete button -->
                    <form action="{{ route('equipment.destroy', $equipment) }}" method="POST" class="inline"
                          onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                            {{ __('Delete') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Equipment Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-200 mb-4">{{ __('Equipment Information') }}</h2>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-gray-600 dark:text-gray-400 font-semibold">{{ __('Brand') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-200 text-lg">{{ $equipment->brand }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-600 dark:text-gray-400 font-semibold">{{ __('Model') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-200 text-lg">{{ $equipment->model }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-600 dark:text-gray-400 font-semibold">{{ __('Category') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-200">{{ $equipment->category?->value ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-600 dark:text-gray-400 font-semibold">{{ __('Cost') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-200 text-lg">${{ number_format($equipment->cost) }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-600 dark:text-gray-400 font-semibold">{{ __('Condition') }}</dt>
                            <dd>
                            <span class="px-3 py-1 rounded-full text-sm font-semibold
                                {{ $equipment->condition?->value === 'new' ? 'bg-green-100 text-green-800' : ($equipment->condition?->value === 'used' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $equipment->condition?->value ?? 'N/A' }}
                            </span>
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Status & Assignment -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-200 mb-4">{{ __('Status & Assignment') }}</h2>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-gray-600 dark:text-gray-400 font-semibold">{{ __('Status') }}</dt>
                            <dd>
                            <span class="px-3 py-1 rounded-full text-sm font-semibold
                                {{ $equipment->status?->value === 'Available' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ $equipment->status?->value ?? 'N/A' }}
                            </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-600 dark:text-gray-400 font-semibold">{{ __('Assigned To') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-200">
                                {{ $equipment->user ? $equipment->user->name . ' ' . $equipment->user->surname : '-' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-600 dark:text-gray-400 font-semibold">{{ __('Loan Date') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-200">
                                {{ $equipment->loan_date ? $equipment->loan_date->format('Y-m-d') : '-' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-600 dark:text-gray-400 font-semibold">{{ __('Loan Expiration') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-200">
                                {{ $equipment->loan_expire_date ? $equipment->loan_expire_date->format('Y-m-d') : '-' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-200 mb-4">{{ __('Additional Information') }}</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-gray-600 dark:text-gray-400 font-semibold">{{ __('Storage Location') }}</dt>
                        <dd class="text-gray-900 dark:text-gray-200">{{ $equipment->storage_location }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-600 dark:text-gray-400 font-semibold">{{ __('Acquisition Date') }}</dt>
                        <dd class="text-gray-900 dark:text-gray-200">{{ $equipment->acquisition_date?->format('Y-m-d') ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-600 dark:text-gray-400 font-semibold">{{ __('Created At') }}</dt>
                        <dd class="text-gray-900 dark:text-gray-200">{{ $equipment->created_at->format('Y-m-d H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-600 dark:text-gray-400 font-semibold">{{ __('Updated At') }}</dt>
                        <dd class="text-gray-900 dark:text-gray-200">{{ $equipment->updated_at->format('Y-m-d H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Maintenance Records -->
            @if($equipment->maintenanceRecords->count() > 0)
                <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-200 mb-4">{{ __('Maintenance Records') }}
                        ({{ $equipment->maintenanceRecords->count() }})</h2>
                    <div class="space-y-4">
                        @foreach($equipment->maintenanceRecords as $record)
                            <div class="border-l-4 border-blue-500 pl-4 py-2">
                                <p class="text-gray-900 dark:text-gray-200 font-semibold">{{ __('Cost:') }} ${{ number_format($record->cost) }}</p>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">
                                    {{ __('Dates:') }} {{ implode(', ', $record->maintenance_date ?? []) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- History -->
            @if($equipment->history->count() > 0)
                <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-200 mb-4">{{ __('Equipment History') }}</h2>
                    <div class="space-y-4">
                        @foreach($equipment->history as $hist)
                            <div class="border-l-4 border-purple-500 pl-4 py-2">
                                <p class="text-gray-900 dark:text-gray-200">{{ __('Users:') }} {{ implode(', ', $hist->user_ids ?? []) }}</p>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">{{ __('Loan Dates:') }} {{ implode(', ', $hist->loan_date ?? []) }}</p>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">
                                    {{ __('Expiration:') }} {{ implode(', ', $hist->loan_expire_date ?? []) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mt-6">
                <a href="{{ route('equipment.index') }}"
                   class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition">
                    {{ __('Back to Equipment') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Repair Modal -->
    <div id="repairModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200">{{ __('Log Equipment Repair') }}</h3>

            <form action="{{ route('equipment.repair', $equipment) }}" method="POST" class="mt-4 space-y-4">
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
        function openRepairModal() {
            document.getElementById('repairModal').classList.remove('hidden');
        }

        function closeRepairModal() {
            document.getElementById('repairModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('repairModal');
            if (event.target == modal) {
                modal.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>

