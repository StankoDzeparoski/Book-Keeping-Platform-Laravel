@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="mb-6 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900">Equipment History Details</h1>
                <div class="flex gap-2">
                    @if(auth()->check() && auth()->user()->isManager())
                        <a href="{{ route('equipmentHistory.edit', $equipmentHistory) }}"
                           class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">Edit</a>
                        <form action="{{ route('equipmentHistory.destroy', $equipmentHistory) }}" method="POST"
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
                <p class="text-gray-900 text-lg">{{ $equipmentHistory->equipment->brand }} {{ $equipmentHistory->equipment->model }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Users</h2>
                <div class="space-y-2">
                    @foreach($equipmentHistory->user_ids ?? [] as $userId)
                        <span
                            class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">User ID: {{ $userId }}</span>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Loan Dates</h2>
                    <ul class="space-y-2">
                        @foreach($equipmentHistory->loan_date ?? [] as $date)
                            <li class="text-gray-900 bg-green-50 p-2 rounded">{{ $date }}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Expiration Dates</h2>
                    <ul class="space-y-2">
                        @foreach($equipmentHistory->loan_expire_date ?? [] as $date)
                            <li class="text-gray-900 bg-red-50 p-2 rounded">{{ $date }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Additional Information</h2>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-gray-600 font-semibold">Created At</dt>
                        <dd class="text-gray-900">{{ $equipmentHistory->created_at->format('Y-m-d H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-600 font-semibold">Updated At</dt>
                        <dd class="text-gray-900">{{ $equipmentHistory->updated_at->format('Y-m-d H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <div class="mt-6">
                <a href="{{ route('equipmentHistory.index') }}"
                   class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition">Back to History</a>
            </div>
        </div>
    </div>
@endsection

