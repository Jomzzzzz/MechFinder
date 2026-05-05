@extends('layouts.motorist')

@section('content')
<div class="min-h-screen flex flex-col text-white">

    <!-- Header -->
    <div class="sticky top-0 z-20 bg-[#0d1118]/95 backdrop-blur border-b border-white/5 px-4 pt-4 pb-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('motorist.shops', ['lat' => $userLat, 'lng' => $userLng]) }}"
               class="w-9 h-9 rounded-full bg-white/10 border border-white/10 flex items-center justify-center text-sm">←</a>
            <h1 class="text-[16px] font-extrabold tracking-wide">SHOP DETAILS</h1>
        </div>
    </div>

    <!-- Shop Info Card -->
    <div class="px-4 pt-5 pb-4">
        <div class="glass-card rounded-[22px] p-5 space-y-4">
            
            <!-- Shop Header -->
            <div class="flex items-start justify-between gap-3">
                <div class="flex-1">
                    <h2 class="text-[20px] font-extrabold">{{ $shop->shop_name }}</h2>
                    <p class="text-[13px] text-gray-400 mt-1">{{ $shop->distance }} km away</p>
                </div>
                <div class="w-14 h-14 rounded-xl bg-white/10 border border-white/10 shrink-0"></div>
            </div>

            <!-- Rating & Status -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-[16px]">⭐⭐⭐⭐⭐</span>
                        <span class="text-[13px] text-gray-300">({{ $shop->review_count ?? 0 }} reviews)</span>
                    </div>
                </div>

                <div>
                    @if($shop->status === 'open')
                        <span class="bg-green-500/20 text-green-400 border border-green-500/30 text-[12px] px-4 py-2 rounded-xl font-bold inline-block">
                            ✓ Dispatch Available
                        </span>
                    @elseif($shop->status === 'busy')
                        <span class="bg-yellow-500/20 text-yellow-300 border border-yellow-500/30 text-[12px] px-4 py-2 rounded-xl font-bold inline-block">
                            ⏱ Currently Busy
                        </span>
                    @else
                        <span class="bg-gray-500/20 text-gray-300 border border-gray-500/30 text-[12px] px-4 py-2 rounded-xl font-bold inline-block">
                            ✕ Currently Closed
                        </span>
                    @endif
                </div>
            </div>

            <!-- Services -->
            <div>
                <p class="text-[12px] text-gray-400 font-semibold mb-3">SERVICES</p>
                <div class="flex flex-wrap gap-2">
                    <span class="bg-green-500/20 text-green-300 border border-green-500/30 text-[12px] px-4 py-2 rounded-xl font-semibold">Flat Tire</span>
                    <span class="bg-green-500/20 text-green-300 border border-green-500/30 text-[12px] px-4 py-2 rounded-xl font-semibold">Engine</span>
                    <span class="bg-green-500/20 text-green-300 border border-green-500/30 text-[12px] px-4 py-2 rounded-xl font-semibold">Brake</span>
                    <span class="bg-green-500/20 text-green-300 border border-green-500/30 text-[12px] px-4 py-2 rounded-xl font-semibold">Oil Change</span>
                </div>
            </div>

            <!-- Shop Hours & Contact -->
            <div class="grid grid-cols-2 gap-3 pt-3 border-t border-white/10">
                <div>
                    <p class="text-[11px] text-gray-400 mb-1">HOURS</p>
                    <p class="text-[13px] font-semibold">9:00 AM - 6:00 PM</p>
                </div>
                <div>
                    <p class="text-[11px] text-gray-400 mb-1">ETA</p>
                    <p class="text-[13px] font-semibold">~{{ $shop->eta_minutes ?? 5 }} minutes</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reviews Section -->
    <div class="px-4 pt-4">
        <p class="text-[14px] font-extrabold tracking-wide mb-3">RECENT REVIEWS</p>
        
        <div class="space-y-3 pb-6">
            <!-- Review Card 1 -->
            <div class="glass-card rounded-[16px] p-4">
                <div class="flex items-start justify-between mb-2">
                    <p class="text-[13px] font-semibold text-gray-200">Juan Dela Cruz</p>
                    <span class="text-[12px]">⭐⭐⭐⭐⭐</span>
                </div>
                <p class="text-[12px] text-gray-400 leading-relaxed">Excellent service! Fixed my motorcycle in just 30 minutes.</p>
                <p class="text-[10px] text-gray-500 mt-2">2 days ago</p>
            </div>

            <!-- Review Card 2 -->
            <div class="glass-card rounded-[16px] p-4">
                <div class="flex items-start justify-between mb-2">
                    <p class="text-[13px] font-semibold text-gray-200">Maria Santos</p>
                    <span class="text-[12px]">⭐⭐⭐⭐</span>
                </div>
                <p class="text-[12px] text-gray-400 leading-relaxed">Good service, reasonable prices. Will visit again.</p>
                <p class="text-[10px] text-gray-500 mt-2">1 week ago</p>
            </div>

            <!-- Review Card 3 -->
            <div class="glass-card rounded-[16px] p-4">
                <div class="flex items-start justify-between mb-2">
                    <p class="text-[13px] font-semibold text-gray-200">Pedro Romero</p>
                    <span class="text-[12px]">⭐⭐⭐⭐⭐</span>
                </div>
                <p class="text-[12px] text-gray-400 leading-relaxed">Highly recommended! Professional mechanics and fair pricing.</p>
                <p class="text-[10px] text-gray-500 mt-2">2 weeks ago</p>
            </div>
        </div>
    </div>

    <!-- Request Dispatch Button -->
    <div class="px-4 pb-6 sticky bottom-0">
        <a href="{{ route('motorist.dispatch', ['shop_id' => $shop->id, 'lat' => $userLat, 'lng' => $userLng]) }}"
           class="w-full block py-4 rounded-[16px] danger-btn text-[14px] font-extrabold tracking-wide text-center">
            🚨 REQUEST DISPATCH
        </a>
    </div>
</div>

@endsection