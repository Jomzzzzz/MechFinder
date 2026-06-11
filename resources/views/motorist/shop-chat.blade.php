@extends('layouts.motorist')

@section('content')
<div class="min-h-screen flex flex-col bg-slate-50 text-slate-900">

    <div class="sticky top-0 z-20 bg-white border-b border-slate-200 px-4 pt-4 pb-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('motorist.shop-messages') }}" class="w-10 h-10 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-700 shadow-sm">←</a>

            <div class="w-12 h-12 rounded-3xl bg-slate-100 border border-slate-200 flex items-center justify-center text-xl text-slate-700 shadow-sm">
                🔧
            </div>

            <div class="min-w-0">
                <h1 class="text-base font-extrabold truncate">{{ $shop->shop_name }}</h1>
                <p class="text-xs text-slate-500 truncate">
                    @if($shop->status === 'open')
                        <span class="text-green-600">● Online</span>
                    @elseif($shop->status === 'busy')
                        <span class="text-amber-600">● Busy</span>
                    @else
                        <span class="text-red-600">● Offline</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap gap-2 text-[11px] text-slate-500">
            <span class="rounded-2xl bg-slate-100 px-3 py-2">⭐ {{ $shop->rating ?? 'N/A' }}</span>
            <span class="rounded-2xl bg-slate-100 px-3 py-2">📍 {{ $shop->distance }} km</span>
            <span class="rounded-2xl bg-slate-100 px-3 py-2">🕐 ~{{ $shop->eta_minutes }} min</span>
        </div>
    </div>

    <div id="chat-box" class="flex-1 min-h-0 overflow-y-auto px-4 py-4 space-y-4 bg-slate-50"></div>

    <div class="px-4 pb-5 pt-4 bg-white border-t border-slate-200 sticky bottom-0 z-10">
        <form id="chat-form" class="flex items-center gap-3">
            <input
                type="text"
                id="message-input"
                placeholder="Type a message..."
                class="flex-1 rounded-full border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                required
            >
            <button type="submit" class="w-14 h-14 rounded-full bg-blue-600 text-white text-lg font-semibold shadow-lg transition hover:bg-blue-700">→</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const shopId = {{ $shop->id }};
    const shopName = @json($shop->shop_name);
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
        let date = new Date(value);
        if (isNaN(date.getTime()) && typeof value === 'string') {
            date = new Date(value.replace(' ', 'T'));
        }
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
                    const senderName = isMine ? 'You' : shopName;

                    const bubble = document.createElement('div');
                    bubble.className = `flex ${isMine ? 'justify-end' : 'justify-start'}`;

                    bubble.innerHTML = `
                        <div class="max-w-[85%]">
                            <div class="mb-2 text-[11px] ${isMine ? 'text-right text-slate-500' : 'text-left text-slate-500'}">
                                ${senderName} · ${escapeHtml(formatTime(msg.created_at))}
                            </div>
                            <div class="rounded-3xl px-4 py-3 text-sm leading-6 shadow-sm ${
                                isMine
                                    ? 'bg-blue-600 text-white'
                                    : 'bg-white text-slate-900 border border-slate-200'
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
