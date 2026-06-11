@extends('layouts.motorist')

@section('content')
<div class="min-h-screen flex flex-col text-white bg-[#06070d]">
    <!-- Header -->
    <div class="sticky top-0 z-20 bg-[#0d1118]/95 backdrop-blur border-b border-white/5 px-4 pt-4 pb-4">
        <div class="flex items-center justify-between gap-3 mb-3">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('motorist.index') }}" class="w-10 h-10 rounded-2xl bg-white/10 border border-white/10 flex items-center justify-center text-sm shrink-0">←</a>
                <div class="min-w-0">
                    <h1 class="text-base font-extrabold truncate">Shop Messages</h1>
                    <p class="text-xs text-gray-400 truncate">Chats with nearby shops</p>
                </div>
            </div>
        </div>

        <div class="rounded-3xl bg-white/5 border border-white/10 px-3 py-3 flex items-center gap-2">
            <input 
                type="text" 
                id="location-input" 
                placeholder="Enter your location"
                class="flex-1 rounded-2xl bg-transparent border-none px-2 py-2 text-sm text-white placeholder:text-gray-500 outline-none"
                value="14.8386, 120.2842"
            >
            <button onclick="updateLocation()" class="inline-flex items-center justify-center rounded-2xl bg-orange-500 px-4 py-2 text-sm font-semibold text-black transition hover:bg-orange-600">Update</button>
        </div>
    </div>

    <div id="shops-list" class="flex-1 overflow-y-auto px-4 py-4 space-y-3">
        <div class="flex justify-center items-center h-32">
            <div class="text-gray-400 text-sm">Loading shops...</div>
        </div>
    </div>
</div>

<!-- Modal Backdrop -->
<div id="modal-backdrop" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-40" onclick="closeModal()"></div>

<!-- Shop Details & Chat Modal -->
<div id="shop-modal" class="fixed bottom-0 left-0 right-0 bg-[#0d1118] border-t border-white/10 rounded-t-3xl hidden z-50 h-[90vh] max-w-2xl mx-auto">
    <!-- Modal Header -->
    <div class="sticky top-0 flex items-center justify-between gap-3 px-4 pt-4 pb-4 border-b border-white/5 flex-shrink-0">
        <button onclick="closeModal()" class="w-9 h-9 rounded-full bg-white/10 border border-white/10 flex items-center justify-center text-sm">←</button>
        <h2 id="modal-shop-name" class="text-[15px] font-extrabold flex-1 text-center truncate">Shop Name</h2>
        <div class="w-9 h-9"></div>
    </div>

    <!-- Shop Info Tabs -->
    <div class="flex gap-2 px-4 pt-2 border-b border-white/5 flex-shrink-0 overflow-x-auto">
        <button onclick="switchTab('details')" id="tab-details" class="px-4 py-2 text-sm font-semibold border-b-2 border-orange-500 whitespace-nowrap">Details</button>
        <button onclick="switchTab('messages')" id="tab-messages" class="px-4 py-2 text-sm font-semibold border-b-2 border-transparent whitespace-nowrap text-gray-400">Messages</button>
    </div>

    <!-- Tab Content -->
    <div class="flex-1 overflow-y-auto">
        <!-- Details Tab -->
        <div id="details-tab" class="px-4 py-4 space-y-4">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-gray-400 text-sm">Status</span>
                    <span id="modal-status" class="px-3 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-400">Open</span>
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
        <div id="messages-tab" class="hidden flex-col flex-1 min-h-0">
            <div id="chat-messages" class="flex-1 min-h-0 overflow-y-auto px-4 py-4 space-y-3"></div>
        </div>
    </div>

    <!-- Message Input -->
    <div id="chat-input-section" class="hidden px-4 pb-4 pt-2 border-t border-white/5 flex-shrink-0 z-10">
        <form id="chat-form-modal" class="flex gap-2 items-center" onsubmit="return sendMessage(event)">
            <input
                type="text"
                id="message-input-modal"
                placeholder="Type a message..."
                class="flex-1 rounded-2xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white placeholder:text-gray-500 outline-none focus:border-orange-500"
                required
            >
            <button type="submit" class="w-12 h-12 rounded-2xl brand-btn text-lg flex items-center justify-center flex-shrink-0">→</button>
        </form>
    </div>
</div>

<script>
let currentShopId = null;
let userLat = 14.8386;
let userLng = 120.2842;
let messageLoadInterval = null;

document.addEventListener('DOMContentLoaded', function() {
    userLat = localStorage.getItem('userLat') || 14.8386;
    userLng = localStorage.getItem('userLng') || 120.2842;
    
    document.getElementById('location-input').value = `${userLat}, ${userLng}`;
    
    loadShops(userLat, userLng);

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
});

function updateLocation() {
    const input = document.getElementById('location-input').value.trim();
    const coords = input.split(',').map(c => c.trim());
    
    if (coords.length !== 2) {
        alert('Please enter coordinates in format: lat, lng');
        return;
    }
    
    userLat = parseFloat(coords[0]);
    userLng = parseFloat(coords[1]);
    
    if (isNaN(userLat) || isNaN(userLng)) {
        alert('Invalid coordinates');
        return;
    }
    
    localStorage.setItem('userLat', userLat);
    localStorage.setItem('userLng', userLng);
    
    loadShops(userLat, userLng);
}

function loadShops(lat, lng) {
    const shopsList = document.getElementById('shops-list');
    shopsList.innerHTML = '<div class="flex justify-center items-center h-32"><div class="text-gray-400 text-sm">Loading shops...</div></div>';

    const identity = typeof mfIdentity === 'function' ? mfIdentity() : { guest_token: '' };
    const params = new URLSearchParams({ lat, lng });
    if (identity.guest_token) {
        params.set('guest_token', identity.guest_token);
    }

    fetch(`/api/motorist/shops-for-messaging?${params.toString()}`)
        .then(res => res.json())
        .then(data => {
            if (!data.success || !data.shops || data.shops.length === 0) {
                shopsList.innerHTML = '<div class="px-4 py-8 text-center text-gray-400">No shops found near you</div>';
                return;
            }

            shopsList.innerHTML = '';
            
            data.shops.forEach(shop => {
                const lastMessageTime = shop.last_message_time ? formatTime(shop.last_message_time) : 'No messages';
                const unreadBadge = shop.unread_count > 0 ? `<span class="ml-2 px-2 py-1 bg-red-500 rounded-full text-xs">${shop.unread_count}</span>` : '';
                
                const shopCard = document.createElement('div');
                shopCard.className = 'glass-card rounded-2xl p-4 transition-all hover:border-orange-500/50';
                
                shopCard.innerHTML = `
                    <div class="flex gap-3 items-start justify-between">
                        <div class="flex gap-3 items-start flex-1 min-w-0">
                            <div class="w-12 h-12 rounded-xl bg-white/10 border border-white/10 flex-shrink-0 flex items-center justify-center">
                                <span class="text-lg">🔧</span>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2 mb-1">
                                    <h3 class="font-semibold text-sm truncate">${escapeHtml(shop.shop_name)}</h3>
                                    ${shop.status === 'open' ? '<span class="text-xs px-2 py-1 bg-green-500/20 text-green-400 rounded flex-shrink-0">Open</span>' : shop.status === 'busy' ? '<span class="text-xs px-2 py-1 bg-yellow-500/20 text-yellow-400 rounded flex-shrink-0">Busy</span>' : '<span class="text-xs px-2 py-1 bg-red-500/20 text-red-400 rounded flex-shrink-0">Closed</span>'}
                                </div>
                                
                                <p class="text-xs text-gray-400 mb-2 truncate">${escapeHtml(shop.address)}</p>
                                
                                <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                                    <span>⭐ ${shop.rating || 'N/A'}</span>
                                    <span>📍 ${shop.distance} km</span>
                                </div>
                                
                                <p class="text-xs text-gray-400 truncate">${shop.last_message ? escapeHtml(shop.last_message) : 'No messages yet'}</p>
                                <p class="text-xs text-gray-500">${lastMessageTime}</p>
                            </div>
                        </div>

                        <button type="button" onclick="openShopModal(${shop.id})" class="px-4 py-2 rounded-lg bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold flex-shrink-0">VIEW</button>
                    </div>
                `;
                
                shopsList.appendChild(shopCard);
            });
        })
        .catch(err => {
            console.error('Error loading shops:', err);
            shopsList.innerHTML = '<div class="px-4 py-8 text-center text-red-400">Failed to load shops</div>';
        });
}

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
    modal.classList.add('flex');
    backdrop.classList.remove('hidden');

    // Load shop details
    const identity = typeof mfIdentity === 'function' ? mfIdentity() : { guest_token: '' };
    const params = new URLSearchParams();
    if (identity.guest_token) {
        params.set('guest_token', identity.guest_token);
    }
    fetch(`/api/motorist/shop-messages/${shopId}?${params.toString()}`)
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
                document.getElementById('modal-phone').innerHTML = shop.phone ? `<a href="tel:${shop.phone}" class="text-orange-400 hover:text-orange-300">${escapeHtml(shop.phone)}</a>` : 'N/A';
                document.getElementById('modal-email').innerHTML = shop.email ? `<a href="mailto:${shop.email}" class="text-orange-400 hover:text-orange-300">${escapeHtml(shop.email)}</a>` : 'N/A';

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
    fetch(`/api/motorist/shop-messages/${shopId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                displayMessages(data.messages, data.current_user_type);
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
            <div class="max-w-[82%]">
                <div class="flex flex-col ${isMine ? 'items-end' : 'items-start'} gap-2">
                    <div class="rounded-3xl px-4 py-3 text-sm leading-6 border ${
                        isMine
                            ? 'bg-orange-500/15 border-orange-500/40 text-orange-100'
                            : 'bg-white/5 border-white/10 text-white'
                    }">
                        ${escapeHtml(msg.message)}
                    </div>
                    <div class="text-[11px] text-gray-500">${formatTime(msg.created_at)}</div>
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
        messagesTab.classList.remove('flex');
        tabDetailsBtn.classList.add('border-orange-500');
        tabDetailsBtn.classList.remove('border-transparent', 'text-gray-400');
        tabMessagesBtn.classList.remove('border-orange-500');
        tabMessagesBtn.classList.add('border-transparent', 'text-gray-400');
        chatInputSection.classList.add('hidden');
    } else {
        detailsTab.classList.add('hidden');
        messagesTab.classList.remove('hidden');
        messagesTab.classList.add('flex');
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

    const identity = typeof mfIdentity === 'function' ? mfIdentity() : { guest_token: '' };
    const submitBtn = document.querySelector('#chat-form-modal button[type="submit"]');
    if (submitBtn) submitBtn.disabled = true;

    fetch('/api/motorist/shop-messages', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            shop_id: currentShopId,
            message: message,
            guest_token: identity.guest_token || ''
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
    modal.classList.remove('flex');
    backdrop.classList.add('hidden');
    currentShopId = null;

    if (messageLoadInterval) {
        clearInterval(messageLoadInterval);
        messageLoadInterval = null;
    }

    // Reset tabs
    document.getElementById('details-tab').classList.remove('hidden');
    const messagesTabEl = document.getElementById('messages-tab');
    messagesTabEl.classList.add('hidden');
    messagesTabEl.classList.remove('flex');
    document.getElementById('tab-details').classList.add('border-orange-500');
    document.getElementById('tab-details').classList.remove('border-transparent', 'text-gray-400');
    document.getElementById('tab-messages').classList.remove('border-orange-500');
    document.getElementById('tab-messages').classList.add('border-transparent', 'text-gray-400');
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
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
</script>
@endsection
