@extends('layouts.motorist')

@section('content')
<div class="min-h-screen flex flex-col text-white">

    <div class="sticky top-0 z-20 bg-[#0d1118]/95 backdrop-blur border-b border-white/5 px-4 pt-4 pb-4">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('motorist.shop-messages') }}" class="w-9 h-9 rounded-full bg-white/10 border border-white/10 flex items-center justify-center text-sm shrink-0">←</a>

                <div class="w-10 h-10 rounded-full bg-white/10 border border-white/10 shrink-0 flex items-center justify-center">
                    <span class="text-lg">🔧</span>
                </div>

                <div class="min-w-0">
                    <h1 class="text-[15px] font-extrabold truncate">{{ $shop->shop_name }}</h1>
                    <p class="text-[12px] text-gray-400 font-medium truncate">
                        @if($shop->status === 'open')
                            <span class="text-green-400">● Online</span>
                        @elseif($shop->status === 'busy')
                            <span class="text-yellow-400">● Busy</span>
                        @else
                            <span class="text-red-400">● Offline</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="text-lg">📞</div>
        </div>

        <div class="mt-4">
            <div class="flex items-center justify-between gap-2 text-xs text-gray-400">
                <div class="flex gap-3">
                    <span>⭐ {{ $shop->rating ?? 'N/A' }}</span>
                    <span>📍 {{ $shop->distance }} km</span>
                    <span>🕐 ~{{ $shop->eta_minutes }} min</span>
                </div>
            </div>
        </div>
    </div>

    <div id="chat-box" class="flex-1 overflow-y-auto px-4 py-4 space-y-3"></div>

    <div class="px-4 pb-4">
        <div class="glass-card rounded-2xl p-3">
            <div class="text-[11px] text-gray-400 mb-2">📍 {{ $shop->address }}</div>
            <div class="text-[11px] text-gray-400">📞 {{ $shop->phone ?? 'N/A' }}</div>
        </div>
    </div>

    <div class="px-4 pb-5 pt-2 border-t border-white/5 bg-[#090909]/95">
        <form id="chat-form" class="flex gap-2 items-center">
            <input
                type="text"
                id="message-input"
                placeholder="Type a message..."
                class="flex-1 rounded-2xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white placeholder:text-gray-500 outline-none focus:border-orange-500"
                required
            >
            <button type="submit" class="w-14 h-14 rounded-2xl brand-btn text-lg flex items-center justify-center">→</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const shopId = {{ $shop->id }};
    const chatBox = document.getElementById('chat-box');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    let isLoading = false;

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
        const date = new Date(value.replace(' ', 'T'));
        if (isNaN(date.getTime())) return value;
        return date.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
    }

    function loadMessages() {
        if (isLoading) return;
        isLoading = true;

        fetch(`/api/motorist/shop-messages/${shopId}`)
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    console.error('Error loading messages:', data.message);
                    chatBox.innerHTML = '<div class="text-center text-red-400 py-4">Failed to load messages</div>';
                    return;
                }

                const previousHeight = chatBox.scrollHeight;
                const previousScroll = chatBox.scrollTop;
                const wasAtBottom = previousScroll + chatBox.clientHeight >= previousHeight - 10;

                chatBox.innerHTML = '';

                data.messages.forEach(msg => {
                    const isMine = msg.sender_type === 'motorist';

                    const bubble = document.createElement('div');
                    bubble.className = `flex ${isMine ? 'justify-end' : 'justify-start'}`;

                    bubble.innerHTML = `
                        <div class="max-w-[84%]">
                            <div class="mb-1 px-1 text-[11px] ${isMine ? 'text-right text-gray-500' : 'text-left text-gray-500'}">
                                ${isMine ? 'You' : '{{ $shop->shop_name }}'} · ${escapeHtml(formatTime(msg.created_at))}
                            </div>

                            <div class="rounded-[8px] px-4 py-3 text-[15px] leading-snug border ${
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

                if (wasAtBottom) {
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
            })
            .catch(err => {
                console.error('Load messages error:', err);
                chatBox.innerHTML = '<div class="text-center text-red-400 py-4">Error loading messages</div>';
            })
            .finally(() => {
                isLoading = false;
            });
    }

    chatForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const message = messageInput.value.trim();
        if (!message) return;

        const sendButton = chatForm.querySelector('button');
        sendButton.disabled = true;

        fetch('/api/motorist/shop-messages', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                shop_id: shopId,
                message: message
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
        })
        .finally(() => {
            sendButton.disabled = false;
        });
    });

    loadMessages();
    setInterval(loadMessages, 2000);
});
</script>
@endsection
