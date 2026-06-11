@extends('layouts.motorist')

@section('content')
<div class="min-h-screen flex flex-col text-white bg-[#0a0a0a]">
    <div class="sticky top-0 z-20 bg-gradient-to-b from-[#0d1118] to-[#0a0a0a] border-b border-white/5 px-4 py-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('motorist.dashboard') }}" class="w-10 h-10 rounded-full bg-white/10 border border-white/10 flex items-center justify-center text-lg hover:border-orange-500 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-[18px] font-extrabold">My Requests</h1>
                <p class="text-sm text-gray-400">View your recent dispatch requests</p>
            </div>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto px-4 py-5 space-y-4">
        @if($requests->isEmpty())
            <div class="rounded-3xl bg-white/5 border border-white/10 p-6 text-center text-gray-400">
                No requests found yet.
            </div>
        @else
            @foreach($requests as $request)
            <div class="bg-white/5 border border-white/10 rounded-2xl p-4">

                <!-- Issue Type & Location -->
                <div class="mb-4">
                    <div class="font-bold text-white mb-1">
                        {{ $request->issue_type ?? 'No issue' }}
                    </div>
                    <div class="text-xs text-gray-400">
                        📍 {{ $request->location ?? 'No location' }}
                    </div>
                </div>

                <!-- Motorist Information (if available) -->
                @if($request->owner_name || $request->contact_number)
                <div class="bg-white/5 rounded-xl p-3 mb-4 border border-white/5">
                    <div class="text-xs text-gray-500 mb-2 font-semibold">MOTORIST INFO</div>
                    <div class="space-y-1 text-sm text-gray-300">
                        @if($request->owner_name)
                            <div>👤 Name: <span class="text-white font-semibold">{{ $request->owner_name }}</span></div>
                        @endif
                        @if($request->contact_number)
                            <div>📞 Contact: <span class="text-white font-semibold">{{ $request->contact_number }}</span></div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Vehicle Information (if available) -->
                @if($request->vehicle_make_model || $request->vehicle_variant_color || $request->plate_temp_number)
                <div class="bg-white/5 rounded-xl p-3 mb-4 border border-white/5">
                    <div class="text-xs text-gray-500 mb-2 font-semibold">VEHICLE INFO</div>
                    <div class="space-y-1 text-sm text-gray-300">
                        @if($request->vehicle_make_model)
                            <div>🏍️ Make/Model: <span class="text-white font-semibold">{{ $request->vehicle_make_model }}</span></div>
                        @endif
                        @if($request->vehicle_variant_color)
                            <div>🎨 Variant/Color: <span class="text-white font-semibold">{{ $request->vehicle_variant_color }}</span></div>
                        @endif
                        @if($request->plate_temp_number)
                            <div>🔢 License/Temp #: <span class="text-white font-semibold">{{ $request->plate_temp_number }}</span></div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- ACTION BUTTONS -->
                <div class="flex gap-2">

                    <a href="/motorist/dispatch/{{ $request->id }}"
                    class="px-3 py-2 bg-gray-700 rounded-lg text-sm hover:bg-gray-600 transition">
                        View
                    </a>

                    <a href="{{ route('motorist.chat', $request->id) }}"
                    class="px-3 py-2 bg-orange-500 text-black rounded-lg text-sm font-semibold hover:bg-orange-600 transition">
                        Message
                    </a>

                </div>

            </div>
            @endforeach
        @endif
    </div>
</div>
@endsection