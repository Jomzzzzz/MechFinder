@extends('layouts.admin')

@section('content')
    <div class="mb-8 flex items-center justify-between">
        <div>
            <div class="text-[#F4B942] text-2xl heading-font">Map Locations</div>
            <div class="text-[#AAA] text-sm mt-1">Manage manual map points stored in the database.</div>
        </div>
        <a href="{{ route('admin.maps.create') }}"
            class="bg-[#F4B942] text-[#0A0A0B] px-4 py-2 rounded-lg font-semibold hover:bg-[#e6b12a]">Add Map Location</a>
    </div>

    <div class="bg-[#111113] border border-[#1E1E21] rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="border-[#1E1E21] border-b">
                <tr class="text-[#888] text-xs uppercase">
                    <th class="px-4 py-3 text-left">Title</th>
                    <th class="px-4 py-3 text-left">Address</th>
                    <th class="px-4 py-3 text-left">Coordinates</th>
                    <th class="px-4 py-3 text-left">Created</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($maps as $map)
                    <tr class="hover:bg-[#1A1A1D] border-[#1E1E21] border-b">
                        <td class="px-4 py-3">{{ $map->title }}</td>
                        <td class="px-4 py-3 text-[#888]">{{ $map->address ?? '-' }}</td>
                        <td class="px-4 py-3 text-[#888]">{{ $map->latitude }}, {{ $map->longitude }}</td>
                        <td class="px-4 py-3 text-[#888]">{{ $map->created_at ? date('Y-m-d', strtotime($map->created_at)) : '-' }}</td>
                        <td class="px-4 py-3 space-x-2">
                            <a href="{{ route('admin.maps.edit', $map->id) }}"
                                class="bg-slate-800 hover:bg-slate-700 px-3 py-1 rounded text-slate-200 text-xs">Edit</a>
                            <form action="{{ route('admin.maps.delete', $map->id) }}" method="POST" class="inline"
                                onsubmit="return confirm('Delete this map location?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="bg-red-800 hover:bg-red-700 px-3 py-1 rounded text-red-200 text-xs">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-[#666] text-center">No map locations found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
