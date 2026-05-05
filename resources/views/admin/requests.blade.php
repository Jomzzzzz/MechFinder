@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-8">
        <div class="text-[#F4B942] text-2xl heading-font">Dispatch Requests</div>
        <div class="flex gap-2 text-sm">
            <a href="{{ route('admin.requests') }}"
                class="px-3 py-1 rounded {{ !$status ? 'bg-[#F4B942] text-[#0A0A0B]' : 'bg-[#1E1E21] text-[#AAA]' }}">All</a>
            @foreach (['pending', 'accepted', 'arrived', 'in_progress', 'completed', 'declined'] as $s)
                <a href="{{ route('admin.requests', ['status' => $s]) }}"
                    class="px-3 py-1 rounded {{ $status === $s ? 'bg-[#F4B942] text-[#0A0A0B]' : 'bg-[#1E1E21] text-[#AAA]' }}">
                    {{ ucfirst(str_replace('_', ' ', $s)) }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="bg-[#111113] border border-[#1E1E21] rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="border-[#1E1E21] border-b">
                <tr class="text-[#888] text-xs uppercase">
                    <th class="px-4 py-3 text-left">ID</th>
                    <th class="px-4 py-3 text-left">Shop</th>
                    <th class="px-4 py-3 text-left">Motorist</th>
                    <th class="px-4 py-3 text-left">Issue</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $req)
                    <tr class="hover:bg-[#1A1A1D] border-[#1E1E21] border-b">
                        <td class="px-4 py-3 text-[#888]">#{{ $req->id }}</td>
                        <td class="px-4 py-3">{{ $req->shop_name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $req->motorist_name }}</td>
                        <td class="px-4 py-3 text-[#AAA]">{{ $req->issue_type ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span
                                class="px-2 py-1 rounded text-xs
                        @if ($req->status === 'pending') bg-yellow-900 text-yellow-300
                        @elseif($req->status === 'accepted') bg-blue-900 text-blue-300
                        @elseif($req->status === 'completed') bg-green-900 text-green-300
                        @elseif($req->status === 'declined') bg-red-900 text-red-300
                        @else bg-[#222] text-[#888] @endif">
                                {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-[#888]">
                            {{ \Carbon\Carbon::parse($req->created_at)->format('M d, Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-[#666] text-center">No requests found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
