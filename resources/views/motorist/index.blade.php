@extends('layouts.motorist')

@section('content')
<header class="flex items-center justify-between mb-4">
    <div>
        <h1 class="text-2xl font-black tracking-tight">MECHFINDER</h1>
        <p class="text-xs text-gray-400">Emergency motorcycle repair assistant</p>
    </div>

    <button onclick="openProfileModal()" class="dark-btn rounded-2xl px-3 py-2 text-sm">
        👤
    </button>
</header>

<section class="glass rounded-3xl p-4 mb-4">
    <div class="flex items-center justify-between mb-3">
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-widest">Current Location</p>
            <p id="locationText" class="font-bold text-sm">Detecting your location...</p>
        </div>

        <button onclick="locateUser()" class="brand-btn rounded-2xl px-4 py-2 text-sm">
            Locate
        </button>
    </div>

    <div id="map"></div>
</section>

<section class="mb-4">
    <div class="flex items-center justify-between mb-3">
        <h2 class="font-black text-lg">Nearby Motor Shops</h2>
        <span id="shopCount" class="text-xs text-gray-400">Loading...</span>
    </div>

    <div id="shopsList" class="space-y-3"></div>
</section>

<div id="profileModal" class="hidden fixed inset-0 z-50 bg-black/80 p-4">
    <div class="glass rounded-3xl p-5 max-w-md mx-auto mt-10">
        <h2 class="text-xl font-black mb-4">Motorist Info</h2>

        <div class="space-y-3">
            <input id="ownerName" class="w-full bg-black/40 border border-white/10 rounded-2xl px-4 py-3" placeholder="Owner name">
            <input id="contactNumber" class="w-full bg-black/40 border border-white/10 rounded-2xl px-4 py-3" placeholder="Contact number">
            <input id="vehicleMakeModel" class="w-full bg-black/40 border border-white/10 rounded-2xl px-4 py-3" placeholder="Motorcycle model e.g. Yamaha Mio">
            <input id="vehicleVariantColor" class="w-full bg-black/40 border border-white/10 rounded-2xl px-4 py-3" placeholder="Color / variant">
            <input id="plateTempNumber" class="w-full bg-black/40 border border-white/10 rounded-2xl px-4 py-3" placeholder="Plate number / temporary number">
        </div>

        <div class="grid grid-cols-2 gap-3 mt-5">
            <button onclick="closeProfileModal()" class="dark-btn rounded-2xl py-3 font-bold">Cancel</button>
            <button onclick="saveProfile()" class="brand-btn rounded-2xl py-3">Save</button>
        </div>
    </div>
</div>

<div id="dispatchModal" class="hidden fixed inset-0 z-50 bg-black/80 p-4 overflow-y-auto">
    <div class="glass rounded-3xl p-5 max-w-md mx-auto mt-8 mb-8">
        <h2 class="text-xl font-black mb-1">Request Mechanic</h2>
        <p id="selectedShopName" class="text-sm text-orange-300 mb-4"></p>

        <input type="hidden" id="selectedShopId">

        <div class="space-y-3">
            <select id="issueType" class="w-full bg-black/40 border border-white/10 rounded-2xl px-4 py-3">
                <option value="">Select issue</option>
                <option value="Flat Tire">Flat Tire</option>
                <option value="Engine Stall">Engine Stall</option>
                <option value="Battery Problem">Battery Problem</option>
                <option value="Brake Problem">Brake Problem</option>
                <option value="Chain Problem">Chain Problem</option>
                <option value="Other">Other</option>
            </select>

            <textarea id="description" class="w-full bg-black/40 border border-white/10 rounded-2xl px-4 py-3" rows="3" placeholder="Describe your problem"></textarea>
        </div>

        <div class="grid grid-cols-2 gap-3 mt-5">
            <button onclick="closeDispatchModal()" class="dark-btn rounded-2xl py-3 font-bold">Cancel</button>
            <button onclick="submitDispatch()" class="brand-btn rounded-2xl py-3">Send</button>
        </div>
    </div>
</div>

<div id="statusBox" class="hidden fixed bottom-4 left-4 right-4 z-40 max-w-md mx-auto glass rounded-3xl p-4">
    <p class="text-xs text-gray-400 uppercase tracking-widest">Request Status</p>
    <h3 id="statusTitle" class="font-black text-lg">Waiting...</h3>
    <p id="statusDetail" class="text-sm text-gray-300"></p>
</div>
@endsection

@section('scripts')
<script>
let map;
let userMarker;
let shopMarkers = [];
let userLat = 14.8386;
let userLng = 120.2842;
let selectedShop = null;
let currentRequestId = localStorage.getItem('mf_current_request_id');

document.addEventListener('DOMContentLoaded', () => {
    initProfileInputs();
    initMap();
    locateUser();

    if (currentRequestId) {
        pollRequestStatus();
        setInterval(pollRequestStatus, 5000);
    }
});

function initMap() {
    map = L.map('map').setView([userLat, userLng], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);
}

function locateUser() {
    if (!navigator.geolocation) {
        document.getElementById('locationText').innerText = 'Geolocation not supported.';
        loadShops();
        return;
    }

    navigator.geolocation.getCurrentPosition(position => {
        userLat = position.coords.latitude;
        userLng = position.coords.longitude;

        document.getElementById('locationText').innerText = 'Location detected';

        if (userMarker) {
            map.removeLayer(userMarker);
        }

        userMarker = L.marker([userLat, userLng]).addTo(map).bindPopup('You are here').openPopup();
        map.setView([userLat, userLng], 15);

        loadShops();
    }, () => {
        document.getElementById('locationText').innerText = 'Using default Olongapo location';
        loadShops();
    }, {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 30000
    });
}

async function loadShops() {
    const res = await fetch(`/motorist/shops?lat=${userLat}&lng=${userLng}`);
    const shops = await res.json();

    document.getElementById('shopCount').innerText = `${shops.length} shops`;

    shopMarkers.forEach(marker => map.removeLayer(marker));
    shopMarkers = [];

    const list = document.getElementById('shopsList');
    list.innerHTML = '';

    shops.forEach(shop => {
        if (shop.latitude && shop.longitude) {
            const marker = L.marker([shop.latitude, shop.longitude])
                .addTo(map)
                .bindPopup(`
                    <b>${shop.name}</b><br>
                    ${shop.address ?? ''}<br>
                    Status: ${shop.status ?? 'unknown'}
                `);

            shopMarkers.push(marker);
        }

        list.innerHTML += `
            <div class="glass rounded-3xl p-4">
                <div class="flex justify-between gap-3">
                    <div>
                        <h3 class="font-black">${shop.name}</h3>
                        <p class="text-xs text-gray-400">${shop.address ?? 'No address'}</p>
                        <p class="text-xs mt-1">
                            ⭐ ${shop.rating ?? 0} 
                            <span class="text-gray-500">(${shop.review_count ?? 0} reviews)</span>
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            ${shop.distance ? shop.distance + ' km away' : 'Distance unavailable'}
                            ${shop.eta ? ' • ETA ' + shop.eta + ' min' : ''}
                        </p>
                    </div>

                    <span class="text-xs h-fit px-3 py-1 rounded-full ${shop.status === 'open' ? 'bg-green-500/20 text-green-300' : 'bg-red-500/20 text-red-300'}">
                        ${shop.status ?? 'closed'}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3 mt-4">
                    <a href="tel:${shop.phone ?? ''}" class="dark-btn rounded-2xl py-3 text-center text-sm font-bold">
                        Call
                    </a>
                    <button onclick='openDispatchModal(${JSON.stringify(shop)})' class="brand-btn rounded-2xl py-3 text-sm">
                        Dispatch
                    </button>
                </div>

                <button onclick="openReviewPrompt(${shop.id})" class="w-full dark-btn rounded-2xl py-3 text-sm font-bold mt-3">
                    Leave Review
                </button>
            </div>
        `;
    });
}

function openProfileModal() {
    initProfileInputs();
    document.getElementById('profileModal').classList.remove('hidden');
}

function closeProfileModal() {
    document.getElementById('profileModal').classList.add('hidden');
}

function initProfileInputs() {
    const id = mfIdentity();

    ownerName.value = id.owner_name;
    contactNumber.value = id.contact_number;
    vehicleMakeModel.value = id.vehicle_make_model;
    vehicleVariantColor.value = id.vehicle_variant_color;
    plateTempNumber.value = id.plate_temp_number;
}

function saveProfile() {
    localStorage.setItem('mf_owner_name', ownerName.value.trim());
    localStorage.setItem('mf_contact_number', contactNumber.value.trim());
    localStorage.setItem('mf_vehicle_make_model', vehicleMakeModel.value.trim());
    localStorage.setItem('mf_vehicle_variant_color', vehicleVariantColor.value.trim());
    localStorage.setItem('mf_plate_temp_number', plateTempNumber.value.trim());

    closeProfileModal();
    alert('Motorist info saved.');
}

function openDispatchModal(shop) {
    const id = mfIdentity();

    if (!id.owner_name || !id.contact_number) {
        openProfileModal();
        alert('Please complete your motorist info first.');
        return;
    }

    selectedShop = shop;
    selectedShopId.value = shop.id;
    selectedShopName.innerText = shop.name;
    document.getElementById('dispatchModal').classList.remove('hidden');
}

function closeDispatchModal() {
    document.getElementById('dispatchModal').classList.add('hidden');
}

async function submitDispatch() {
    const id = mfIdentity();

    if (!issueType.value) {
        alert('Please select issue type.');
        return;
    }

    const payload = {
        shop_id: selectedShopId.value,
        guest_token: id.guest_token,
        owner_name: id.owner_name,
        contact_number: id.contact_number,
        vehicle_make_model: id.vehicle_make_model,
        vehicle_variant_color: id.vehicle_variant_color,
        plate_temp_number: id.plate_temp_number,
        issue_type: issueType.value,
        description: description.value,
        location: `Lat: ${userLat}, Lng: ${userLng}`,
        latitude: userLat,
        longitude: userLng
    };

    const res = await fetch('/motorist/dispatch', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        },
        body: JSON.stringify(payload)
    });

    const data = await res.json();

    if (data.success) {
        localStorage.setItem('mf_current_request_id', data.request_id);
        currentRequestId = data.request_id;
        closeDispatchModal();
        pollRequestStatus();
        setInterval(pollRequestStatus, 5000);
        alert('Dispatch request sent to selected motor shop.');
    } else {
        alert('Failed to send request.');
    }
}

async function pollRequestStatus() {
    if (!currentRequestId) return;

    const res = await fetch(`/motorist/request/${currentRequestId}`);

    if (!res.ok) return;

    const data = await res.json();

    statusBox.classList.remove('hidden');
    statusTitle.innerText = data.status.toUpperCase();
    statusDetail.innerText = `${data.shop_name} • ${data.issue_type}`;

    if (['completed', 'done', 'declined'].includes(data.status)) {
        localStorage.removeItem('mf_current_request_id');
    }
}

async function openReviewPrompt(shopId) {
    const id = mfIdentity();

    const rating = prompt('Rate this shop from 1 to 5:');
    if (!rating || rating < 1 || rating > 5) return;

    const comment = prompt('Write your comment:') || '';

    const res = await fetch('/motorist/review', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        },
        body: JSON.stringify({
            shop_id: shopId,
            guest_token: id.guest_token,
            owner_name: id.owner_name || 'Motorist',
            rating: rating,
            comment: comment
        })
    });

    const data = await res.json();

    if (data.success) {
        alert('Review submitted.');
        loadShops();
    }
}
</script>
@endsection