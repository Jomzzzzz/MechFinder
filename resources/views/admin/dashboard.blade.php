@extends('layouts.admin')

@section('content')
    <div class="mb-8 text-[#F4B942] text-2xl heading-font">Admin Dashboard</div>

    <div class="gap-4 grid grid-cols-2 md:grid-cols-4 mb-10">
        <div class="bg-[#111113] p-5 border border-[#1E1E21] rounded-xl">
            <p class="mb-1 text-[#888] text-xs">Total Shops</p>
            <p class="font-bold text-[#F4B942] text-3xl">{{ $totalShops }}</p>
        </div>
        <div class="bg-[#111113] p-5 border border-[#1E1E21] rounded-xl">
            <p class="mb-1 text-[#888] text-xs">Shop Users</p>
            <p class="font-bold text-white text-3xl">{{ $totalUsers }}</p>
        </div>
        <div class="bg-[#111113] p-5 border border-[#1E1E21] rounded-xl">
            <p class="mb-1 text-[#888] text-xs">Dispatch Requests</p>
            <p class="font-bold text-white text-3xl">{{ $totalRequests }}</p>
        </div>
        <div class="bg-[#111113] p-5 border border-[#1E1E21] rounded-xl">
            <p class="mb-1 text-[#888] text-xs">Reviews</p>
            <p class="font-bold text-white text-3xl">{{ $totalReviews }}</p>
        </div>
    </div>

    <div class="mb-4 text-[#EEEEEE] text-lg heading-font">Recent Requests</div>
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
                @forelse($recentRequests as $req)
                    <tr class="hover:bg-[#1A1A1D] border-[#1E1E21] border-b">
                        <td class="px-4 py-3 text-[#888]">#{{ $req->id }}</td>
                        <td class="px-4 py-3">{{ $req->shop_name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $req->motorist_name }}</td>
                        <td class="px-4 py-3">{{ $req->issue_type ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @php
                                switch ($req->status) {
                                    case 'pending':
                                        $bgColor = '#78350f';
                                        $textColor = '#fbbf24';
                                        break;
                                    case 'accepted':
                                        $bgColor = '#1e3a8a';
                                        $textColor = '#93c5fd';
                                        break;
                                    case 'completed':
                                        $bgColor = '#14532d';
                                        $textColor = '#86efac';
                                        break;
                                    case 'declined':
                                        $bgColor = '#7f1d1d';
                                        $textColor = '#fecaca';
                                        break;
                                    default:
                                        $bgColor = '#111827';
                                        $textColor = '#9ca3af';
                                }
                            @endphp
                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold"
                                style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
                                {{ ucfirst($req->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-[#888]">{{ \Carbon\Carbon::parse($req->created_at)->format('M d, Y') }}
                        </td>
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
