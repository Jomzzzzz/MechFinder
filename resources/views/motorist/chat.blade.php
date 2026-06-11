@extends('layouts.motorist')

@section('content')
<div class="min-h-screen flex flex-col text-white bg-[#0a0a0a]">

    {{-- HEADER --}}
    <div class="sticky top-0 z-20 bg-gradient-to-b from-[#0d1118] to-[#0a0a0a] border-b border-white/5 px-4 pt-4 pb-0">
        <div class="flex items-center justify-between gap-3 pb-4">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('motorist.index') }}#requests" class="w-10 h-10 rounded-full bg-white/10 border border-white/10 flex items-center justify-center text-lg shrink-0 hover:border-orange-500 transition-colors">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 border border-white/10 shrink-0 flex items-center justify-center">
                    <i class="fa-solid fa-store text-white text-lg"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="text-[16px] font-extrabold truncate">{{ $dispatch->shop_name ?? 'Shop Chat' }}</h1>
                    <p class="text-[12px] text-gray-400 truncate flex items-center gap-1">
                        <i class="fa-solid fa-wrench text-orange-500 text-[10px]"></i>
                        {{ $dispatch->mechanic_name ?? 'Unassigned' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- CONVERSATION TABS --}}
        <div class="pb-4 border-t border-white/5 pt-4">
            <div class="flex gap-2 mb-3">
                <button id="tab-mechanic" type="button" onclick="switchChatTab('mechanic')" class="px-4 py-2 rounded-full border border-white/10 bg-[#F7941D] text-black text-[13px] font-semibold transition-all hover:shadow-lg">
                    <i class="fa-solid fa-wrench text-[11px] mr-1"></i>Mechanic
                </button>
                <button id="tab-shop" type="button" onclick="switchChatTab('shop')" class="px-4 py-2 rounded-full border border-white/10 bg-transparent text-[13px] text-gray-400 font-semibold transition-all hover:border-orange-500">
                    <i class="fa-solid fa-store text-[11px] mr-1"></i>Shop
                </button>
            </div>
            <p id="chat-mode-text" class="text-[12px] text-gray-500 flex items-center gap-1">
                <i class="fa-solid fa-info-circle text-[10px]"></i>
                Chat with your assigned mechanic
            </p>
        </div>
    </div>

    {{-- CHAT MESSAGES --}}
    <div id="chat-box" class="flex-1 min-h-0 overflow-y-auto px-4 py-4 space-y-4"></div>

    {{-- LOCATION CARD --}}
    <div class="px-4 pb-3 pt-2">
        <div class="rounded-[16px] p-3 bg-white/[0.03] border border-white/10 backdrop-blur">
            <div class="text-[10px] text-gray-500 mb-2 font-semibold flex items-center gap-1">
                <i class="fa-solid fa-location-dot text-red-500"></i>LIVE LOCATION
            </div>
            <div class="rounded-[12px] h-24 relative flex items-center justify-center border border-white/10 bg-white/[0.02]">
                <div class="w-3 h-3 rounded-full bg-red-500 shadow-[0_0_12px_#ef4444] animate-pulse"></div>
            </div>
        </div>
    </div>

    {{-- CHAT CLOSED NOTE --}}
    <div id="chat-closed-note" class="hidden px-4 pb-4 pt-2 text-center text-sm text-gray-400">
        <div class="rounded-lg bg-red-500/10 border border-red-500/30 px-4 py-3">
            <i class="fa-solid fa-lock text-red-500 mr-2"></i>Chat is closed after rescue completion
        </div>
    </div>

    {{-- MESSAGE INPUT --}}
    <div id="chat-footer" class="px-4 pb-4 pt-3 border-t border-white/5 bg-gradient-to-t from-[#0a0a0a] to-transparent sticky bottom-0 z-10">
        <form id="chat-form" class="flex gap-2 items-end">
            <input
                type="text"
                id="message-input"
                placeholder="Type a message..."
                class="flex-1 rounded-[12px] bg-white/5 border border-white/10 px-4 py-3 text-[13px] text-white placeholder:text-gray-600 outline-none focus:border-orange-500 focus:bg-white/8 transition-all"
                required
            >
            <button type="submit" class="w-11 h-11 rounded-[10px] bg-gradient-to-br from-orange-500 to-orange-600 text-white flex items-center justify-center flex-shrink-0 font-bold transition-all hover:shadow-lg hover:shadow-orange-500/40 active:scale-95">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const dispatchId = {{ $dispatch->id }};
    const currentUserId = {{ auth()->id() ?? 'null' }};
    const chatBox = document.getElementById('chat-box');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    const chatFooter = document.getElementById('chat-footer');
    const chatClosedNote = document.getElementById('chat-closed-note');
    const tabMechanic = document.getElementById('tab-mechanic');
    const tabShop = document.getElementById('tab-shop');
    const chatModeText = document.getElementById('chat-mode-text');
    const sendButton = chatForm ? chatForm.querySelector('button[type="submit"]') : null;
    const dispatchStatus = '{{ $dispatch->status }}';
    let isLoading = false;
    let channel = null;
    let activeConversationType = 'mechanic';
    const messageIds = new Set();

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatTime(value) {
        if (!value) return '';
        let date = new Date(value);
        if (isNaN(date.getTime()) && typeof value === 'string') {
            date = new Date(value.replace(' ', 'T'));
        }
        if (isNaN(date.getTime())) return value;
        return date.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
    }

    function getApiConversationType() {
        // Shop tab shows motorist↔shop chat, mechanic tab shows motorist↔mechanic chat.
        return activeConversationType === 'shop' ? 'motorist' : 'mechanic';
    }

    function updateTabStyles() {
        if (activeConversationType === 'shop') {
            tabShop.classList.add('bg-[#F7941D]', 'text-black');
            tabShop.classList.remove('bg-transparent', 'text-gray-400');
            tabMechanic.classList.remove('bg-[#F7941D]', 'text-black');
            tabMechanic.classList.add('bg-transparent', 'text-gray-400');
            chatModeText.textContent = '🏪 Chat with your shop';
        } else {
            tabMechanic.classList.add('bg-[#F7941D]', 'text-black');
            tabMechanic.classList.remove('bg-transparent', 'text-gray-400');
            tabShop.classList.remove('bg-[#F7941D]', 'text-black');
            tabShop.classList.add('bg-transparent', 'text-gray-400');
            chatModeText.textContent = '🔧 Chat with your assigned mechanic';
        }
    }

    function switchChatTab(tab) {
        activeConversationType = tab === 'shop' ? 'shop' : 'mechanic';
        updateTabStyles();
        const params = new URLSearchParams(window.location.search);
        params.set('conversation_type', tab);
        history.replaceState(null, '', window.location.pathname + '?' + params.toString());
        loadMessages();
    }

    window.switchChatTab = switchChatTab;

    function appendMessage(msg, scroll = true) {
        if (messageIds.has(msg.id)) {
            return;
        }
        messageIds.add(msg.id);
        const isMine = msg.sender_type === 'motorist';
        const bubble = document.createElement('div');
        bubble.className = `flex ${isMine ? 'justify-end' : 'justify-start'} animate-fadeIn`;

        const senderLabel = isMine ? 'You' : (msg.sender_name || 'Sender');
        const senderIcon = isMine ? '👤' : (msg.sender_type === 'shop' ? '🏪' : '🔧');

        bubble.innerHTML = `
            <div class="max-w-[85%]">
                <div class="mb-2 px-2 text-[10px] font-medium ${isMine ? 'text-right text-gray-500' : 'text-left text-gray-400'}">
                    ${senderIcon} ${escapeHtml(senderLabel)} · ${escapeHtml(formatTime(msg.created_at))}
                </div>
                <div class="rounded-[12px] px-4 py-3 text-[14px] leading-relaxed break-words font-[500] ${
                    isMine
                        ? 'bg-gradient-to-br from-[#F7941D] to-[#ff9e2a] text-black shadow-lg shadow-orange-500/20'
                        : 'bg-white/8 backdrop-blur border border-white/10 text-white'
                }">
                    ${escapeHtml(msg.message)}
                </div>
            </div>
        `;

        chatBox.appendChild(bubble);
        if (scroll) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    }

    function renderMessages(messages) {
        chatBox.innerHTML = '';
        messageIds.clear();
        messages.forEach(msg => appendMessage(msg, false));
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function loadMessages() {
        if (isLoading) return;
        if (dispatchStatus === 'completed' || dispatchStatus === 'cancelled') {
            chatClosedNote.classList.remove('hidden');
            if (chatFooter) chatFooter.classList.add('hidden');
        }
        const conversationType = getApiConversationType();
        isLoading = true;

        fetch(`/api/chat/${dispatchId}?conversation_type=${conversationType}`)
            .then(res => res.json())
            .then(data => {
                if (!data.success) return;
                renderMessages(data.messages);
            })
            .catch(err => console.error('Load messages error:', err))
            .finally(() => {
                isLoading = false;
            });
    }

    function subscribeDispatchChannel() {
        if (!window.Echo || !window.Echo.private) {
            return;
        }

        if (channel) {
            window.Echo.leave(`dispatch.${dispatchId}`);
        }

        channel = window.Echo.private(`dispatch.${dispatchId}`);
        channel.listen('.message.sent', (event) => {
            if (event.message?.dispatch_id !== dispatchId) {
                return;
            }
            const expectedType = getApiConversationType();
            if (event.message?.conversation_type && event.message.conversation_type !== expectedType) {
                return;
            }
            appendMessage(event.message);
        });
    }

    chatForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const message = messageInput.value.trim();
        if (!message) return;

        const conversationType = getApiConversationType();
        fetch('/api/messages', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                dispatch_id: dispatchId,
                message: message,
                sender_type: 'motorist',
                motorist_id: currentUserId,
                conversation_type: conversationType,
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                messageInput.value = '';
                loadMessages();
            } else {
                alert(data.message || 'Failed to send message.');
            }
        })
        .catch(() => {
            alert('Something went wrong.');
        });
    });

    const urlParams = new URLSearchParams(window.location.search);
    const initialType = urlParams.get('conversation_type') === 'shop' ? 'shop' : 'mechanic';
    activeConversationType = initialType;
    updateTabStyles();
    loadMessages();
    subscribeDispatchChannel();
});
</script>
@endsection