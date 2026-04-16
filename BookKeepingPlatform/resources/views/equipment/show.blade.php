@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="mb-6 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900">Equipment Details</h1>
                <div class="flex gap-2">
                    @if(auth()->check() && auth()->user()->isManager())
                        <a href="{{ route('equipment.edit', $equipment) }}"
                           class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                            Edit
                        </a>
                        <form action="{{ route('equipment.destroy', $equipment) }}" method="POST" class="inline"
                              onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
                                Delete
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Equipment Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Equipment Information</h2>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-gray-600 font-semibold">Brand</dt>
                            <dd class="text-gray-900 text-lg">{{ $equipment->brand }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-600 font-semibold">Model</dt>
                            <dd class="text-gray-900 text-lg">{{ $equipment->model }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-600 font-semibold">Category</dt>
                            <dd class="text-gray-900">{{ $equipment->category?->value ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-600 font-semibold">Cost</dt>
                            <dd class="text-gray-900 text-lg">${{ number_format($equipment->cost) }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-600 font-semibold">Condition</dt>
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
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Status & Assignment</h2>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-gray-600 font-semibold">Status</dt>
                            <dd>
                            <span class="px-3 py-1 rounded-full text-sm font-semibold
                                {{ $equipment->status?->value === 'Available' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ $equipment->status?->value ?? 'N/A' }}
                            </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-600 font-semibold">Assigned To</dt>
                            <dd class="text-gray-900">
                                {{ $equipment->user ? $equipment->user->name . ' ' . $equipment->user->surname : '-' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-600 font-semibold">Loan Date</dt>
                            <dd class="text-gray-900">
                                {{ $equipment->loan_date ? $equipment->loan_date->format('Y-m-d') : '-' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-600 font-semibold">Loan Expiration</dt>
                            <dd class="text-gray-900">
                                {{ $equipment->loan_expire_date ? $equipment->loan_expire_date->format('Y-m-d') : '-' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="mt-6 bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Additional Information</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-gray-600 font-semibold">Storage Location</dt>
                        <dd class="text-gray-900">{{ $equipment->storage_location }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-600 font-semibold">Acquisition Date</dt>
                        <dd class="text-gray-900">{{ $equipment->acquisition_date?->format('Y-m-d') ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-600 font-semibold">Created At</dt>
                        <dd class="text-gray-900">{{ $equipment->created_at->format('Y-m-d H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-600 font-semibold">Updated At</dt>
                        <dd class="text-gray-900">{{ $equipment->updated_at->format('Y-m-d H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Maintenance Records -->
            @if($equipment->maintenanceRecords->count() > 0)
                <div class="mt-6 bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Maintenance Records
                        ({{ $equipment->maintenanceRecords->count() }})</h2>
                    <div class="space-y-4">
                        @foreach($equipment->maintenanceRecords as $record)
                            <div class="border-l-4 border-blue-500 pl-4 py-2">
                                <p class="text-gray-900 font-semibold">Cost: ${{ number_format($record->cost) }}</p>
                                <p class="text-gray-600 text-sm">
                                    Dates: {{ implode(', ', $record->maintenance_date ?? []) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- History -->
            @if($equipment->history->count() > 0)
                <div class="mt-6 bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Equipment History</h2>
                    <div class="space-y-4">
                        @foreach($equipment->history as $hist)
                            <div class="border-l-4 border-purple-500 pl-4 py-2">
                                <p class="text-gray-900">Users: {{ implode(', ', $hist->user_ids ?? []) }}</p>
                                <p class="text-gray-600 text-sm">Loan
                                    Dates: {{ implode(', ', $hist->loan_date ?? []) }}</p>
                                <p class="text-gray-600 text-sm">
                                    Expiration: {{ implode(', ', $hist->loan_expire_date ?? []) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mt-6">
                <a href="{{ route('equipment.index') }}"
                   class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition">
                    Back to Equipment
                </a>
            </div>
        </div>
    </div>
@endsection

