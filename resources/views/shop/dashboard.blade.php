@extends('layouts.shop')

@section('content')

@php
    $currentStatus = strtolower($shopStatus ?? 'closed');

    $statusClasses = match ($currentStatus) {
        'open' => 'bg-green-500/10 text-green-400 border-green-500/20',
        'busy' => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20',
        'maintenance' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
        default => 'bg-red-500/10 text-red-400 border-red-500/20',
    };
@endphp

<div class="min-h-screen bg-[#0F0F0F] text-white">

    <!-- TOP HEADER -->
    <div class="border-b border-white/10 px-6 md:px-10 py-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            <!-- LEFT -->
            <div>
                <h1 class="text-3xl font-black tracking-wide">
                    Motor Shop Dashboard
                </h1>
                <p class="text-gray-400 text-sm mt-1">
                    Manage dispatch requests and monitor motorist locations.
                </p>
            </div>

            <!-- RIGHT (STATUS + PROFILE) -->
            <div class="flex items-center gap-4">

                <!-- STATUS -->
                <div id="shop-status-badge"
                     class="px-4 py-2 rounded-xl text-sm font-black border {{ $statusClasses }}">
                    {{ strtoupper($currentStatus) }}
                </div>

                <!-- PROFILE -->
                <div class="relative">

                    <button id="profileBtn"
                        class="flex items-center gap-3 bg-white/5 border border-white/10 px-4 py-2 rounded-xl hover:bg-white/10 transition">

                        <!-- AVATAR -->
                        <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center font-bold">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>

                        <!-- NAME -->
                        <span class="text-sm font-semibold text-white hidden md:block">
                            {{ auth()->user()->name ?? 'Shop Owner' }}
                        </span>

                        <!-- ICON -->
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- DROPDOWN -->
                    <div id="profileMenu"
                        class="hidden absolute right-0 mt-2 w-52 bg-[#1A1A1A] border border-white/10 rounded-xl shadow-lg overflow-hidden z-50">

                        <div class="px-4 py-3 border-b border-white/10">
                            <p class="text-sm text-white font-semibold">
                                {{ auth()->user()->name ?? 'Shop Owner' }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ auth()->user()->email ?? '' }}
                            </p>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-4 py-3 text-sm text-red-400 hover:bg-red-500/10">
                                🚪 Logout
                            </button>
                        </form>

                    </div>

                </div>

            </div>

        </div>
    </div>

    <div class="px-6 md:px-10 pb-10">

        <!-- STATS -->
        <div class="grid md:grid-cols-2 gap-6 mb-8">

            <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl p-6">
                <p class="text-sm text-gray-400 mb-2">Pending Requests</p>
                <h2 id="pending-count" class="text-4xl font-black text-red-500">
                    {{ $pending }}
                </h2>
            </div>

            <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl p-6">
                <p class="text-sm text-gray-400 mb-2">Average Rating</p>
                <h2 class="text-4xl font-black text-orange-400">
                    {{ $averageRating > 0 ? '⭐ '.$averageRating : 'No rating' }}
                </h2>
            </div>

        </div>

        <!-- MAIN GRID -->
        <div class="grid lg:grid-cols-3 gap-8">

            <!-- REQUESTS -->
            <div class="lg:col-span-2 bg-[#1A1A1A] border border-white/10 rounded-3xl p-6">

                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-2xl font-black">Incoming Requests</h3>
                        <p class="text-sm text-gray-400 mt-1">
                            New motorist dispatch requests will appear here.
                        </p>
                    </div>

                    <span id="pending-label"
                          class="px-3 py-1 rounded-full bg-red-500/10 text-red-400 text-xs font-black border border-red-500/20">
                        {{ $pending }} NEW
                    </span>
                </div>

                <div id="requests-container" class="space-y-4 transition-opacity duration-200">
                    @include('components.requests-list', ['requests' => $requests])
                </div>
            </div>

            <!-- RIGHT SIDE -->
            <div class="space-y-8">

                <!-- LIVE MAP -->
<div class="bg-[#1A1A1A] border border-white/10 rounded-3xl p-6">

    <div class="flex items-center justify-between mb-5">
        <div>
            <h3 class="text-2xl font-black">Live Map</h3>
            <p class="text-sm text-gray-400 mt-1">
                Click to view shop and motorist request locations.
            </p>
        </div>

        <span class="flex items-center gap-2 px-3 py-1 rounded-full bg-green-500/10 text-green-400 text-xs font-black border border-green-500/20">
            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
            LIVE
        </span>
    </div>

    <button type="button" onclick="openLiveMapModal()" class="w-full block">
        <div id="shop-live-map"
             class="h-72 rounded-2xl border border-white/10 overflow-hidden bg-black cursor-pointer">
        </div>
    </button>

    <div class="mt-4 flex flex-wrap gap-3 text-xs text-gray-400">
        <span class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-blue-500"></span>
            Shop
        </span>

        <span class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-red-500"></span>
            Motorist Request
        </span>
    </div>

    <div id="map-status-text" class="mt-4 text-xs text-gray-500">
        Loading map...
    </div>
</div>

<!-- MAP MODAL -->
<div id="liveMapModal" class="hidden fixed inset-0 z-[9999] bg-black/80 px-4 py-6">
    <div class="max-w-6xl mx-auto h-full bg-[#121214] border border-white/10 rounded-3xl overflow-hidden flex flex-col">

        <div class="p-5 border-b border-white/10 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-black text-white">Live Map</h2>
                <p class="text-sm text-gray-400">
                    Blue marker = shop, red markers = motorists requesting this shop.
                </p>
            </div>

            <button onclick="closeLiveMapModal()"
                class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-2 rounded-xl font-black">
                CLOSE
            </button>
        </div>

        <div id="modal-live-map" class="flex-1 bg-black"></div>
    </div>
</div>

            </div>
        </div>

    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
let smallMap = null;
let modalMap = null;

let latestShop = null;
let latestRequests = [];

let smallShopMarker = null;
let modalShopMarker = null;

let smallRequestMarkers = [];
let modalRequestMarkers = [];

document.addEventListener('DOMContentLoaded', function () {
    initSmallMap();
    loadLiveMapData();

    setInterval(loadLiveMapData, 3000);
});

function initSmallMap() {
    smallMap = L.map('shop-live-map').setView([14.8386, 120.2842], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19
    }).addTo(smallMap);
}

function initModalMap() {
    if (modalMap) return;

    modalMap = L.map('modal-live-map').setView([14.8386, 120.2842], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19
    }).addTo(modalMap);
}

function openLiveMapModal() {
    document.getElementById('liveMapModal').classList.remove('hidden');

    initModalMap();

    setTimeout(() => {
        modalMap.invalidateSize();
        renderMarkers(modalMap, true);
        fitMap(modalMap);
    }, 200);
}

function closeLiveMapModal() {
    document.getElementById('liveMapModal').classList.add('hidden');
}

async function loadLiveMapData() {
    try {
        const response = await fetch('/shop/dashboard-map-data', {
            headers: {
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (!data.success) {
            document.getElementById('map-status-text').innerText = 'Unable to load map data.';
            return;
        }

        latestShop = data.shop;
        latestRequests = data.requests || [];

        renderMarkers(smallMap, false);
        fitMap(smallMap);

        if (modalMap && !document.getElementById('liveMapModal').classList.contains('hidden')) {
            renderMarkers(modalMap, true);
            fitMap(modalMap);
        }

        document.getElementById('map-status-text').innerText =
            `${latestRequests.length} motorist request location(s) found.`;

    } catch (error) {
        console.error(error);
        document.getElementById('map-status-text').innerText = 'Map loading error.';
    }
}

function renderMarkers(map, isModal) {
    if (!map) return;

    let requestMarkers = isModal ? modalRequestMarkers : smallRequestMarkers;

    requestMarkers.forEach(marker => map.removeLayer(marker));

    if (isModal) {
        modalRequestMarkers = [];
    } else {
        smallRequestMarkers = [];
    }

    const shopIcon = L.divIcon({
        className: '',
        html: `<div style="width:22px;height:22px;background:#3B82F6;border:3px solid white;border-radius:50%;box-shadow:0 0 15px #3B82F6;"></div>`,
        iconSize: [22, 22],
        iconAnchor: [11, 11]
    });

    const motoristIcon = L.divIcon({
        className: '',
        html: `<div style="width:20px;height:20px;background:#EF4444;border:3px solid white;border-radius:50%;box-shadow:0 0 15px #EF4444;"></div>`,
        iconSize: [20, 20],
        iconAnchor: [10, 10]
    });

    if (latestShop && latestShop.latitude && latestShop.longitude) {
        const shopLat = parseFloat(latestShop.latitude);
        const shopLng = parseFloat(latestShop.longitude);

        if (!isNaN(shopLat) && !isNaN(shopLng)) {
            const popup = `
                <strong>${escapeHtml(latestShop.shop_name || 'Shop')}</strong><br>
                ${escapeHtml(latestShop.address || latestShop.location || 'No address')}<br>
                Status: ${escapeHtml(latestShop.status || 'closed')}
            `;

            if (isModal) {
                if (modalShopMarker) {
                    modalShopMarker.setLatLng([shopLat, shopLng]).setPopupContent(popup);
                } else {
                    modalShopMarker = L.marker([shopLat, shopLng], { icon: shopIcon }).addTo(map).bindPopup(popup);
                }
            } else {
                if (smallShopMarker) {
                    smallShopMarker.setLatLng([shopLat, shopLng]).setPopupContent(popup);
                } else {
                    smallShopMarker = L.marker([shopLat, shopLng], { icon: shopIcon }).addTo(map).bindPopup(popup);
                }
            }
        }
    }

    latestRequests.forEach(req => {
        const lat = parseFloat(req.latitude);
        const lng = parseFloat(req.longitude);

        if (isNaN(lat) || isNaN(lng)) return;

        const popup = `
            <strong>${escapeHtml(req.motorist_name || req.guest_name || 'Motorist')}</strong><br>
            Issue: ${escapeHtml(req.issue_type || 'Motorcycle Issue')}<br>
            Status: ${escapeHtml(req.status || 'requested')}<br>
            Location: ${escapeHtml(req.location || 'No location')}
        `;

        const marker = L.marker([lat, lng], { icon: motoristIcon }).addTo(map).bindPopup(popup);

        if (isModal) {
            modalRequestMarkers.push(marker);
        } else {
            smallRequestMarkers.push(marker);
        }
    });
}

function fitMap(map) {
    const points = [];

    if (latestShop && latestShop.latitude && latestShop.longitude) {
        points.push([parseFloat(latestShop.latitude), parseFloat(latestShop.longitude)]);
    }

    latestRequests.forEach(req => {
        const lat = parseFloat(req.latitude);
        const lng = parseFloat(req.longitude);

        if (!isNaN(lat) && !isNaN(lng)) {
            points.push([lat, lng]);
        }
    });

    if (points.length === 1) {
        map.setView(points[0], 16);
    }

    if (points.length > 1) {
        map.fitBounds(points, {
            padding: [40, 40],
            maxZoom: 16
        });
    }
}

function escapeHtml(text) {
    return String(text ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}
</script>

<script>
const profileBtn = document.getElementById('profileBtn');
const profileMenu = document.getElementById('profileMenu');

profileBtn.addEventListener('click', function (e) {
    e.stopPropagation();
    profileMenu.classList.toggle('hidden');
});

document.addEventListener('click', function () {
    profileMenu.classList.add('hidden');
});
</script>

@if($needsProfileCompletion)
<div class="fixed inset-0 z-[9999] bg-black/80 flex items-center justify-center px-4">
    <div class="w-full max-w-lg bg-[#1a1a1a] border border-orange-500/40 rounded-2xl p-6 shadow-2xl">
        
        <h2 class="text-2xl font-black text-orange-500 mb-2">
            Complete Shop Profile
        </h2>

        <p class="text-gray-400 text-sm mb-6">
            Please complete your shop information before using the dashboard.
        </p>

        <form method="POST" action="{{ route('shop.update') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-bold text-gray-300 mb-2">Shop Name</label>
                <input 
                    type="text" 
                    name="shop_name" 
                    value="{{ old('shop_name', $shop->shop_name ?? '') }}"
                    required
                    class="w-full bg-white/10 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-orange-500"
                    placeholder="Example: SSJC Motor Shop"
                >
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-300 mb-2">Shop Address</label>
                <input 
                    type="text" 
                    name="address" 
                    value="{{ old('address', $shop->address ?? '') }}"
                    required
                    class="w-full bg-white/10 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-orange-500"
                    placeholder="87 Lower Kalaklan, Olongapo City"
                >
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-300 mb-2">Phone Number</label>
                <input 
                    type="text" 
                    name="phone" 
                    value="{{ old('phone', $shop->phone ?? '') }}"
                    required
                    class="w-full bg-white/10 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-orange-500"
                    placeholder="+63 917 140 3498"
                >
            </div>

            <input type="hidden" name="latitude" value="{{ old('latitude', $shop->latitude ?? 14.8386) }}">
            <input type="hidden" name="longitude" value="{{ old('longitude', $shop->longitude ?? 120.2842) }}">
            <input type="hidden" name="status" value="{{ old('status', $shop->status ?? 'closed') }}">

            <button 
                type="submit"
                class="w-full bg-orange-500 hover:bg-orange-600 text-white font-black py-3 rounded-lg transition">
                Save Shop Info
            </button>
        </form>
    </div>
</div>
@endif

@endsection