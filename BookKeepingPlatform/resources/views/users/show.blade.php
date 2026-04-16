@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="mb-6 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900">User Details</h1>
                <div class="flex gap-2">
                    @if(auth()->check() && auth()->user()->isManager())
                        <a href="{{ route('users.edit', $user) }}"
                           class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">Edit</a>
                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline"
                              onsubmit="return confirm('Are you sure?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
                                Delete
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Personal Information</h2>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-gray-600 font-semibold">First Name</dt>
                            <dd class="text-gray-900 text-lg">{{ $user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-600 font-semibold">Surname</dt>
                            <dd class="text-gray-900 text-lg">{{ $user->surname }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-600 font-semibold">Date of Birth</dt>
                            <dd class="text-gray-900">{{ $user->dob }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-600 font-semibold">Email</dt>
                            <dd class="text-gray-900">{{ $user->email }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Account Information</h2>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-gray-600 font-semibold">Role</dt>
                            <dd>
                            <span
                                class="px-3 py-1 rounded-full text-sm font-semibold {{ $user->role === 'Manager' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $user->role }}
                            </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-600 font-semibold">Equipment Assigned</dt>
                            <dd class="text-gray-900 text-lg">{{ $user->equipment->count() }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-600 font-semibold">Equipment Histories</dt>
                            <dd class="text-gray-900">{{ $user->equipmentHistories->count() }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-600 font-semibold">Joined At</dt>
                            <dd class="text-gray-900">{{ $user->created_at->format('Y-m-d H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            @if($user->equipment->count() > 0)
                <div class="mt-6 bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Assigned Equipment ({{ $user->equipment->count() }}
                        )</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-semibold">Brand</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold">Model</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold">Status</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold">Loan Date</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y">
                            @foreach($user->equipment as $eq)
                                <tr>
                                    <td class="px-4 py-2 text-sm">{{ $eq->brand }}</td>
                                    <td class="px-4 py-2 text-sm">{{ $eq->model }}</td>
                                    <td class="px-4 py-2 text-sm"><span
                                            class="px-2 py-1 rounded text-xs font-semibold bg-purple-100 text-purple-800">{{ $eq->status?->value }}</span>
                                    </td>
                                    <td class="px-4 py-2 text-sm">{{ $eq->loan_date?->format('Y-m-d') ?? '-' }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <div class="mt-6">
                <a href="{{ route('users.index') }}"
                   class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition">Back to Users</a>
            </div>
        </div>
    </div>
@endsection

