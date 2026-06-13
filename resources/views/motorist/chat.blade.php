@extends('layouts.motorist')

@push('styles')
    <style>
        :root {
            --brand: #F7941D;
            --brand-dk: #C87010;
            --brand-bg: rgba(247, 148, 29, .09);
            --surface: #FFFFFF;
            --surface-2: #F5F6F8;
            --border: #E4E7EC;
            --text-1: #111827;
            --text-2: #6B7280;
            --text-3: #9CA3AF;
            --action: #1E293B;
            --r2: 10px;
            --r3: 14px;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        #chatApp {
            position: fixed;
            inset: 0;
            display: flex;
            flex-direction: column;
            background: var(--surface-2);
            font-family: Inter, system-ui, sans-serif;
            color: var(--text-1);
            max-width: 430px;
            margin: 0 auto;
        }

        #chatHeader {
            flex: 0 0 auto;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 12px 14px 0;
            z-index: 10;
        }

        .ch-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 12px;
        }

        .ch-back {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--surface-2);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: var(--text-2);
            cursor: pointer;
            flex-shrink: 0;
            text-decoration: none;
        }

        .ch-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--brand-bg);
            color: var(--brand);
            border: 1.5px solid rgba(247, 148, 29, .25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .ch-info {
            flex: 1;
            min-width: 0;
        }

        .ch-name {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-1);
            line-height: 1.2;
        }

        .ch-sub {
            font-size: 11px;
            color: var(--text-2);
            margin-top: 1px;
        }

        .ch-tabs {
            display: flex;
            gap: 6px;
            padding: 10px 0 12px;
            border-top: 1px solid var(--border);
        }

        .ch-tab {
            flex: 1;
            padding: 8px 0;
            border-radius: var(--r2);
            font-size: 12px;
            font-weight: 700;
            border: 1.5px solid var(--border);
            background: var(--surface-2);
            color: var(--text-2);
            cursor: pointer;
            transition: all .15s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .ch-tab.active {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
        }

        #chatBox {
            flex: 1;
            overflow-y: auto;
            padding: 16px 14px 8px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            -webkit-overflow-scrolling: touch;
        }

        .msg-sep {
            text-align: center;
            font-size: 10px;
            font-weight: 600;
            color: var(--text-3);
            letter-spacing: .06em;
            text-transform: uppercase;
            margin: 10px 0 6px;
        }

        .msg-row {
            display: flex;
            margin-bottom: 2px;
        }

        .msg-row.mine {
            justify-content: flex-end;
        }

        .msg-row.theirs {
            justify-content: flex-start;
        }

        .msg-wrap {
            max-width: 78%;
            display: flex;
            flex-direction: column;
        }

        .msg-row.mine .msg-wrap {
            align-items: flex-end;
        }

        .msg-row.theirs .msg-wrap {
            align-items: flex-start;
        }

        .msg-sender {
            font-size: 10px;
            font-weight: 600;
            color: var(--text-3);
            margin-bottom: 3px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .msg-bubble {
            padding: 10px 14px;
            border-radius: 18px;
            font-size: 14px;
            line-height: 1.5;
            word-break: break-word;
        }

        .msg-row.mine .msg-bubble {
            background: var(--brand);
            color: #fff;
            border-bottom-right-radius: 4px;
        }

        .msg-row.theirs .msg-bubble {
            background: var(--surface);
            border: 1px solid var(--border);
            color: var(--text-1);
            border-bottom-left-radius: 4px;
        }

        .msg-time {
            font-size: 10px;
            color: var(--text-3);
            margin-top: 3px;
            padding: 0 4px;
        }

        /* Typing indicator */
        #typingIndicator {
            padding: 4px 14px 8px;
            font-size: 11px;
            color: var(--text-3);
            display: none;
            align-items: center;
            gap: 6px;
            min-height: 24px;
        }

        .typing-dots {
            display: flex;
            gap: 3px;
            align-items: center;
        }

        .typing-dots span {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: var(--text-3);
            animation: typingBounce 1.2s ease-in-out infinite;
        }

        .typing-dots span:nth-child(2) {
            animation-delay: .2s;
        }

        .typing-dots span:nth-child(3) {
            animation-delay: .4s;
        }

        @keyframes typingBounce {

            0%,
            60%,
            100% {
                transform: translateY(0);
                opacity: .4;
            }

            30% {
                transform: translateY(-4px);
                opacity: 1;
            }
        }

        #chatEmpty {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: var(--text-3);
            padding: 40px 0;
        }

        .empty-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: var(--brand-bg);
            color: var(--brand);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        #chatClosed {
            display: none;
            margin: 8px 14px;
            background: rgba(239, 68, 68, .07);
            border: 1px solid rgba(239, 68, 68, .2);
            border-radius: var(--r2);
            padding: 10px 13px;
            font-size: 12px;
            color: #EF4444;
            text-align: center;
        }

        #chatFooter {
            flex: 0 0 auto;
            background: var(--surface);
            border-top: 1px solid var(--border);
            padding: 10px 14px;
            padding-bottom: max(10px, env(safe-area-inset-bottom));
        }

        .cf-row {
            display: flex;
            align-items: flex-end;
            gap: 8px;
        }

        #msgInput {
            flex: 1;
            background: var(--surface-2);
            border: 1.5px solid var(--border);
            border-radius: 22px;
            padding: 10px 16px;
            font-size: 14px;
            font-family: inherit;
            color: var(--text-1);
            outline: none;
            resize: none;
            max-height: 120px;
            line-height: 1.4;
            transition: border-color .15s;
        }

        #msgInput:focus {
            border-color: var(--brand);
        }

        #msgInput::placeholder {
            color: var(--text-3);
        }

        #sendBtn {
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            border-radius: 50%;
            background: var(--brand);
            color: #fff;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            transition: opacity .15s, transform .1s;
        }

        #sendBtn:active {
            opacity: .85;
            transform: scale(.94);
        }

        #sendBtn:disabled {
            opacity: .35;
            cursor: not-allowed;
        }
    </style>
@endpush

@section('content')
    <div id="chatApp">
        <div id="chatHeader">
            <div class="ch-row">
                <a href="{{ route('motorist.index') }}" class="ch-back">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div class="ch-avatar"><i class="fa-solid fa-store"></i></div>
                <div class="ch-info">
                    <div class="ch-name">{{ $dispatch->shop_name ?? 'Chat' }}</div>
                    <div class="ch-sub">
                        <i class="fa-solid fa-motorcycle" style="font-size:9px;color:var(--brand);"></i>
                        {{ $dispatch->mechanic_name ?? 'Awaiting mechanic' }}
                    </div>
                </div>
            </div>
            <div class="ch-tabs">
                <button class="ch-tab active" id="tabMechanic" onclick="switchTab('mechanic')">
                    <i class="fa-solid fa-wrench"></i> Mechanic
                </button>
                <button class="ch-tab" id="tabShop" onclick="switchTab('shop')">
                    <i class="fa-solid fa-store"></i> Shop
                </button>
            </div>
        </div>

        <div id="chatBox">
            <div id="chatEmpty">
                <div class="empty-icon"><i class="fa-solid fa-comments"></i></div>
                <div style="font-size:13px;font-weight:600;color:var(--text-2);">No messages yet</div>
                <div style="font-size:11px;">Send a message to get started</div>
            </div>
        </div>

        <div id="typingIndicator">
            <div class="typing-dots"><span></span><span></span><span></span></div>
            <span id="typingText"></span>
        </div>

        <div id="chatClosed">
            <i class="fa-solid fa-lock" style="margin-right:5px;"></i>Chat is closed — rescue has ended
        </div>

        <div id="chatFooter">
            <div class="cf-row">
                <textarea id="msgInput" rows="1" placeholder="Type a message…"></textarea>
                <button id="sendBtn" type="button"><i class="fa-solid fa-paper-plane"></i></button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        (function() {
            const DISPATCH_ID = {{ $dispatch->id }};
            const CURRENT_USER = {{ auth()->id() ?? 'null' }};
            const CSRF = '{{ csrf_token() }}';
            const STATUS = '{{ $dispatch->status }}';
            const chatBox = document.getElementById('chatBox');
            const chatEmpty = document.getElementById('chatEmpty');
            const chatClosed = document.getElementById('chatClosed');
            const chatFooter = document.getElementById('chatFooter');
            const msgInput = document.getElementById('msgInput');
            const sendBtn = document.getElementById('sendBtn');
            const typingEl = document.getElementById('typingIndicator');
            const typingText = document.getElementById('typingText');

            let activeTab = 'mechanic';
            const seenIds = new Set();
            let channel = null;
            let typingTimer = null;
            let isTyping = false;

            /* ── TABS ── */
            window.switchTab = function(tab) {
                activeTab = tab;
                document.getElementById('tabMechanic').classList.toggle('active', tab === 'mechanic');
                document.getElementById('tabShop').classList.toggle('active', tab === 'shop');
                loadMessages();
            };

            function apiConvType() {
                return activeTab === 'shop' ? 'motorist' : 'mechanic';
            }

            /* ── HELPERS ── */
            function esc(s) {
                if (!s) return '';
                return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g,
                    '&quot;').replace(/'/g, '&#039;');
            }

            function fmtTime(val) {
                if (!val) return '';
                let d = new Date(val);
                if (isNaN(d) && typeof val === 'string') d = new Date(val.replace(' ', 'T'));
                return isNaN(d) ? '' : d.toLocaleTimeString([], {
                    hour: 'numeric',
                    minute: '2-digit'
                });
            }

            function fmtDate(val) {
                if (!val) return '';
                const d = new Date(typeof val === 'string' ? val.replace(' ', 'T') : val);
                if (isNaN(d)) return '';
                const today = new Date(),
                    yesterday = new Date(today);
                yesterday.setDate(today.getDate() - 1);
                if (d.toDateString() === today.toDateString()) return 'Today';
                if (d.toDateString() === yesterday.toDateString()) return 'Yesterday';
                return d.toLocaleDateString([], {
                    weekday: 'short',
                    month: 'short',
                    day: 'numeric'
                });
            }

            /* ── RENDER ── */
            function renderMessages(msgs) {
                chatBox.innerHTML = '';
                seenIds.clear();
                if (!msgs.length) {
                    chatBox.appendChild(chatEmpty);
                    chatEmpty.style.display = 'flex';
                    return;
                }
                chatEmpty.style.display = 'none';
                let lastDate = null;
                msgs.forEach(m => {
                    const d = fmtDate(m.created_at);
                    if (d !== lastDate) {
                        const sep = document.createElement('div');
                        sep.className = 'msg-sep';
                        sep.textContent = d;
                        chatBox.appendChild(sep);
                        lastDate = d;
                    }
                    appendBubble(m, false);
                });
                chatBox.scrollTop = chatBox.scrollHeight;
            }

            function appendBubble(msg, scroll = true) {
                if (seenIds.has(msg.id)) return;
                seenIds.add(msg.id);
                const mine = msg.sender_type === 'motorist';
                const name = mine ? 'You' : (msg.sender_name || (msg.sender_type === 'shop' ? 'Shop' : 'Mechanic'));
                const icon = mine ? 'fa-user' : (msg.sender_type === 'shop' ? 'fa-store' : 'fa-user-gear');
                const row = document.createElement('div');
                row.className = `msg-row ${mine?'mine':'theirs'}`;
                row.innerHTML = `<div class="msg-wrap">
            <div class="msg-sender"><i class="fa-solid ${esc(icon)}" style="font-size:9px;"></i>${esc(name)}</div>
            <div class="msg-bubble">${esc(msg.message)}</div>
            <div class="msg-time">${esc(fmtTime(msg.created_at))}</div>
        </div>`;
                chatBox.appendChild(row);
                if (scroll) chatBox.scrollTop = chatBox.scrollHeight;
            }

            /* ── LOAD ── */
            function loadMessages() {
                fetch(`/api/chat/${DISPATCH_ID}?conversation_type=${apiConvType()}`, {
                        headers: {
                            'X-CSRF-TOKEN': CSRF
                        }
                    })
                    .then(r => r.json())
                    .then(d => {
                        if (d.success) renderMessages(d.messages);
                    })
                    .catch(() => {});
            }

            /* ── SEND ── */
            function sendMessage() {
                const text = msgInput.value.trim();
                if (!text) return;
                sendBtn.disabled = true;
                msgInput.value = '';
                autoResize();
                stopTyping();
                fetch('/api/messages', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF
                    },
                    body: JSON.stringify({
                        dispatch_id: DISPATCH_ID,
                        message: text,
                        sender_type: 'motorist',
                        motorist_id: CURRENT_USER,
                        conversation_type: apiConvType(),
                    })
                }).then(r => r.json()).then(d => {
                    if (d.success) {
                        if (d.data) appendBubble(d.data); // show immediately
                        loadMessages(); // sync full list in background
                    }
                }).catch(() => {}).finally(() => {
                    sendBtn.disabled = false;
                });
            }

            sendBtn.addEventListener('click', sendMessage);
            msgInput.addEventListener('keydown', e => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });

            function autoResize() {
                msgInput.style.height = 'auto';
                msgInput.style.height = Math.min(msgInput.scrollHeight, 120) + 'px';
            }
            msgInput.addEventListener('input', autoResize);

            /* ── TYPING INDICATOR ── */
            function startTyping() {
                if (!channel || isTyping) return;
                isTyping = true;
                try {
                    channel.trigger('client-typing', {
                        sender: 'motorist',
                        tab: apiConvType()
                    });
                } catch (e) {}
            }

            function stopTyping() {
                if (!channel || !isTyping) return;
                isTyping = false;
                try {
                    channel.trigger('client-stop-typing', {
                        sender: 'motorist',
                        tab: apiConvType()
                    });
                } catch (e) {}
            }

            msgInput.addEventListener('input', () => {
                if (msgInput.value.trim()) {
                    startTyping();
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(stopTyping, 2500);
                } else {
                    stopTyping();
                }
            });

            function showTyping(name) {
                typingText.textContent = name + ' is typing…';
                typingEl.style.display = 'flex';
            }
            let hideTypingTimer = null;

            function hideTyping() {
                typingEl.style.display = 'none';
                typingText.textContent = '';
            }

            /* ── CLOSED STATE ── */
            if (['completed', 'cancelled'].includes(STATUS)) {
                chatClosed.style.display = 'block';
                chatFooter.style.display = 'none';
            }

            /* ── REAL-TIME via Pusher private channel ── */
            function subscribeChannel() {
                if (!window.Pusher) {
                    setTimeout(subscribeChannel, 300);
                    return;
                }

                const pusher = new Pusher(window.pusherKey, {
                    cluster: window.pusherCluster,
                    forceTLS: true,
                    // Auth for private channel
                    channelAuthorization: {
                        endpoint: '/broadcasting/auth',
                        transport: 'ajax',
                        headers: {
                            'X-CSRF-TOKEN': CSRF
                        }
                    }
                });

                // Subscribe to the correct private channel that MessageSent broadcasts on
                channel = pusher.subscribe('private-dispatch.' + DISPATCH_ID);

                channel.bind('pusher:subscription_error', (err) => {
                    console.error('[Pusher] subscription error', err);
                });

                channel.bind('pusher:subscription_succeeded', () => {
                    console.log('[Pusher] subscribed to private-dispatch.' + DISPATCH_ID);
                });

                // Real-time message (from OTHER users — own messages use appendBubble in sendMessage)
                channel.bind('message.sent', event => {
                    const m = event.message;
                    if (!m || m.dispatch_id !== DISPATCH_ID) return;
                    if (m.sender_type === 'motorist') return; // own message already shown
                    if (m.conversation_type && m.conversation_type !== apiConvType()) return;
                    hideTyping();
                    appendBubble(m);
                });

                // Typing indicators (client events — relayed by Pusher, no server needed)
                channel.bind('client-typing', event => {
                    if (event.sender === 'motorist') return; // ignore own events
                    const label = event.sender === 'shop' ? 'Shop' : 'Mechanic';
                    showTyping(label);
                    clearTimeout(hideTypingTimer);
                    hideTypingTimer = setTimeout(hideTyping, 3000);
                });

                channel.bind('client-stop-typing', event => {
                    if (event.sender === 'motorist') return;
                    clearTimeout(hideTypingTimer);
                    hideTyping();
                });
            }

            loadMessages();
            subscribeChannel();
        })();
    </script>
@endsection
