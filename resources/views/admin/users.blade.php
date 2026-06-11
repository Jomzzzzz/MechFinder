@extends('layouts.admin')

@section('content')
    <div class="mb-8 text-[#F4B942] text-2xl heading-font">Users</div>

    <div class="bg-[#111113] border border-[#1E1E21] rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="border-[#1E1E21] border-b">
                <tr class="text-[#888] text-xs uppercase">
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">Role</th>
                    <th class="px-4 py-3 text-left">Shop</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="hover:bg-[#1A1A1D] border-[#1E1E21] border-b">
                        <td class="px-4 py-3">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-[#888]">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            @php
                                $roleClasses = 'bg-[#222] text-[#888]';
                                if ($user->role === 'admin') {
                                    $roleClasses = 'bg-purple-900 text-purple-300';
                                } elseif ($user->role === 'shop') {
                                    $roleClasses = 'bg-blue-900 text-blue-300';
                                }
                            @endphp
                            <span class="inline-flex px-2 py-1 rounded-full text-xs {{ $roleClasses }}">
                                {{ ucfirst($user->role ?? 'user') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-[#888]">{{ $user->shop_name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <form action="{{ route('admin.users.role', $user->id) }}" method="POST"
                                class="flex items-center gap-2">
                                @csrf
                                <select name="role"
                                    class="bg-[#1E1E21] px-2 py-1 border border-[#333] rounded text-[#EEE] text-xs">
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="shop" {{ $user->role === 'shop' ? 'selected' : '' }}>Shop</option>
                                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                                </select>
                                <button type="submit"
                                    class="bg-[#F4B942] px-2 py-1 rounded font-semibold text-[#0A0A0B] text-xs">
                                    Update
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-[#666] text-center">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
