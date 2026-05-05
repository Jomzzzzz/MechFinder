@extends('layouts.admin')

@section('content')
    <div class="mb-8 text-[#F4B942] text-2xl heading-font">Shops</div>

    <div class="bg-[#111113] border border-[#1E1E21] rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="border-[#1E1E21] border-b">
                <tr class="text-[#888] text-xs uppercase">
                    <th class="px-4 py-3 text-left">Shop Name</th>
                    <th class="px-4 py-3 text-left">Owner</th>
                    <th class="px-4 py-3 text-left">Address</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Avg Rating</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($shops as $shop)
                    <tr class="hover:bg-[#1A1A1D] border-[#1E1E21] border-b">
                        <td class="px-4 py-3">{{ $shop->shop_name }}</td>
                        <td class="px-4 py-3 text-[#888]">{{ $shop->owner_name ?? '-' }}</td>
                        <td class="px-4 py-3 text-[#888]">{{ $shop->address ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span
                                class="px-2 py-1 rounded text-xs
                        @if ($shop->status === 'available') bg-green-900 text-green-300
                        @elseif($shop->status === 'busy') bg-yellow-900 text-yellow-300
                        @else bg-red-900 text-red-300 @endif">
                                {{ ucfirst($shop->status ?? 'unknown') }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ number_format($shop->avg_rating, 1) }} ({{ $shop->review_count }})</td>
                        <td class="px-4 py-3">
                            <form action="{{ route('admin.shops.delete', $shop->id) }}" method="POST"
                                onsubmit="return confirm('Delete this shop? This cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="bg-red-800 hover:bg-red-700 px-2 py-1 rounded text-red-200 text-xs">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-[#666] text-center">No shops found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
