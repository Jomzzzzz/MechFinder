@extends('layouts.motorist')

@section('content')
<div class="min-h-screen flex flex-col text-white">

    <div class="sticky top-0 z-20 bg-[#0d1118]/95 backdrop-blur border-b border-white/5 px-4 pt-4 pb-4">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('motorist.requests') }}" class="w-9 h-9 rounded-full bg-white/10 border border-white/10 flex items-center justify-center text-sm shrink-0">←</a>

                <div class="w-10 h-10 rounded-full bg-white/10 border border-white/10 shrink-0"></div>

                <div class="min-w-0">
                    <h1 class="text-[15px] font-extrabold truncate">{{ $dispatch->shop_name ?? 'Shop Chat' }}</h1>
                    <p class="text-[12px] text-green-400 font-medium truncate">• En route - ~5 min</p>
                </div>
            </div>

            <div class="flex gap-2 items-center">
                <a href="{{ route('motorist.shop-messages') }}" class="text-sm px-3 py-2 rounded-lg bg-white/10 border border-white/10 hover:border-orange-500 transition-colors" title="Message other shops">
                    💬
                </a>
                <div class="text-pink-400 text-lg">📞</div>
            </div>
        </div>

        <div class="mt-4">
            <div class="flex items-center justify-between mb-1 text-[11px]">
                <span class="text-red-400 font-bold tracking-wide">DISPATCH ACTIVE</span>
                <span class="text-gray-400">65%</span>
            </div>
            <div class="h-2 rounded-full bg-white/10 overflow-hidden">
                <div class="h-full w-[65%] bg-gradient-to-r from-red-500 to-red-400 rounded-full"></div>
            </div>
        </div>
    </div>

    <div id="chat-box" class="flex-1 overflow-y-auto px-4 py-4 space-y-4"></div>

    <div class="px-4 pb-3 pt-4">
        <div class="glass-card rounded-[16px] p-4">
            <div class="text-[11px] text-gray-500 mb-3 font-semibold">📍 LIVE LOCATION SHARED BY MECHANIC</div>
            <div class="soft-grid rounded-[12px] h-28 relative flex items-center justify-center border border-white/10">
                <div class="w-4 h-4 rounded-full bg-red-500 shadow-[0_0_12px_#ef4444] animate-pulse"></div>
            </div>
        </div>
    </div>

    <div class="px-4 pb-5 pt-3 border-t border-white/5 bg-[#090909]/95 sticky bottom-0">
        <form id="chat-form" class="flex gap-2 items-center">
            <input
                type="text"
                id="message-input"
                placeholder="Type a message..."
                class="flex-1 rounded-[14px] bg-white/5 border border-white/10 px-4 py-3 text-[13px] text-white placeholder:text-gray-600 outline-none focus:border-orange-500 transition-colors"
                required
            >
            <button type="submit" class="w-12 h-12 rounded-[14px] brand-btn text-[18px] flex items-center justify-center flex-shrink-0 font-bold transition-all hover:shadow-lg">→</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const dispatchId = {{ $dispatch->id }};
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

        fetch(`/api/chat/${dispatchId}`)
            .then(res => res.json())
            .then(data => {
                if (!data.success) return;

                chatBox.innerHTML = '';

                data.messages.forEach(msg => {
                    const isMine = Number(msg.sender_id) === Number(data.current_user_id);

                    const bubble = document.createElement('div');
                    bubble.className = `flex ${isMine ? 'justify-end' : 'justify-start'}`;

                    bubble.innerHTML = `
                        <div class="max-w-[84%]">
                            <div class="mb-1 px-1 text-[11px] ${isMine ? 'text-right text-gray-500' : 'text-left text-gray-500'}">
                                ${escapeHtml(msg.sender_name || 'User')} · ${escapeHtml(formatTime(msg.created_at))}
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

                chatBox.scrollTop = chatBox.scrollHeight;
            })
            .catch(err => console.error('Load messages error:', err))
            .finally(() => {
                isLoading = false;
            });
    }

    chatForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const message = messageInput.value.trim();
        if (!message) return;

        fetch('/api/messages', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                dispatch_id: dispatchId,
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
        });
    });

    loadMessages();
    setInterval(loadMessages, 2000);
});
</script>
@endsection