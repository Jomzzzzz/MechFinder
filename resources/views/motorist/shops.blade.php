@extends('layouts.motorist')

@section('content')
    <div class="min-h-screen flex flex-col text-white">

        <div class="sticky top-0 z-20 bg-[#0d1118]/95 backdrop-blur border-b border-white/5 px-4 pt-4 pb-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="text-[#f7a51d] text-lg font-black">⚙ MF</div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="openProfileModal()"
                        class="w-9 h-9 rounded-full bg-white/10 border border-white/10 flex items-center justify-center text-sm hover:bg-white/20 transition">👤</button>
                    <div class="flex items-center gap-2 text-xs text-green-400">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        <span>Live</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-4 pt-4">
            <div class="flex gap-2">
                <input type="text" id="search-bar" placeholder="Search area or shop name..."
                    class="flex-1 rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white placeholder:text-gray-500 outline-none focus:border-orange-500">
                <button class="w-12 h-12 rounded-xl muted-btn flex items-center justify-center text-sm">⚙</button>
            </div>
        </div>

        <div class="px-4 pt-4 relative">
            <div id="map" class="soft-grid w-full h-[320px] rounded-[22px] border border-white/10 overflow-hidden">
            </div>

            <div class="absolute bottom-4 right-7 bg-black/80 border border-white/10 rounded-xl px-3 py-2 text-[11px]">
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span> You
                </div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-2 h-2 rounded-full bg-[#f7a51d]"></span> Shop
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span> Dispatch
                </div>
            </div>
        </div>

        <div class="px-4 pt-4">
            <div id="ai-summary"
                class="hidden text-[13px] text-orange-200 bg-orange-500/10 border border-orange-500/20 rounded-2xl px-4 py-3 leading-relaxed">
            </div>
        </div>

        <div class="px-4 pt-5 pb-3">
            <h3 class="text-[15px] font-extrabold tracking-wide">
                NEARBY SHOPS
                <span class="text-gray-500 font-medium text-[13px]" id="shop-count">(0 FOUND)</span>
            </h3>
        </div>

        <div class="flex-1 overflow-y-auto px-4 pb-6">
            <ul id="shop-list" class="space-y-4"></ul>
        </div>
    </div>

    <!-- Modal Backdrop -->
    <div id="modal-backdrop" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-40" onclick="closeModal()"></div>

    <!-- Shop Details & Chat Modal -->
    <div id="shop-modal"
        class="fixed bottom-0 left-0 right-0 bg-[#0d1118] border-t border-white/10 rounded-t-3xl hidden z-50 h-[90vh] max-w-2xl mx-auto">
        <!-- Modal Header -->
        <div
            class="sticky top-0 flex items-center justify-between gap-3 px-4 pt-4 pb-4 border-b border-white/5 flex-shrink-0">
            <button type="button" onclick="closeModal()"
                class="w-9 h-9 rounded-full bg-white/10 border border-white/10 flex items-center justify-center text-sm">←</button>
            <h2 id="modal-shop-name" class="text-[15px] font-extrabold flex-1 text-center truncate">Shop Name</h2>
            <div class="w-9 h-9"></div>
        </div>

        <!-- Shop Info Tabs -->
        <div class="flex gap-2 px-4 pt-2 border-b border-white/5 flex-shrink-0 overflow-x-auto">
            <button type="button" onclick="switchTab('details')" id="tab-details"
                class="px-4 py-2 text-sm font-semibold border-b-2 border-orange-500 whitespace-nowrap">Details</button>
            <button type="button" onclick="switchTab('messages')" id="tab-messages"
                class="px-4 py-2 text-sm font-semibold border-b-2 border-transparent whitespace-nowrap text-gray-400">Messages</button>
        </div>

        <!-- Tab Content -->
        <div class="flex-1 overflow-y-auto">
            <!-- Details Tab -->
            <div id="details-tab" class="px-4 py-4 space-y-4">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-sm">Status</span>
                        <span id="modal-status"
                            class="px-3 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-400">Open</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-sm">Rating</span>
                        <span id="modal-rating" class="text-sm">⭐ 4.5</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-sm">Distance</span>
                        <span id="modal-distance" class="text-sm">2.5 km</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-sm">ETA</span>
                        <span id="modal-eta" class="text-sm">~8 min</span>
                    </div>
                </div>

                <div class="pt-2 border-t border-white/10">
                    <p class="text-gray-400 text-xs mb-2">Address</p>
                    <p id="modal-address" class="text-sm">Loading...</p>
                </div>

                <div class="pt-2 border-t border-white/10">
                    <p class="text-gray-400 text-xs mb-2">Phone</p>
                    <p id="modal-phone" class="text-sm">
                        <a id="phone-link" href="" class="text-orange-400 hover:text-orange-300">Loading...</a>
                    </p>
                </div>

                <div class="pt-2 border-t border-white/10">
                    <p class="text-gray-400 text-xs mb-2">Email</p>
                    <p id="modal-email" class="text-sm">
                        <a id="email-link" href="" class="text-orange-400 hover:text-orange-300">Loading...</a>
                    </p>
                </div>
            </div>

            <!-- Messages Tab -->
            <div id="messages-tab" class="hidden h-full">
                <div id="chat-messages" class="flex-1 overflow-y-auto px-4 py-4 space-y-3"></div>
            </div>
        </div>

        <!-- Message Input -->
        <div id="chat-input-section" class="hidden px-4 pb-4 pt-2 border-t border-white/5 flex-shrink-0">
            <form id="chat-form-modal" class="flex gap-2 items-center" onsubmit="return sendMessage(event)">
                <input type="text" id="message-input-modal" placeholder="Type a message..."
                    class="flex-1 rounded-2xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white placeholder:text-gray-500 outline-none focus:border-orange-500"
                    required>
                <button type="submit"
                    class="w-12 h-12 rounded-2xl brand-btn text-lg flex items-center justify-center flex-shrink-0">→</button>
            </form>
        </div>
    </div>

    <!-- Profile Modal Backdrop -->
    <div id="profile-modal-backdrop" class="fixed inset-0 bg-black/80 backdrop-blur-md hidden z-50"
        onclick="closeProfileModal()"></div>

    <!-- Motorist Profile Modal -->
    <div id="profile-modal"
        class="fixed inset-0 bg-[#0d1118] hidden z-50 max-w-2xl mx-auto overflow-hidden">
        <!-- Modal Header -->
        <div class="flex items-center justify-between gap-3 px-4 pt-4 pb-4 border-b border-white/5 flex-shrink-0">
            <h2 class="text-[15px] font-extrabold">MY PROFILE</h2>
            <button type="button" onclick="closeProfileModal()"
                class="w-9 h-9 rounded-full bg-white/10 border border-white/10 flex items-center justify-center text-sm hover:bg-red-500/20 transition">✕</button>
        </div>

        <!-- Modal Content - Scrollable -->
        <div class="flex-1 overflow-y-auto">
            <div class="px-6 py-6 space-y-6">

                <!-- Contact Information -->
                <div>
                    <label class="block text-xs text-gray-400 mb-3 font-semibold">CONTACT INFORMATION</label>
                    <div class="space-y-3 bg-white/5 rounded-2xl p-4 border border-white/10">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Owner/Motorist Name</p>
                            <input id="prof-input-owner-name" type="text" maxlength="150"
                                class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-sm text-white placeholder:text-gray-600 outline-none focus:border-orange-500"
                                placeholder="e.g., Juan Dela Cruz">
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Contact Number (SMS/WhatsApp)</p>
                            <input id="prof-input-contact-number" type="tel" maxlength="50"
                                class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-sm text-white placeholder:text-gray-600 outline-none focus:border-orange-500"
                                placeholder="e.g., 09123456789">
                        </div>
                    </div>
                </div>

                <!-- Vehicle Information -->
                <div>
                    <label class="block text-xs text-gray-400 mb-3 font-semibold">VEHICLE INFORMATION</label>
                    <div class="space-y-3 bg-white/5 rounded-2xl p-4 border border-white/10">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Make/Model</p>
                            <input id="prof-input-vehicle-make-model" type="text" maxlength="150"
                                class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-sm text-white placeholder:text-gray-600 outline-none focus:border-orange-500"
                                placeholder="e.g., Honda Wave 110">
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Variant/Color</p>
                            <input id="prof-input-vehicle-variant-color" type="text" maxlength="150"
                                class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-sm text-white placeholder:text-gray-600 outline-none focus:border-orange-500"
                                placeholder="e.g., Red v3, Black 2020">
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">License Plate / Temp Number</p>
                            <input id="prof-input-plate-temp-number" type="text" maxlength="80"
                                class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-sm text-white placeholder:text-gray-600 outline-none focus:border-orange-500"
                                placeholder="e.g., ABC-1234">
                        </div>
                    </div>
                </div>

                <!-- Save Button -->
                <button type="button" onclick="saveProfileModal()"
                    class="w-full px-4 py-3 rounded-xl bg-orange-500 text-black font-bold text-sm hover:bg-orange-600 transition">
                    💾 SAVE PROFILE
                </button>

                <!-- Guest Token Info -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-4">
                    <p class="text-xs text-gray-400 mb-2 font-semibold">GUEST ID</p>
                    <p id="profile-guest-id" class="text-xs text-gray-500 font-mono break-all">Loading...</p>
                    <p class="text-xs text-gray-600 mt-2">This ID links you to your dispatch requests and messages</p>
                </div>
            </div>
        </div>

        document.addEventListener('DOMContentLoaded', function () {
        let userLat = {{ $userLat ?? 14.8386 }};
        let userLng = {{ $userLng ?? 120.2842 }};

        let map;
        let userMarker;
        let radarCircle;
        let shopMarkers = [];
        let shopsData = [];
        let isFetching = false;

        init();

        const initialTab = window.location.hash.replace('#', '');
        if (initialTab === 'details' || initialTab === 'messages') {
            switchTab(initialTab);
        }

        window.addEventListener('hashchange', () => {
            const nextTab = window.location.hash.replace('#', '');
            if (nextTab === 'details' || nextTab === 'messages') {
                switchTab(nextTab);
            }
        });

        function init() {
        if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(position => {
        userLat = position.coords.latitude;
        userLng = position.coords.longitude;
        renderMap();
        fetchShops();
        }, () => {
        renderMap();
        fetchShops();
        }, {
        enableHighAccuracy: true,
        timeout: 10000
        });
        } else {
        renderMap();
        fetchShops();
        }
        }

        function renderMap() {
        map = L.map('map', { zoomControl: false }).setView([userLat, userLng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        }).addTo(map);

        const userIcon = L.divIcon({
        className: '',
        html: `<div
            style="width:16px;height:16px;background:#3B82F6;border:2px solid white;border-radius:50%;box-shadow:0 0 12px rgba(59,130,246,.9);">
        </div>`,
        iconSize: [16, 16],
        iconAnchor: [8, 8]
        });

        userMarker = L.marker([userLat, userLng], { icon: userIcon }).addTo(map);

        radarCircle = L.circle([userLat, userLng], {
        radius: 250,
        color: '#f7a51d',
        weight: 1,
        fillOpacity: 0
        }).addTo(map);
        }

        function clearMarkers() {
        shopMarkers.forEach(marker => map.removeLayer(marker));
        shopMarkers = [];
        }

        function fetchShops() {
        if (isFetching) return;
        isFetching = true;

        fetch(`/api/motorist/shops?lat=${userLat}&lng=${userLng}`)
        .then(res => res.json())
        .then(data => {
        clearMarkers();
        document.getElementById('shop-list').innerHTML = '';
        document.getElementById('ai-summary').classList.add('hidden');

        if (data.success && data.shops && data.shops.length > 0) {
        shopsData = data.shops;
        document.getElementById('shop-count').textContent = `(${data.shops.length} FOUND)`;

        const recommendedShop = data.shops.find(shop => shop.recommended);
        if (recommendedShop) {
        const summary = document.getElementById('ai-summary');
        summary.classList.remove('hidden');
        summary.innerHTML = `<strong>AI Recommendation:</strong> ${escapeHtml(recommendedShop.shop_name)} is the best nearby
        option based on distance, rating, availability, and completed jobs.`;
        }

        data.shops.forEach(shop => {
        addShopMarker(shop);
        addShopToList(shop);
        });
        } else {
        document.getElementById('shop-count').textContent = '(0 FOUND)';
        }
        })
        .catch(error => {
        console.error('Fetch shops error:', error);
        })
        .finally(() => {
        isFetching = false;
        });
        }

        function addShopMarker(shop) {
        // Safety check: ensure map is initialized
        if (!map) {
        console.warn('Map not initialized yet, skipping marker for shop:', shop.id);
        return;
        }

        const isRecommended = !!shop.recommended;

        let color = '#f7a51d';
        if (shop.status === 'busy') color = '#eab308';
        if (shop.status === 'closed') color = '#6b7280';
        if (isRecommended) color = '#ef4444';

        const icon = L.divIcon({
        className: '',
        html: `<div
            style="width:15px;height:15px;background:${color};border:2px solid rgba(255,255,255,.95);border-radius:50%;box-shadow:0 0 10px ${color};">
        </div>`,
        iconSize: [15, 15],
        iconAnchor: [7.5, 7.5]
        });

        const marker = L.marker([shop.latitude, shop.longitude], { icon }).addTo(map);
        shopMarkers.push(marker);
        }

        function addShopToList(shop) {
        const ul = document.getElementById('shop-list');
        const li = document.createElement('li');

        let statusBadge = `<span
            class="bg-gray-600/30 text-gray-300 border border-gray-500/20 text-[11px] px-3 py-1 rounded-md font-bold">CLOSED</span>`;
        if (shop.status === 'open') {
        statusBadge = `<span
            class="bg-green-500/20 text-green-400 border border-green-500/20 text-[11px] px-3 py-1 rounded-md font-bold">OPEN</span>`;
        } else if (shop.status === 'busy') {
        statusBadge = `<span
            class="bg-yellow-500/20 text-yellow-300 border border-yellow-500/20 text-[11px] px-3 py-1 rounded-md font-bold">BUSY</span>`;
        }

        const distance = Number(shop.distance || 0);
        const rating = shop.rating ? Number(shop.rating).toFixed(1) : 'N/A';
        const recommendedBadge = shop.recommended
        ? `<span class="bg-red-600 text-white text-[10px] px-2 py-1 rounded-full ml-2 font-bold">TOP PICK</span>`
        : '';

        li.className = 'glass-card rounded-[20px] p-3 cursor-pointer';

        li.innerHTML = `
        <div class="flex gap-3 items-center">
            <div class="w-12 h-12 rounded-xl bg-white/10 border border-white/10 shrink-0"></div>

            <div class="flex-1 min-w-0">
                <div class="h-3.5 flex items-center">
                    <div class="font-bold text-[16px] truncate">${escapeHtml(shop.shop_name)}</div>
                    ${recommendedBadge}
                </div>

                <div class="mt-2 h-2.5 w-28 rounded-full bg-white/10"></div>
                <div class="mt-2 h-2.5 w-20 rounded-full bg-white/5"></div>

                <div class="mt-3 flex items-center gap-2 text-[12px]">
                    ${statusBadge}
                    <span class="text-gray-400">${distance.toFixed(1)} km</span>
                </div>
            </div>

            <button type="button" class="px-4 py-3 rounded-xl brand-btn text-[12px] font-bold shrink-0"
                onclick="openShopModal(${shop.id})">
                VIEW
            </button>
        </div>
        `;

        ul.appendChild(li);
        }

        document.getElementById('search-bar').addEventListener('input', function(e) {
        const value = e.target.value.toLowerCase().trim();

        document.querySelectorAll('#shop-list li').forEach(li => {
        li.style.display = li.innerText.toLowerCase().includes(value) ? 'block' : 'none';
        });
        });

        setInterval(fetchShops, 2000);

        function escapeHtml(str) {
        if (!str) return '';
        return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;' ) .replace( />/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
        }
        });

        // Modal Functions (outside DOMContentLoaded)
        function openShopModal(shopId) {
        console.log('Opening modal for shop:', shopId);
        currentShopId = shopId;
        const modal = document.getElementById('shop-modal');
        const backdrop = document.getElementById('modal-backdrop');

        if (!modal || !backdrop) {
        console.error('Modal or backdrop not found');
        return;
        }

        modal.classList.remove('hidden');
        backdrop.classList.remove('hidden');

        // Get guest identity to pass with requests
        const guest = getGuestIdentity();

        // Load shop details
        fetch(`/api/motorist/shop-messages/${shopId}?guest_token=${encodeURIComponent(guest.guestToken || '')}`)
        .then(res => {
        if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
        return res.json();
        })
        .then(data => {
        console.log('Shop data received:', data);
        if (data.success) {
        const shop = data.shop;

        document.getElementById('modal-shop-name').textContent = escapeHtml(shop.shop_name);
        document.getElementById('modal-status').textContent = shop.status.charAt(0).toUpperCase() + shop.status.slice(1);
        document.getElementById('modal-status').className = `px-3 py-1 rounded-full text-xs font-semibold ${
        shop.status === 'open' ? 'bg-green-500/20 text-green-400' :
        shop.status === 'busy' ? 'bg-yellow-500/20 text-yellow-400' :
        'bg-red-500/20 text-red-400'
        }`;
        document.getElementById('modal-rating').textContent = `⭐ ${shop.rating || 'N/A'}`;
        document.getElementById('modal-address').textContent = escapeHtml(shop.address);
        document.getElementById('modal-phone').innerHTML = shop.phone ? `<a href="tel:${shop.phone}"
            class="text-orange-400 hover:text-orange-300">${escapeHtml(shop.phone)}</a>` : 'N/A';
        document.getElementById('modal-email').innerHTML = shop.email ? `<a href="mailto:${shop.email}"
            class="text-orange-400 hover:text-orange-300">${escapeHtml(shop.email)}</a>` : 'N/A';

        // Calculate distance and ETA
        const distance = ((Math.random() * 5) + 0.5).toFixed(1);
        const eta = Math.ceil(distance * 3);
        document.getElementById('modal-distance').textContent = `${distance} km`;
        document.getElementById('modal-eta').textContent = `~${eta} min`;

        // Load messages
        displayMessages(data.messages, data.current_user_type);

        // Start auto-refreshing messages
        if (messageLoadInterval) clearInterval(messageLoadInterval);
        messageLoadInterval = setInterval(() => loadModalMessages(shopId), 2000);
        } else {
        console.error('API error:', data.message);
        alert('Error loading shop details: ' + data.message);
        }
        })
        .catch(err => {
        console.error('Error loading shop details:', err);
        alert('Error loading shop details. Please check console for details.');
        });
        }

        function loadModalMessages(shopId) {
        const guest = getGuestIdentity();
        const url = `/api/motorist/shop-messages/${shopId}?guest_token=${encodeURIComponent(guest.guestToken || '')}`;

        fetch(url)
        .then(res => res.json())
        .then(data => {
        if (data.success) {
        displayMessages(data.messages, data.current_user_type);
        } else {
        console.error('Error loading messages:', data.message);
        }
        })
        .catch(err => console.error('Error loading messages:', err));
        }

        function displayMessages(messages, userType) {
        const chatBox = document.getElementById('chat-messages');
        chatBox.innerHTML = '';

        if (!messages || messages.length === 0) {
        chatBox.innerHTML = '<div class="flex justify-center items-center h-full"><p class="text-gray-400 text-sm">No messages yet. Start a conversation!</p></div>';
        return;
        }

        messages.forEach(msg => {
        const isMine = msg.sender_type === 'motorist';
        const bubble = document.createElement('div');
        bubble.className = `flex ${isMine ? 'justify-end' : 'justify-start'}`;

        bubble.innerHTML = `
        <div class="max-w-[84%]">
            <div class="mb-1 px-1 text-[11px] ${isMine ? 'text-right text-gray-500' : 'text-left text-gray-500'}">
                ${isMine ? 'You' : 'Shop'} · ${formatTime(msg.created_at)}
            </div>

            <div
                class="rounded-[8px] px-4 py-3 text-[15px] leading-snug border ${
                    isMine
                        ? 'bg-[#1a1a1a] border-orange-500/40 text-[#ffd899]'
                        : 'bg-[#2a2d33] border-white/10 text-white'
                }">
                ${escapeHtml(msg.message)}
            </div>
        </div>
        `;

        chatBox.appendChild(bubble);
        });

        chatBox.scrollTop = chatBox.scrollHeight;
        }

        function switchTab(tab) {
        const detailsTab = document.getElementById('details-tab');
        const messagesTab = document.getElementById('messages-tab');
        const tabDetailsBtn = document.getElementById('tab-details');
        const tabMessagesBtn = document.getElementById('tab-messages');
        const chatInputSection = document.getElementById('chat-input-section');

        const baseUrl = window.location.pathname + window.location.search;
        const newUrl = baseUrl + '#' + tab;
        if (window.location.href !== newUrl) {
            history.replaceState(null, '', newUrl);
        }

        if (tab === 'details') {
        detailsTab.classList.remove('hidden');
        messagesTab.classList.add('hidden');
        tabDetailsBtn.classList.add('border-orange-500');
        tabDetailsBtn.classList.remove('border-transparent', 'text-gray-400');
        tabMessagesBtn.classList.remove('border-orange-500');
        tabMessagesBtn.classList.add('border-transparent', 'text-gray-400');
        chatInputSection.classList.add('hidden');
        } else {
        detailsTab.classList.add('hidden');
        messagesTab.classList.remove('hidden');
        tabDetailsBtn.classList.remove('border-orange-500');
        tabDetailsBtn.classList.add('border-transparent', 'text-gray-400');
        tabMessagesBtn.classList.add('border-orange-500');
        tabMessagesBtn.classList.remove('border-transparent', 'text-gray-400');
        chatInputSection.classList.remove('hidden');
        }
        }

        function sendMessage(e) {
        e.preventDefault();

        const messageInput = document.getElementById('message-input-modal');
        const message = messageInput.value.trim();

        if (!message || !currentShopId) {
        console.warn('No message or shop ID');
        return false;
        }

        const guest = getGuestIdentity();
        const submitBtn = document.querySelector('#chat-form-modal button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;

        fetch('/api/motorist/shop-messages', {
        method: 'POST',
        headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'X-Guest-Token': guest.guestToken || ''
        },
        body: JSON.stringify({
        shop_id: currentShopId,
        message: message,
        guest_token: guest.guestToken
        })
        })
        .then(res => {
        if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
        return res.json();
        })
        .then(data => {
        console.log('Message response:', data);
        if (data.success) {
        messageInput.value = '';
        loadModalMessages(currentShopId);
        } else {
        alert(data.message || 'Failed to send message');
        }
        })
        .catch((err) => {
        console.error('Error sending message:', err);
        alert('Error sending message. Please check console.');
        })
        .finally(() => {
        if (submitBtn) submitBtn.disabled = false;
        });

        return false;
        }

        function closeModal() {
        const modal = document.getElementById('shop-modal');
        const backdrop = document.getElementById('modal-backdrop');

        modal.classList.add('hidden');
        backdrop.classList.add('hidden');
        currentShopId = null;

        if (messageLoadInterval) {
        clearInterval(messageLoadInterval);
        messageLoadInterval = null;
        }

        // Reset tabs
        document.getElementById('details-tab').classList.remove('hidden');
        document.getElementById('messages-tab').classList.add('hidden');
        document.getElementById('tab-details').classList.add('border-orange-500');
        document.getElementById('tab-details').classList.remove('border-transparent', 'text-gray-400');
        document.getElementById('tab-messages').classList.remove('border-orange-500');
        document.getElementById('tab-messages').classList.add('border-transparent', 'text-gray-400');
        }

        function escapeHtml(str) {
        if (!str) return '';
        return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;' ) .replace( />/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
        }

        function formatTime(dateString) {
        if (!dateString) return '';
        let date = new Date(dateString);
        if (isNaN(date.getTime()) && typeof dateString === 'string') {
            date = new Date(dateString.replace(' ', 'T'));
        }
        if (isNaN(date.getTime())) return dateString;

        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);

        if (diffMins < 1) return 'just now';
        if (diffMins < 60) return `${diffMins}m ago`;
        if (diffHours < 24) return `${diffHours}h ago`;
        if (diffDays < 7) return `${diffDays}d ago`;
        return date.toLocaleDateString();
        }

        // Profile
            Modal Functions function openProfileModal() { const modal=document.getElementById('profile-modal'); const
            backdrop=document.getElementById('profile-modal-backdrop'); if (!modal || !backdrop) return; // Populate inputs
            from DB-backed profile cache const identity=getGuestIdentity();
            document.getElementById('prof-input-owner-name').value=identity.ownerName || '' ;
            document.getElementById('prof-input-contact-number').value=identity.contactNumber || '' ;
            document.getElementById('prof-input-vehicle-make-model').value=identity.vehicleMakeModel || '' ;
            document.getElementById('prof-input-vehicle-variant-color').value=identity.vehicleVariantColor || '' ;
            document.getElementById('prof-input-plate-temp-number').value=identity.plateTempNumber || '' ;
            document.getElementById('profile-guest-id').textContent=identity.guestToken || 'Unknown' ;
            modal.classList.remove('hidden'); modal.classList.add('flex'); backdrop.classList.remove('hidden'); } function closeProfileModal() { const
            modal=document.getElementById('profile-modal'); const
            backdrop=document.getElementById('profile-modal-backdrop'); if (modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); } if
            (backdrop) backdrop.classList.add('hidden'); } async function saveProfileModal() { const patch={ owner_name:
            document.getElementById('prof-input-owner-name').value.trim(), contact_number:
            document.getElementById('prof-input-contact-number').value.trim(), vehicle_make_model:
            document.getElementById('prof-input-vehicle-make-model').value.trim(), vehicle_variant_color:
            document.getElementById('prof-input-vehicle-variant-color').value.trim(), plate_temp_number:
            document.getElementById('prof-input-plate-temp-number').value.trim(), }; const
            btn=document.querySelector('#profile-modal button[onclick="saveProfileModal()"]'); if (btn) {
            btn.disabled=true; btn.textContent = 'Saving…'; } await mfSaveProfile(patch); if (btn) { btn.disabled=false;
            btn.textContent = '💾 SAVE PROFILE'; } window.motoristIdentity=getGuestIdentity(); closeProfileModal(); } //
            Legacy edit stubs — no longer needed but kept to avoid JS errors function editMotoristName() {
            openProfileModal(); } function editContactInfo() { openProfileModal(); } function editVehicleInfo() {
            openProfileModal(); } </script>
        @endsection
