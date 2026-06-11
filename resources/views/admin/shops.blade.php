@extends('layouts.admin')

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="text-[#F4B942] text-2xl heading-font">Shops</div>
            <div class="text-[#AAA] text-sm mt-1 max-w-2xl">Minimal, professional shop management with the key details arranged for clarity and fast review.</div>
        </div>
        <a href="{{ route('admin.shops.create') }}"
            class="inline-flex items-center justify-center rounded-lg bg-[#F4B942] px-4 py-2 text-sm font-semibold text-[#0A0A0B] hover:bg-[#e6b12a]">
            Add Shop
        </a>
    </div>

    <div class="grid gap-4 md:grid-cols-3 mb-8">
        <div class="bg-[#111113] border border-[#1E1E21] rounded-xl p-5">
            <div class="text-[#888] text-[11px] uppercase tracking-[0.3em] mb-3">Total shops</div>
            <div class="text-white text-3xl font-semibold">{{ $shops->count() }}</div>
        </div>
        <div class="bg-[#111113] border border-[#1E1E21] rounded-xl p-5">
            <div class="text-[#888] text-[11px] uppercase tracking-[0.3em] mb-3">Verified locations</div>
            <div class="text-white text-3xl font-semibold">{{ $shops->filter(fn($shop) => $shop->latitude && $shop->longitude)->count() }}</div>
        </div>
        <div class="bg-[#111113] border border-[#1E1E21] rounded-xl p-5">
            <div class="text-[#888] text-[11px] uppercase tracking-[0.3em] mb-3">Phone listed</div>
            <div class="text-white text-3xl font-semibold">{{ $shops->filter(fn($shop) => $shop->phone)->count() }}</div>
        </div>
    </div>

    <div class="bg-[#111113] border border-[#1E1E21] rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-[#DDD]">
                <thead class="bg-[#0F1115] border-b border-[#1E1E21]">
                    <tr class="text-[#888] text-[11px] uppercase tracking-[0.2em]">
                        <th class="px-4 py-3 text-left">Shop</th>
                        <th class="px-4 py-3 text-left">Contact</th>
                        <th class="px-4 py-3 text-left">Location</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1E1E21]">
                    @forelse($shops as $shop)
                        <tr class="hover:bg-[#151518]">
                            <td class="px-4 py-4">
                                <div class="font-semibold text-[#EEE]">{{ $shop->shop_name }}</div>
                                <div class="text-xs text-[#888] mt-1">{{ $shop->owner_name ?? 'No owner' }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-[#EEE]">{{ $shop->phone ?? '-' }}</div>
                                <div class="text-xs text-[#777] mt-1">{{ $shop->email ?? 'No email' }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-[#EEE]">{{ $shop->address ?? '-' }}</div>
                                <div class="text-xs text-[#777] mt-1">
                                    @if($shop->latitude && $shop->longitude)
                                        {{ $shop->latitude }}, {{ $shop->longitude }}
                                    @else
                                        Coordinates missing
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                @php
                                    $statusClasses = 'bg-red-950 text-red-300 ring-1 ring-red-800';
                                    if ($shop->status === 'open') {
                                        $statusClasses = 'bg-emerald-950 text-emerald-300 ring-1 ring-emerald-900';
                                    } elseif ($shop->status === 'busy') {
                                        $statusClasses = 'bg-amber-950 text-amber-300 ring-1 ring-amber-900';
                                    } elseif ($shop->status === 'maintenance') {
                                        $statusClasses = 'bg-sky-950 text-sky-300 ring-1 ring-sky-900';
                                    }
                                @endphp
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold tracking-[0.05em] {{ $statusClasses }}">
                                    {{ ucfirst($shop->status ?? 'unknown') }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <div class="inline-flex items-center gap-2 justify-end">
                                    <a href="{{ route('admin.shops.edit', $shop->id) }}"
                                        class="inline-flex items-center rounded-full border border-[#1E1E21] bg-[#0A0B0D] px-3 py-2 text-xs font-semibold text-[#EEE] transition hover:border-[#F4B942] hover:text-[#F4B942]">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.shops.delete', $shop->id) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Delete this shop? This cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center rounded-full bg-[#8F1D1D] px-3 py-2 text-xs font-semibold text-[#FFE5E5] transition hover:bg-[#A11F1F]">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-[#666]">No shops found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
