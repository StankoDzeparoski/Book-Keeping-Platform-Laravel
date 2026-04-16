@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="mb-6 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900">Maintenance Record Details</h1>
                <div class="flex gap-2">
                    @if(auth()->check() && auth()->user()->isManager())
                        <a href="{{ route('maintenanceRecord.edit', $maintenanceRecord) }}"
                           class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">Edit</a>
                        <form action="{{ route('maintenanceRecord.destroy', $maintenanceRecord) }}" method="POST"
                              class="inline" onsubmit="return confirm('Are you sure?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
                                Delete
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Equipment</h2>
                <p class="text-gray-900 text-lg">{{ $maintenanceRecord->equipment->brand }} {{ $maintenanceRecord->equipment->model }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Maintenance Cost</h2>
                <p class="text-gray-900 text-2xl font-bold">${{ number_format($maintenanceRecord->cost) }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Descriptions</h2>
                <ul class="list-disc list-inside space-y-2">
                    @foreach($maintenanceRecord->description ?? [] as $desc)
                        <li class="text-gray-900">{{ $desc }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Maintenance Dates</h2>
                <ul class="space-y-2">
                    @foreach($maintenanceRecord->maintenance_date ?? [] as $date)
                        <li class="text-gray-900 bg-blue-50 p-2 rounded">{{ $date }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="mt-6">
                <a href="{{ route('maintenanceRecord.index') }}"
                   class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition">Back to Records</a>
            </div>
        </div>
    </div>
@endsection

