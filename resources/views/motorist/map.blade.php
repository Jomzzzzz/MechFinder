@extends('layouts.motorist')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-black via-gray-900 to-gray-950 text-white flex flex-col">

    <!-- Header -->
    <div class="sticky top-0 bg-gray-900 border-b border-orange-500/20 px-4 py-4 z-10">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-2xl">⚙</span>
                <h1 class="text-xl font-bold">MechFinder</h1>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></span>
                <span class="text-xs text-gray-400">Live</span>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="px-4 py-3">
        <div class="flex gap-2">
            <input
                type="text"
                id="search-bar"
                placeholder="Search area or shop name..."
                class="flex-1 bg-gray-800 text-white px-4 py-2 rounded-lg border border-gray-700 focus:border-orange-500 outline-none text-sm"
            >
            <button
                id="refresh-btn"
                class="px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm hover:border-orange-500"
            >
                Refresh
            </button>
        </div>
    </div>

    <!-- Map -->
    <div class="px-4 py-2 relative">
        <div id="map" class="w-full h-72 rounded-2xl border border-white/10 shadow-lg"></div>

        <!-- Floating Legend -->
        <div class="absolute bottom-4 right-6 bg-black/70 backdrop-blur px-3 py-2 rounded-xl text-xs border border-white/10">
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2 h-2 bg-blue-500 rounded-full"></span> You
            </div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2 h-2 bg-orange-500 rounded-full"></span> Shop
            </div>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-red-500 rounded-full"></span> Recommended
            </div>
        </div>
    </div>

    <!-- Recommendation summary -->
    <div class="px-4 pt-3">
        <div id="ai-summary" class="text-xs text-orange-300 bg-orange-500/10 border border-orange-500/20 rounded-xl px-3 py-2 hidden"></div>
    </div>

    <!-- Shops Title -->
    <div class="px-4 py-3">
        <h3 class="text-sm font-semibold text-gray-300">
            NEARBY SHOPS
            <span class="text-xs text-gray-500" id="shop-count">(0 FOUND)</span>
        </h3>
    </div>

    <!-- Shop List -->
    <div class="flex-1 overflow-y-auto px-4 pb-6">
        <ul id="shop-list" class="space-y-3"></ul>
    </div>

</div>
@endsection

@push('scripts')
<script>
    let map;
    let userMarker = null;
    let infoWindow = null;
    let directionsService = null;
    let directionsRenderer = null;

    let userLat = 14.8386;
    let userLng = 120.2842;

    let allShops = [];
    let shopMarkers = [];

    async function initMap() {
        const { Map, InfoWindow } = await google.maps.importLibrary("maps");
        const { AdvancedMarkerElement, PinElement } = await google.maps.importLibrary("marker");
        const { Place } = await google.maps.importLibrary("places");

        map = new Map(document.getElementById("map"), {
            center: { lat: userLat, lng: userLng },
            zoom: 14,
            mapId: "DEMO_MAP_ID"
        });

        infoWindow = new InfoWindow();
        directionsService = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer({
            suppressMarkers: true
        });
        directionsRenderer.setMap(map);

        await detectUserLocation();

        map.setCenter({ lat: userLat, lng: userLng });

        const userPin = new PinElement({
            background: "#3b82f6",
            borderColor: "#1d4ed8",
            glyphColor: "#ffffff"
        });

        userMarker = new AdvancedMarkerElement({
            map,
            position: { lat: userLat, lng: userLng },
            title: "You are here",
            content: userPin.element
        });

        await loadNearbyShops();

        document.getElementById("refresh-btn").addEventListener("click", async () => {
            await detectUserLocation();
            map.setCenter({ lat: userLat, lng: userLng });
            userMarker.position = { lat: userLat, lng: userLng };
            await loadNearbyShops();
        });

        document.getElementById("search-bar").addEventListener("input", function(e) {
            const value = e.target.value.toLowerCase().trim();

            document.querySelectorAll("#shop-list li").forEach(li => {
                li.style.display = li.innerText.toLowerCase().includes(value) ? "block" : "none";
            });

            shopMarkers.forEach(markerObj => {
                const visible =
                    !value ||
                    markerObj.shop.displayName.toLowerCase().includes(value) ||
                    (markerObj.shop.formattedAddress || "").toLowerCase().includes(value);

                markerObj.marker.map = visible ? map : null;
            });
        });
    }

    async function detectUserLocation() {
        return new Promise((resolve) => {
            if (!navigator.geolocation) {
                resolve();
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    userLat = position.coords.latitude;
                    userLng = position.coords.longitude;
                    resolve();
                },
                () => resolve(),
                {
                    enableHighAccuracy: true,
                    timeout: 10000
                }
            );
        });
    }

    async function loadNearbyShops() {
        const { Place } = await google.maps.importLibrary("places");
        const { AdvancedMarkerElement, PinElement } = await google.maps.importLibrary("marker");

        clearShopsUI();

        const request = {
            fields: [
                "displayName",
                "formattedAddress",
                "location",
                "rating",
                "userRatingCount",
                "businessStatus",
                "googleMapsURI",
                "regularOpeningHours",
                "primaryType",
                "types"
            ],
            locationRestriction: {
                center: { lat: userLat, lng: userLng },
                radius: 5000
            },
            includedPrimaryTypes: ["car_repair", "tire_shop"],
            maxResultCount: 20,
            rankPreference: "DISTANCE"
        };

        const { places } = await Place.searchNearby(request);

        if (!places || !places.length) {
            document.getElementById("shop-count").textContent = "(0 FOUND)";
            document.getElementById("shop-list").innerHTML = `
                <li class="bg-white/5 border border-white/10 rounded-2xl p-4 text-sm text-gray-400">
                    No nearby repair shops found.
                </li>
            `;
            document.getElementById("ai-summary").classList.add("hidden");
            return;
        }

        let normalized = places
            .map(place => normalizeShop(place))
            .filter(shop => isRelevantRepairShop(shop));

        normalized.forEach(shop => {
            shop.aiScore = computeAIScore(shop);
        });

        normalized.sort((a, b) => b.aiScore - a.aiScore);
        allShops = normalized;

        document.getElementById("shop-count").textContent = `(${allShops.length} FOUND)`;

        if (allShops.length > 0) {
            const best = allShops[0];
            const summary = document.getElementById("ai-summary");
            summary.classList.remove("hidden");
            summary.innerHTML = `
                <strong>AI Recommendation:</strong>
                ${escapeHtml(best.displayName)}
                is the top nearby option based on distance, rating, review count, and availability.
            `;
        }

        allShops.forEach((shop, index) => {
            const pin = new PinElement({
                background: index === 0 ? "#ef4444" : "#f59e0b",
                borderColor: index === 0 ? "#b91c1c" : "#c2410c",
                glyphColor: "#ffffff"
            });

            const marker = new AdvancedMarkerElement({
                map,
                position: shop.position,
                title: shop.displayName,
                content: pin.element
            });

            marker.addListener("click", () => {
                openInfoWindow(shop);
                focusShop(shop.position.lat, shop.position.lng);
            });

            shopMarkers.push({ marker, shop });
            addShopToList(shop, index === 0);
        });
    }

    function normalizeShop(place) {
        const rating = Number(place.rating || 0);
        const reviewCount = Number(place.userRatingCount || 0);
        const distanceKm = calculateDistanceKm(
            userLat,
            userLng,
            place.location.lat(),
            place.location.lng()
        );

        const openNow =
            place.regularOpeningHours &&
            typeof place.regularOpeningHours.openNow === "boolean"
                ? place.regularOpeningHours.openNow
                : null;

        return {
            place,
            placeId: place.id || null,
            displayName: place.displayName || "Unknown Shop",
            formattedAddress: place.formattedAddress || "No address available",
            rating,
            reviewCount,
            openNow,
            businessStatus: place.businessStatus || "UNKNOWN",
            googleMapsURI: place.googleMapsURI || null,
            primaryType: place.primaryType || "",
            types: place.types || [],
            distanceKm,
            etaMin: Math.max(3, Math.round((distanceKm / 25) * 60)),
            position: {
                lat: place.location.lat(),
                lng: place.location.lng()
            }
        };
    }

    function isRelevantRepairShop(shop) {
        const haystack = (
            shop.displayName + " " +
            shop.formattedAddress + " " +
            shop.primaryType + " " +
            (shop.types || []).join(" ")
        ).toLowerCase();

        const keywords = [
            "repair",
            "auto",
            "motor",
            "motorcycle",
            "shop",
            "garage",
            "mechanic",
            "tire",
            "vulcanizing"
        ];

        return keywords.some(word => haystack.includes(word));
    }

    function computeAIScore(shop) {
        const distanceScore = Math.max(0, 40 - (shop.distanceKm * 8));
        const ratingScore = (shop.rating || 0) * 10;
        const reviewScore = Math.min(20, Math.log10((shop.reviewCount || 0) + 1) * 10);
        const openScore = shop.openNow === true ? 20 : (shop.openNow === false ? 0 : 8);
        const activeScore = shop.businessStatus === "OPERATIONAL" ? 10 : 0;

        return distanceScore + ratingScore + reviewScore + openScore + activeScore;
    }

    function clearShopsUI() {
        shopMarkers.forEach(item => {
            item.marker.map = null;
        });
        shopMarkers = [];
        allShops = [];
        directionsRenderer.set("directions", null);
        document.getElementById("shop-list").innerHTML = "";
    }

    function addShopToList(shop, isTopPick = false) {
        const ul = document.getElementById("shop-list");
        const li = document.createElement("li");

        let statusBadge = `<span class="bg-gray-600 text-xs px-2 py-1 rounded-full">UNKNOWN</span>`;
        if (shop.openNow === true) {
            statusBadge = `<span class="bg-green-600 text-xs px-2 py-1 rounded-full">OPEN</span>`;
        } else if (shop.openNow === false) {
            statusBadge = `<span class="bg-red-600 text-xs px-2 py-1 rounded-full">CLOSED</span>`;
        }

        const topPickBadge = isTopPick
            ? `<span class="bg-red-600 text-white text-[10px] px-2 py-1 rounded-full ml-2">TOP PICK</span>`
            : "";

        li.className = `
            bg-white/5 backdrop-blur border border-white/10
            rounded-2xl p-4 cursor-pointer
            hover:border-orange-500/40 transition
        `;

        li.innerHTML = `
            <div class="flex justify-between mb-3 gap-3">
                <div>
                    <div class="font-bold text-lg">
                        ${escapeHtml(shop.displayName)}
                        ${topPickBadge}
                    </div>
                    <div class="text-gray-400 text-xs">${escapeHtml(shop.formattedAddress)}</div>
                </div>
                ${statusBadge}
            </div>

            <div class="flex justify-between text-sm mb-3">
                <div class="flex gap-3">
                    <span class="text-yellow-400">⭐ ${shop.rating ? shop.rating.toFixed(1) : "N/A"}</span>
                    <span class="text-gray-400">(${shop.reviewCount || 0})</span>
                </div>
                <div class="text-blue-400">
                    📍 ${shop.distanceKm.toFixed(1)} km • ⏱ ${shop.etaMin} min
                </div>
            </div>

            <div class="flex gap-2">
                <button
                    class="flex-1 px-3 py-2 bg-gray-700 rounded-lg text-sm"
                    onclick='showDirections(${shop.position.lat}, ${shop.position.lng})'
                >
                    DIRECTIONS
                </button>

                <button
                    class="flex-1 px-3 py-2 bg-orange-600 rounded-lg text-white text-sm"
                    onclick='openGooglePlace("${encodeURIComponent(shop.googleMapsURI || "")}")'
                >
                    VIEW
                </button>
            </div>
        `;

        li.addEventListener("click", () => {
            focusShop(shop.position.lat, shop.position.lng);
            openInfoWindow(shop);
        });

        ul.appendChild(li);
    }

    function openInfoWindow(shop) {
        infoWindow.setContent(`
            <div style="color:#111; min-width:220px;">
                <div style="font-weight:700; margin-bottom:6px;">${escapeHtml(shop.displayName)}</div>
                <div style="font-size:12px; margin-bottom:6px;">${escapeHtml(shop.formattedAddress)}</div>
                <div style="font-size:12px;">
                    Rating: ${shop.rating ? shop.rating.toFixed(1) : "N/A"} |
                    Reviews: ${shop.reviewCount || 0}
                </div>
            </div>
        `);

        infoWindow.setPosition(shop.position);
        infoWindow.open(map);
    }

    function focusShop(lat, lng) {
        map.panTo({ lat, lng });
        map.setZoom(16);
    }

    async function showDirections(destLat, destLng) {
        directionsService.route(
            {
                origin: { lat: userLat, lng: userLng },
                destination: { lat: destLat, lng: destLng },
                travelMode: google.maps.TravelMode.DRIVING
            },
            (result, status) => {
                if (status === "OK") {
                    directionsRenderer.setDirections(result);
                } else {
                    alert("Unable to load directions.");
                }
            }
        );
    }

    function openGooglePlace(encodedUrl) {
        const url = decodeURIComponent(encodedUrl || "");
        if (url) {
            window.open(url, "_blank");
        }
    }

    function calculateDistanceKm(lat1, lon1, lat2, lon2) {
        const earthRadius = 6371;
        const dLat = deg2rad(lat2 - lat1);
        const dLon = deg2rad(lon2 - lon1);

        const a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(deg2rad(lat1)) *
            Math.cos(deg2rad(lat2)) *
            Math.sin(dLon / 2) *
            Math.sin(dLon / 2);

        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return earthRadius * c;
    }

    function deg2rad(deg) {
        return deg * (Math.PI / 180);
    }

    function escapeHtml(str) {
        if (!str) return "";
        return str
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    window.showDirections = showDirections;
    window.openGooglePlace = openGooglePlace;
    window.initMap = initMap;
</script>

<script
    async
    src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&loading=async&callback=initMap&v=weekly">
</script>
@endpush