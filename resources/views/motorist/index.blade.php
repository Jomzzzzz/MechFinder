@extends('layouts.motorist')

@section('content')
    <header class="flex justify-between items-center mb-4">
        <div>
            <h1 class="font-black text-2xl tracking-tight">MECHFINDER</h1>
            <p class="text-gray-400 text-xs">Emergency motorcycle repair assistant</p>
        </div>
        <button onclick="openProfileModal()" class="px-3 py-2 rounded-2xl text-sm dark-btn">
            👤
        </button>
    </header>

    <section class="mb-4 p-4 rounded-3xl glass">
        <div class="flex justify-between items-center mb-3">
            <div>
                <p class="text-gray-400 text-xs uppercase tracking-widest">Current Location</p>
                <p id="locationText" class="font-bold text-sm">Detecting your location...</p>
            </div>
            <button onclick="locateUser()" class="px-4 py-2 rounded-2xl text-sm brand-btn">Locate</button>
        </div>
        <div id="map"></div>
    </section>

    <section class="mb-4">
        <div class="flex justify-between items-center mb-3">
            <h2 class="font-black text-lg">Nearby Motor Shops</h2>
            <span id="shopCount" class="text-gray-400 text-xs">Loading...</span>
        </div>
        <div id="shopsList" class="space-y-3"></div>
    </section>

    {{-- Profile Modal --}}
    <div id="profileModal" class="hidden z-50 fixed inset-0 bg-black/80 p-4">
        <div class="mx-auto mt-10 p-5 rounded-3xl max-w-md glass">
            <h2 class="mb-1 font-black text-xl">Your Info</h2>
            <p class="mb-4 text-gray-400 text-xs">Used when requesting a mechanic. Saved on this device only.</p>
            <div class="space-y-3">
                <input id="ownerName" class="bg-black/40 px-4 py-3 border border-white/10 rounded-2xl w-full"
                    placeholder="Your full name">
                <input id="contactNumber" class="bg-black/40 px-4 py-3 border border-white/10 rounded-2xl w-full"
                    placeholder="Contact number">
                <input id="vehicleMakeModel" class="bg-black/40 px-4 py-3 border border-white/10 rounded-2xl w-full"
                    placeholder="Motorcycle make & model (e.g. Yamaha Mio)">
                <input id="vehicleVariantColor" class="bg-black/40 px-4 py-3 border border-white/10 rounded-2xl w-full"
                    placeholder="Color / variant">
                <input id="plateTempNumber" class="bg-black/40 px-4 py-3 border border-white/10 rounded-2xl w-full"
                    placeholder="Plate / temporary number">
            </div>
            <div class="gap-3 grid grid-cols-2 mt-5">
                <button onclick="closeProfileModal()" class="py-3 rounded-2xl font-bold dark-btn">Cancel</button>
                <button onclick="saveProfile()" class="py-3 rounded-2xl brand-btn">Save</button>
            </div>
        </div>
    </div>

    {{-- Dispatch Modal --}}
    <div id="dispatchModal" class="hidden z-50 fixed inset-0 bg-black/80 p-4 overflow-y-auto">
        <div class="mx-auto mt-8 mb-8 p-5 rounded-3xl max-w-md glass">

            {{-- Header --}}
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="font-black text-xl leading-tight">Request a Mechanic</h2>
                    <p id="selectedShopName" class="mt-0.5 font-bold text-orange-400 text-sm"></p>
                </div>
                <button onclick="closeDispatchModal()"
                    class="mt-1 text-gray-500 hover:text-white text-xl leading-none">✕</button>
            </div>

            {{-- How it works --}}
            <div class="bg-orange-500/10 mb-4 p-3 border border-orange-500/20 rounded-2xl">
                <p class="mb-2 font-bold text-orange-300 text-xs uppercase tracking-widest">How it works</p>
                <div class="space-y-1.5 text-gray-300 text-xs">
                    <div class="flex items-center gap-2"><span class="font-black text-orange-400">1.</span> You describe
                        your issue and send the request</div>
                    <div class="flex items-center gap-2"><span class="font-black text-orange-400">2.</span> The shop reviews
                        and accepts your request</div>
                    <div class="flex items-center gap-2"><span class="font-black text-orange-400">3.</span> A mechanic is
                        dispatched to your location</div>
                    <div class="flex items-center gap-2"><span class="font-black text-orange-400">4.</span> You get
                        real-time status updates here</div>
                </div>
            </div>

            <input type="hidden" id="selectedShopId">

            <div class="space-y-3">
                <div>
                    <label class="block mb-1 text-gray-400 text-xs uppercase tracking-widest">What's the problem?</label>
                    <select id="issueType" class="bg-black/40 px-4 py-3 border border-white/10 rounded-2xl w-full">
                        <option value="">Select issue type</option>
                        <option value="Flat Tire">🔧 Flat Tire</option>
                        <option value="Engine Stall">⚙️ Engine Stall</option>
                        <option value="Battery Problem">🔋 Battery Problem</option>
                        <option value="Brake Problem">🛑 Brake Problem</option>
                        <option value="Chain Problem">⛓️ Chain Problem</option>
                        <option value="Other">❓ Other</option>
                    </select>
                </div>
                <div>
                    <label class="block mb-1 text-gray-400 text-xs uppercase tracking-widest">Additional details</label>
                    <textarea id="description" class="bg-black/40 px-4 py-3 border border-white/10 rounded-2xl w-full" rows="3"
                        placeholder="Describe your situation (optional)..."></textarea>
                </div>
            </div>

            <p class="mt-3 text-gray-500 text-xs">📍 Your current GPS location will be shared with the shop.</p>

            <div class="gap-3 grid grid-cols-2 mt-4">
                <button onclick="closeDispatchModal()" class="py-3 rounded-2xl font-bold dark-btn">Cancel</button>
                <button onclick="submitDispatch()" id="submitDispatchBtn" class="py-3 rounded-2xl font-black brand-btn">
                    Send Request
                </button>
            </div>
        </div>
    </div>

    {{-- Active Request Status Box (bottom sheet style) --}}
    <div id="statusBox" class="hidden right-0 bottom-0 left-0 z-40 fixed">
        <div class="mx-auto p-4 max-w-md">
            <div class="p-4 border border-orange-500/20 rounded-3xl glass">

                <div class="flex justify-between items-start mb-3">
                    <div>
                        <p class="font-bold text-orange-400 text-xs uppercase tracking-widest">Active Request</p>
                        <h3 id="statusTitle" class="mt-0.5 font-black text-lg leading-tight">Waiting...</h3>
                        <p id="statusDetail" class="mt-0.5 text-gray-400 text-xs"></p>
                    </div>
                    <button onclick="dismissStatus()" class="text-gray-600 hover:text-gray-400 text-sm">✕</button>
                </div>

                {{-- Step progress --}}
                <div class="flex items-center gap-1 mt-2" id="statusSteps">
                    <div class="flex-1 bg-white/10 rounded-full h-1.5 step-dot" data-step="requested"></div>
                    <div class="flex-1 bg-white/10 rounded-full h-1.5 step-dot" data-step="accepted"></div>
                    <div class="flex-1 bg-white/10 rounded-full h-1.5 step-dot" data-step="en_route"></div>
                    <div class="flex-1 bg-white/10 rounded-full h-1.5 step-dot" data-step="arrived"></div>
                    <div class="flex-1 bg-white/10 rounded-full h-1.5 step-dot" data-step="in_progress"></div>
                    <div class="flex-1 bg-white/10 rounded-full h-1.5 step-dot" data-step="completed"></div>
                </div>
                <div class="flex justify-between mt-1">
                    <span class="text-[9px] text-gray-600">Sent</span>
                    <span class="text-[9px] text-gray-600">Accepted</span>
                    <span class="text-[9px] text-gray-600">En Route</span>
                    <span class="text-[9px] text-gray-600">Arrived</span>
                    <span class="text-[9px] text-gray-600">Working</span>
                    <span class="text-[9px] text-gray-600">Done</span>
                </div>

                <div id="statusDeclinedMsg"
                    class="hidden bg-red-500/10 mt-3 p-3 border border-red-500/20 rounded-xl text-red-300 text-xs">
                    Your request was declined by the shop. Please try another shop nearby.
                    <button onclick="clearDeclinedRequest()"
                        class="ml-2 font-bold text-red-400 underline">Dismiss</button>
                </div>
            </div>
        </div>
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
        let dispatchPusher = null;

        const STATUS_LABELS = {
            requested: 'Waiting for shop to accept...',
            accepted: '✅ Request Accepted!',
            en_route: '🏍️ Mechanic is on the way',
            arrived: '📍 Mechanic has arrived',
            in_progress: '🔧 Repair in progress',
            completed: '✅ All done!',
            declined: '❌ Request declined',
        };

        // Step order for progress bar
        const STEP_ORDER = ['requested', 'accepted', 'en_route', 'arrived', 'in_progress', 'completed'];

        document.addEventListener('DOMContentLoaded', () => {
            initProfileInputs();
            initMap();
            locateUser();

            if (currentRequestId) {
                fetchCurrentStatus(currentRequestId).then(() => {
                    subscribeToDispatch(currentRequestId);
                });
            }
        });

        // ── Pusher ──────────────────────────────────────────────────────────────

        function subscribeToDispatch(requestId) {
            if (!window.pusherKey || !requestId) return;

            if (dispatchPusher) dispatchPusher.disconnect();

            dispatchPusher = new Pusher(window.pusherKey, {
                cluster: window.pusherCluster,
                forceTLS: true,
            });

            const channel = dispatchPusher.subscribe('dispatch-status.' + requestId);

            channel.bind('dispatch.status', (data) => {
                showStatusBox(data.status);

                if (data.status === 'completed') {
                    setTimeout(() => {
                        localStorage.removeItem('mf_current_request_id');
                        currentRequestId = null;
                        if (dispatchPusher) dispatchPusher.disconnect();
                        document.getElementById('statusBox').classList.add('hidden');
                    }, 8000);
                }
            });
        }

        async function fetchCurrentStatus(requestId) {
            try {
                const res = await fetch(`/motorist/request/${requestId}`);
                if (!res.ok) return;
                const data = await res.json();
                showStatusBox(data.status, data.shop_name, data.issue_type);
            } catch (e) {
                /* ignore */ }
        }

        function showStatusBox(status, shopName, issueType) {
            const box = document.getElementById('statusBox');
            const title = document.getElementById('statusTitle');
            const detail = document.getElementById('statusDetail');
            const declined = document.getElementById('statusDeclinedMsg');

            box.classList.remove('hidden');
            title.innerText = STATUS_LABELS[status] || status.replace(/_/g, ' ').toUpperCase();

            if (shopName || issueType) {
                detail.innerText = [shopName, issueType].filter(Boolean).join(' • ');
            }

            // Update step progress bar
            const stepIndex = STEP_ORDER.indexOf(status);
            document.querySelectorAll('#statusSteps .step-dot').forEach((dot, i) => {
                dot.style.background = i <= stepIndex ?
                    'linear-gradient(90deg, #ffbf2f, #f7941d)' :
                    'rgba(255,255,255,0.1)';
            });

            // Show declined message
            if (status === 'declined') {
                declined.classList.remove('hidden');
                // Reset steps to empty for declined
                document.querySelectorAll('#statusSteps .step-dot').forEach(dot => {
                    dot.style.background = 'rgba(255, 100, 100, 0.3)';
                });
            } else {
                declined.classList.add('hidden');
            }
        }

        function dismissStatus() {
            document.getElementById('statusBox').classList.add('hidden');
        }

        function clearDeclinedRequest() {
            localStorage.removeItem('mf_current_request_id');
            currentRequestId = null;
            if (dispatchPusher) dispatchPusher.disconnect();
            document.getElementById('statusBox').classList.add('hidden');
        }

        // ── Map ─────────────────────────────────────────────────────────────────

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

                if (userMarker) map.removeLayer(userMarker);
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

        // ── Shop list ────────────────────────────────────────────────────────────

        async function loadShops() {
            const res = await fetch(`/motorist/shops?lat=${userLat}&lng=${userLng}`);
            const shops = await res.json();

            document.getElementById('shopCount').innerText = `${shops.length} shops`;

            shopMarkers.forEach(m => map.removeLayer(m));
            shopMarkers = [];

            const list = document.getElementById('shopsList');
            list.innerHTML = '';

            shops.forEach(shop => {
                if (shop.latitude && shop.longitude) {
                    const m = L.marker([shop.latitude, shop.longitude])
                        .addTo(map)
                        .bindPopup(`<b>${shop.shop_name ?? shop.name}</b><br>${shop.address ?? ''}`);
                    shopMarkers.push(m);
                }

                const isOpen = shop.status === 'open';
                const shopName = shop.shop_name ?? shop.name ?? 'Shop';

                list.innerHTML += `
                <div class="rounded-3xl overflow-hidden glass">

                    {{-- Shop header --}}
                    <div class="flex justify-between items-start p-4 pb-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-black text-base">${shopName}</h3>
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-bold ${isOpen ? 'bg-green-500/20 text-green-300' : 'bg-gray-500/20 text-gray-400'}">
                                    ${isOpen ? '● OPEN' : '● CLOSED'}
                                </span>
                            </div>
                            <p class="mt-0.5 text-gray-400 text-xs truncate">${shop.address ?? 'No address'}</p>
                            <div class="flex items-center gap-3 mt-1 text-gray-400 text-xs">
                                <span>⭐ ${Number(shop.rating ?? 0).toFixed(1)} <span class="text-gray-600">(${shop.review_count ?? 0})</span></span>
                                ${shop.distance ? `<span>📍 ${shop.distance} km</span>` : ''}
                                ${shop.eta ? `<span>⏱ ~${shop.eta} min</span>` : ''}
                            </div>
                        </div>
                    </div>

                    ${isOpen ? `
                        {{-- Primary CTA: Request mechanic --}}
                        <div class="px-4 pb-2">
                            <button onclick='openDispatchModal(${JSON.stringify(shop)})'
                                class="flex justify-center items-center gap-2 py-3 rounded-2xl w-full font-black text-sm brand-btn">
                                🔧 Request a Mechanic
                            </button>
                            <p class="mt-1.5 text-[10px] text-gray-500 text-center">Shop will review your request and send a mechanic to you</p>
                        </div>
                        ` : `
                        <div class="px-4 pb-2">
                            <div class="bg-white/5 py-3 border border-white/10 rounded-2xl w-full font-bold text-gray-500 text-sm text-center">
                                Shop is currently closed
                            </div>
                        </div>
                        `}

                    {{-- Secondary actions --}}
                    <div class="flex gap-2 mt-1 px-4 pb-4">
                        ${shop.latitude && shop.longitude ? `
                            <button onclick="getDirections(${shop.latitude}, ${shop.longitude}, '${(shopName).replace(/'/g, "\\'")}')"
                                class="flex flex-1 justify-center items-center gap-1.5 py-2.5 rounded-2xl font-bold text-xs dark-btn">
                                🗺️ Get Directions
                            </button>
                            ` : ''}
                        ${shop.phone ? `
                            <a href="tel:${shop.phone}"
                                class="flex flex-1 justify-center items-center gap-1.5 py-2.5 rounded-2xl font-bold text-xs dark-btn">
                                📞 Call Shop
                            </a>
                            ` : ''}
                        <button onclick="openReviewPrompt(${shop.id})"
                            class="flex flex-1 justify-center items-center gap-1.5 py-2.5 rounded-2xl font-bold text-xs dark-btn">
                            ⭐ Review
                        </button>
                    </div>
                </div>`;
            });
        }

        // ── Directions ───────────────────────────────────────────────────────────

        function getDirections(lat, lng, name) {
            // Opens Google Maps directions from user location to shop
            const origin = `${userLat},${userLng}`;
            const dest = `${lat},${lng}`;
            const url = `https://www.google.com/maps/dir/?api=1&origin=${origin}&destination=${dest}&travelmode=driving`;
            window.open(url, '_blank');
        }

        // ── Profile modal ────────────────────────────────────────────────────────

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
        }

        // ── Dispatch modal ───────────────────────────────────────────────────────

        function openDispatchModal(shop) {
            const id = mfIdentity();

            if (!id.owner_name || !id.contact_number) {
                openProfileModal();
                return;
            }

            selectedShop = shop;
            document.getElementById('selectedShopId').value = shop.id;
            document.getElementById('selectedShopName').innerText = shop.shop_name ?? shop.name ?? '';
            document.getElementById('issueType').value = '';
            document.getElementById('description').value = '';
            document.getElementById('dispatchModal').classList.remove('hidden');
        }

        function closeDispatchModal() {
            document.getElementById('dispatchModal').classList.add('hidden');
        }

        async function submitDispatch() {
            const id = mfIdentity();

            if (!document.getElementById('issueType').value) {
                alert('Please select an issue type.');
                return;
            }

            const btn = document.getElementById('submitDispatchBtn');
            btn.disabled = true;
            btn.innerText = 'Sending...';

            const payload = {
                shop_id: document.getElementById('selectedShopId').value,
                guest_token: id.guest_token,
                owner_name: id.owner_name,
                contact_number: id.contact_number,
                vehicle_make_model: id.vehicle_make_model,
                vehicle_variant_color: id.vehicle_variant_color,
                plate_temp_number: id.plate_temp_number,
                issue_type: document.getElementById('issueType').value,
                description: document.getElementById('description').value,
                location: `Lat: ${userLat}, Lng: ${userLng}`,
                latitude: userLat,
                longitude: userLng,
            };

            try {
                const res = await fetch('/motorist/dispatch', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.csrfToken
                    },
                    body: JSON.stringify(payload),
                });

                const data = await res.json();

                if (data.success) {
                    localStorage.setItem('mf_current_request_id', data.request_id);
                    currentRequestId = data.request_id;
                    closeDispatchModal();
                    showStatusBox('requested', selectedShop?.shop_name ?? selectedShop?.name, payload.issue_type);
                    subscribeToDispatch(data.request_id);
                } else {
                    alert('Failed to send request. Please try again.');
                }
            } catch (e) {
                alert('Network error. Please check your connection.');
            } finally {
                btn.disabled = false;
                btn.innerText = 'Send Request';
            }
        }

        // ── Review ───────────────────────────────────────────────────────────────

        async function openReviewPrompt(shopId) {
            const id = mfIdentity();

            const rating = prompt('Rate this shop (1–5):');
            if (!rating || rating < 1 || rating > 5) return;

            const comment = prompt('Leave a comment (optional):') || '';

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
                    rating,
                    comment,
                }),
            });

            const data = await res.json();
            if (data.success) {
                alert('Review submitted. Thank you!');
                loadShops();
            }
        }
    </script>
@endsection
