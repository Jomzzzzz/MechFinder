@extends('layouts.mechanic-mobile')

@section('hide-bottom-nav')@endsection

@section('content')
<div class="min-h-screen flex flex-col bg-[#06090f] text-slate-100">

    <div class="sticky top-0 z-20 bg-[#08101a]/95 border-b border-slate-800/70 px-4 pt-4 pb-4 backdrop-blur-xl">
        <div class="flex items-center justify-between gap-3 mb-3">
            <a href="{{ route('mechanic.messages') }}" class="w-10 h-10 rounded-2xl bg-slate-900/80 border border-slate-800 text-slate-200 flex items-center justify-center transition hover:bg-slate-800">
                ←
            </a>

            <div class="min-w-0">
                <h1 class="text-base font-semibold truncate">{{ $dispatch->shop->shop_name ?? 'Dispatch Chat' }}</h1>
                <p class="text-sm text-slate-400 truncate">{{ $dispatch->motorist->name ?? 'Motorist' }}</p>
            </div>

            <button class="w-10 h-10 rounded-2xl bg-slate-900/80 border border-slate-800 text-slate-200 flex items-center justify-center transition hover:bg-slate-800" aria-label="Call motorist">
                📞
            </button>
        </div>

        <div class="flex flex-wrap items-center gap-2 text-xs text-slate-400">
            <span class="rounded-full border border-slate-800/80 bg-slate-900/70 px-3 py-2">JOB MODE</span>
            <span class="text-slate-400">Dispatch #{{ $dispatch->id }}</span>
        </div>
    </div>

    <div id="chat-box" class="flex-1 min-h-0 overflow-y-auto px-4 py-4 space-y-3"></div>

    <div class="px-4 pb-5 pt-3 border-t border-slate-800/80 bg-[#08101a] sticky bottom-0">
        <form id="chat-form" class="flex items-center gap-3">
            <input
                type="text"
                id="message-input"
                placeholder="Write a message"
                class="flex-1 rounded-full bg-slate-950/70 border border-slate-800 px-4 py-3 text-sm text-slate-100 placeholder:text-slate-500 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition"
                required
            >
            <button type="submit" class="w-12 h-12 rounded-full bg-orange-500 text-slate-950 text-lg flex items-center justify-center transition hover:bg-orange-400">→</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const dispatchId = {{ $dispatch->id }};
    // Read conversation_type from querystring (default to 'motorist')
    const urlParams = new URLSearchParams(window.location.search);
    const conversationType = urlParams.get('conversation_type') === 'mechanic'
        ? 'shop'
        : (urlParams.get('conversation_type') || 'motorist');
    const chatBox = document.getElementById('chat-box');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    let isLoading = false;
    let channel = null;
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

    function appendMessage(msg, scroll = true) {
        if (messageIds.has(msg.id)) {
            return;
        }
        messageIds.add(msg.id);
        const isMine = msg.sender_type === 'mechanic';
        const bubble = document.createElement('div');
        bubble.className = `flex ${isMine ? 'justify-end' : 'justify-start'}`;

        bubble.innerHTML = `
            <div class="max-w-[80%]">
                <div class="flex flex-col ${isMine ? 'items-end' : 'items-start'} gap-2">
                    <div class="rounded-[26px] px-4 py-3 text-sm leading-6 ${
                        isMine
                            ? 'bg-orange-500/12 border border-orange-500/20 text-orange-100'
                            : 'bg-slate-900/90 border border-slate-800 text-slate-100'
                    } shadow-sm ${isMine ? 'shadow-orange-500/10' : 'shadow-slate-900/20'} backdrop-blur-sm">
                        ${escapeHtml(msg.message)}
                    </div>
                    <div class="text-[11px] ${isMine ? 'text-right text-slate-500' : 'text-left text-slate-500'}">
                        ${escapeHtml(formatTime(msg.created_at))}
                    </div>
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

        if (!messages || messages.length === 0) {
            chatBox.innerHTML = '<div class="flex h-full min-h-[240px] items-center justify-center"><div class="rounded-[28px] border border-slate-800/80 bg-slate-950/80 px-5 py-4 text-sm text-slate-400">No messages yet. Start the conversation with your motorist.</div></div>';
            return;
        }

        messages.forEach(msg => appendMessage(msg, false));
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function loadMessages() {
        if (isLoading) return;
        isLoading = true;

        const convQuery = conversationType ? `?conversation_type=${conversationType}` : '';
        fetch(`/api/mechanic/messages/${dispatchId}${convQuery}`)
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
            window.Echo.leave(`private-dispatch.${dispatchId}`);
        }

        channel = window.Echo.private(`dispatch.${dispatchId}`);
        channel.listen('.message.sent', (event) => {
            if (event.message?.dispatch_id !== dispatchId) {
                return;
            }
            
            // Normalize conversation_type for comparison
            // Mechanic motorist tab sends 'motorist' but backend saves it as 'mechanic'
            const expectedType = conversationType === 'motorist' ? 'mechanic' : conversationType;
            
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

        fetch('/api/mechanic/messages/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                dispatch_id: dispatchId,
                message: message,
                conversation_type: conversationType
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
    subscribeDispatchChannel();
});
</script>
@endsection
