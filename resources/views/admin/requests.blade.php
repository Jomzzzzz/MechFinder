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
                            @php
                                $statusThemes = [
                                    'pending' => ['bg' => '#78350f', 'text' => '#fbbf24'],
                                    'accepted' => ['bg' => '#1e3a8a', 'text' => '#93c5fd'],
                                    'completed' => ['bg' => '#14532d', 'text' => '#86efac'],
                                    'declined' => ['bg' => '#7f1d1d', 'text' => '#fecaca'],
                                ];
                                $theme = $statusThemes[$req->status] ?? ['bg' => '#111827', 'text' => '#9ca3af'];
                            @endphp
                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold"
                                style="background-color: {{ $theme['bg'] }}; color: {{ $theme['text'] }};">
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
