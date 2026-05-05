@extends('layouts.shop')

@section('content')

<div class="mb-8">
    <h2 class="heading-font text-3xl mb-2">Messages</h2>
    <p class="text-gray-400">Chat with motorists connected to dispatch requests.</p>
</div>

<div class="h-[calc(100vh-180px)] flex bg-[#0F0F0F] text-white rounded-2xl overflow-hidden border border-white/10">

    <!-- LEFT: CONVERSATIONS -->
    <div class="w-full md:w-1/3 border-r border-white/10 bg-[#121214] overflow-y-auto">

        <div class="p-4 font-black text-xl border-b border-white/10 flex items-center justify-between">
            <span>Conversations</span>
            <span class="text-xs px-3 py-1 rounded-full bg-white/5 text-gray-400">
                {{ $conversations->count() }}
            </span>
        </div>

        @forelse($conversations as $c)
            <button
                onclick="openChat({{ $c->dispatch_id }}, @js($c->motorist_name), @js($c->issue_type))"
                id="conversation-{{ $c->dispatch_id }}"
                class="w-full text-left p-4 border-b border-white/5 cursor-pointer hover:bg-white/5 transition conversation-item"
            >
                <div class="flex justify-between gap-3">
                    <div>
                        <div class="font-bold text-white">
                            {{ $c->motorist_name ?? 'Unknown Motorist' }}
                        </div>

                        <div class="text-xs text-gray-400 mt-1">
                            {{ $c->issue_type ?? 'Motorcycle Issue' }}
                        </div>

                        <div class="text-xs text-gray-500 mt-1">
                            Status: {{ strtoupper(str_replace('_', ' ', $c->status ?? 'unknown')) }}
                        </div>
                    </div>

                    <div class="text-xs text-[#F7941D] font-bold">
                        #{{ $c->dispatch_id }}
                    </div>
                </div>
            </button>
        @empty
            <div class="p-6 text-center">
                <p class="text-gray-400">No conversations yet.</p>
                <p class="text-gray-500 text-sm mt-1">
                    Messages will appear after motorists send requests.
                </p>
            </div>
        @endforelse

    </div>

    <!-- RIGHT: CHAT -->
    <div class="hidden md:flex flex-1 flex-col" id="chatPanel">

        <div class="p-4 border-b border-white/10 bg-[#121214]">
            <div class="font-black text-white" id="chatTitle">
                Select conversation
            </div>
            <div class="text-xs text-gray-400 mt-1" id="chatSubtitle">
                Choose a motorist conversation from the left.
            </div>
        </div>

        <div id="chatBox" class="flex-1 p-4 overflow-y-auto space-y-3 bg-[#0F0F0F]">
            <div class="h-full flex items-center justify-center text-center">
                <div>
                    <p class="text-gray-400 text-lg">No chat selected</p>
                    <p class="text-gray-500 text-sm mt-1">Select a conversation to start messaging.</p>
                </div>
            </div>
        </div>

        <div class="p-4 border-t border-white/10 bg-[#121214] flex gap-2">
            <input
                id="msgInput"
                class="flex-1 bg-white/5 border border-white/10 p-3 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-orange-500"
                placeholder="Type message..."
                disabled
                onkeydown="if(event.key === 'Enter') sendMsg()"
            >

            <button
                onclick="sendMsg()"
                id="sendButton"
                disabled
                class="bg-[#F7941D] px-5 rounded-xl text-black font-black disabled:opacity-50 disabled:cursor-not-allowed"
            >
                Send
            </button>
        </div>

    </div>
</div>

<script>
let dispatchId = null;
let refreshInterval = null;

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
}

function openChat(id, name, issue) {
    dispatchId = id;

    document.querySelectorAll('.conversation-item').forEach(item => {
        item.classList.remove('bg-white/10', 'border-orange-500/30');
    });

    const selected = document.getElementById(`conversation-${id}`);
    if (selected) {
        selected.classList.add('bg-white/10', 'border-orange-500/30');
    }

    document.getElementById('chatPanel').classList.remove('hidden');
    document.getElementById('chatPanel').classList.add('flex');

    document.getElementById('chatTitle').innerText = name || 'Unknown Motorist';
    document.getElementById('chatSubtitle').innerText = issue || 'Motorcycle Issue';

    document.getElementById('msgInput').disabled = false;
    document.getElementById('sendButton').disabled = false;

    loadMessages();

    if (refreshInterval) clearInterval(refreshInterval);
    refreshInterval = setInterval(loadMessages, 3000);
}

async function loadMessages() {
    if (!dispatchId) return;

    try {
        const res = await fetch(`/api/shop/messages/${dispatchId}`, {
            headers: {
                'Accept': 'application/json'
            }
        });

        const data = await res.json();
        const box = document.getElementById('chatBox');

        box.innerHTML = '';

        if (!data.messages || data.messages.length === 0) {
            box.innerHTML = `
                <div class="h-full flex items-center justify-center text-center">
                    <div>
                        <p class="text-gray-400">No messages yet.</p>
                        <p class="text-gray-500 text-sm mt-1">Send the first message to the motorist.</p>
                    </div>
                </div>
            `;
            return;
        }

        data.messages.forEach(msg => {
            const isShop = msg.sender_type === 'shop';

            box.innerHTML += `
                <div class="${isShop ? 'text-right' : 'text-left'}">
                    <div class="inline-block max-w-[75%] px-4 py-3 rounded-2xl text-sm ${
                        isShop
                            ? 'bg-[#F7941D] text-black rounded-br-sm'
                            : 'bg-white/10 text-white rounded-bl-sm'
                    }">
                        <div>${escapeHtml(msg.message)}</div>
                        <div class="text-[10px] mt-1 opacity-60">
                            ${formatTime(msg.created_at)}
                        </div>
                    </div>
                </div>
            `;
        });

        box.scrollTop = box.scrollHeight;

    } catch (error) {
        console.error(error);
    }
}

async function sendMsg() {
    const input = document.getElementById('msgInput');

    if (!dispatchId) {
        alert('Please select a conversation first.');
        return;
    }

    const message = input.value.trim();

    if (!message) return;

    input.disabled = true;
    document.getElementById('sendButton').disabled = true;

    try {
        const res = await fetch('/api/shop/messages/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({
                dispatch_id: dispatchId,
                message: message
            })
        });

        const data = await res.json();

        if (data.success) {
            input.value = '';
            await loadMessages();
        } else {
            alert(data.message || 'Failed to send message.');
        }

    } catch (error) {
        alert('Error sending message.');
    } finally {
        input.disabled = false;
        document.getElementById('sendButton').disabled = false;
        input.focus();
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.innerText = text ?? '';
    return div.innerHTML;
}

function formatTime(dateString) {
    if (!dateString) return '';

    const date = new Date(dateString);
    return date.toLocaleTimeString([], {
        hour: '2-digit',
        minute: '2-digit'
    });
}
</script>

@endsection