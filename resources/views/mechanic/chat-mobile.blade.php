@extends('layouts.mechanic-mobile')

@section('hide-bottom-nav')
@endsection

@section('main-class', '')

@section('content')
<style>
    #chatApp {
        position: fixed; inset: 0;
        display: flex; flex-direction: column;
        background: var(--surface-2);
        font-family: Inter, system-ui, sans-serif;
        color: var(--text-1);
        max-width: 430px; margin: 0 auto;
    }
    #chatHeader {
        flex: 0 0 auto;
        background: var(--surface);
        border-bottom: 1px solid var(--border);
        padding: 12px 14px 12px;
        z-index: 10;
    }
    .ch-row { display: flex; align-items: center; gap: 10px; }
    .ch-back {
        width: 36px; height: 36px; border-radius: 50%;
        background: var(--surface-2); border: 1px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        font-size: 14px; color: var(--text-2); cursor: pointer; flex-shrink: 0;
        text-decoration: none;
    }
    .ch-avatar {
        width: 40px; height: 40px; border-radius: 50%;
        background: var(--brand-bg); color: var(--brand);
        border: 1.5px solid rgba(247,148,29,.25);
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; flex-shrink: 0;
    }
    .ch-info { flex: 1; min-width: 0; }
    .ch-name { font-size: 15px; font-weight: 700; color: var(--text-1); line-height: 1.2; }
    .ch-sub  { font-size: 11px; color: var(--text-2); margin-top: 1px; }
    .ch-badge {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 10px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase;
        padding: 3px 8px; border-radius: 20px;
        background: var(--brand-bg); color: var(--brand);
        border: 1px solid rgba(247,148,29,.2);
    }
    #chatBox {
        flex: 1; overflow-y: auto;
        padding: 16px 14px 8px; display: flex; flex-direction: column; gap: 4px;
        -webkit-overflow-scrolling: touch;
    }
    .msg-sep {
        text-align: center; font-size: 10px; font-weight: 600;
        color: var(--text-3); letter-spacing: .06em; text-transform: uppercase; margin: 10px 0 6px;
    }
    .msg-row { display: flex; margin-bottom: 2px; }
    .msg-row.mine   { justify-content: flex-end; }
    .msg-row.theirs { justify-content: flex-start; }
    .msg-wrap { max-width: 78%; display: flex; flex-direction: column; }
    .msg-row.mine   .msg-wrap { align-items: flex-end; }
    .msg-row.theirs .msg-wrap { align-items: flex-start; }
    .msg-sender { font-size: 10px; font-weight: 600; color: var(--text-3); margin-bottom: 3px; display: flex; align-items: center; gap: 4px; }
    .msg-bubble {
        padding: 10px 14px; border-radius: 18px;
        font-size: 14px; line-height: 1.5; word-break: break-word;
    }
    .msg-row.mine   .msg-bubble { background: var(--brand); color: #fff; border-bottom-right-radius: 4px; }
    .msg-row.theirs .msg-bubble { background: var(--surface); border: 1px solid var(--border); color: var(--text-1); border-bottom-left-radius: 4px; }
    .msg-time { font-size: 10px; color: var(--text-3); margin-top: 3px; padding: 0 4px; }
    #typingIndicator {
        padding: 4px 14px 8px;
        font-size: 11px; color: var(--text-3);
        display: none; align-items: center; gap: 6px;
        min-height: 24px;
    }
    .typing-dots { display: flex; gap: 3px; align-items: center; }
    .typing-dots span {
        width: 5px; height: 5px; border-radius: 50%;
        background: var(--text-3);
        animation: typingBounce 1.2s ease-in-out infinite;
    }
    .typing-dots span:nth-child(2) { animation-delay: .2s; }
    .typing-dots span:nth-child(3) { animation-delay: .4s; }
    @keyframes typingBounce {
        0%, 60%, 100% { transform: translateY(0); opacity: .4; }
        30% { transform: translateY(-4px); opacity: 1; }
    }
    #chatEmpty {
        flex: 1; display: flex; flex-direction: column;
        align-items: center; justify-content: center; gap: 10px;
        color: var(--text-3); padding: 40px 0;
    }
    .empty-icon {
        width: 56px; height: 56px; border-radius: 50%;
        background: var(--brand-bg); color: var(--brand);
        display: flex; align-items: center; justify-content: center; font-size: 22px;
    }
    #chatFooter {
        flex: 0 0 auto; background: var(--surface);
        border-top: 1px solid var(--border);
        padding: 10px 14px;
        padding-bottom: max(10px, env(safe-area-inset-bottom));
    }
    .cf-row { display: flex; align-items: flex-end; gap: 8px; }
    #msgInput {
        flex: 1; background: var(--surface-2);
        border: 1.5px solid var(--border); border-radius: 22px;
        padding: 10px 16px; font-size: 14px; font-family: inherit;
        color: var(--text-1); outline: none; resize: none; max-height: 120px;
        line-height: 1.4; transition: border-color .15s;
    }
    #msgInput:focus { border-color: var(--brand); }
    #msgInput::placeholder { color: var(--text-3); }
    #sendBtn {
        width: 42px; height: 42px; flex-shrink: 0; border-radius: 50%;
        background: var(--brand); color: #fff; border: none; cursor: pointer;
        display: flex; align-items: center; justify-content: center; font-size: 16px;
        transition: opacity .15s, transform .1s;
    }
    #sendBtn:active { opacity: .85; transform: scale(.94); }
    #sendBtn:disabled { opacity: .35; cursor: not-allowed; }
</style>

<div id="chatApp">
    <div id="chatHeader">
        <div class="ch-row">
            <a href="{{ route('mechanic.messages') }}" class="ch-back">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div class="ch-avatar">
                <i class="fa-solid fa-user"></i>
            </div>
            <div class="ch-info">
                <div class="ch-name">{{ $dispatch->motorist->name ?? ($dispatch->guest_name ?? 'Motorist') }}</div>
                <div class="ch-sub">
                    <i class="fa-solid fa-store" style="font-size:9px;color:var(--brand);"></i>
                    {{ $dispatch->shop->shop_name ?? 'Dispatch #' . $dispatch->id }}
                </div>
            </div>
            <span class="ch-badge">
                <i class="fa-solid fa-wrench" style="font-size:9px;"></i>
                Mechanic
            </span>
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

    <div id="chatFooter">
        <div class="cf-row">
            <textarea id="msgInput" rows="1" placeholder="Type a message…"></textarea>
            <button id="sendBtn" type="button"><i class="fa-solid fa-paper-plane"></i></button>
        </div>
    </div>
</div>

<script>
(function () {
    const DISPATCH_ID  = {{ $dispatch->id }};
    const CSRF         = '{{ csrf_token() }}';

    // conversation_type from URL: 'mechanic' means chatting with shop, default means chatting with motorist
    const urlParams      = new URLSearchParams(window.location.search);
    const rawConvType    = urlParams.get('conversation_type') || 'motorist';
    // The API uses 'shop' when mechanic is talking to shop, 'motorist' for motorist conversation
    const convType       = rawConvType === 'mechanic' ? 'shop' : rawConvType;
    const partnerLabel   = convType === 'shop' ? 'Shop' : 'Motorist';
    const partnerIcon    = convType === 'shop' ? 'fa-store' : 'fa-user';

    const chatBox     = document.getElementById('chatBox');
    const chatEmpty   = document.getElementById('chatEmpty');
    const msgInput    = document.getElementById('msgInput');
    const sendBtn     = document.getElementById('sendBtn');
    const typingEl    = document.getElementById('typingIndicator');
    const typingText  = document.getElementById('typingText');

    let channel       = null;
    let typingTimer   = null;
    let isTyping      = false;
    let hideTypingTmr = null;
    const seenIds     = new Set();

    /* ── HELPERS ── */
    function esc(s) {
        if (!s) return '';
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
    }
    function fmtTime(val) {
        if (!val) return '';
        let d = new Date(val);
        if (isNaN(d) && typeof val==='string') d = new Date(val.replace(' ','T'));
        return isNaN(d) ? '' : d.toLocaleTimeString([], {hour:'numeric',minute:'2-digit'});
    }
    function fmtDate(val) {
        if (!val) return '';
        const d = new Date(typeof val==='string' ? val.replace(' ','T') : val);
        if (isNaN(d)) return '';
        const today = new Date(), yesterday = new Date(today);
        yesterday.setDate(today.getDate()-1);
        if (d.toDateString()===today.toDateString()) return 'Today';
        if (d.toDateString()===yesterday.toDateString()) return 'Yesterday';
        return d.toLocaleDateString([],{weekday:'short',month:'short',day:'numeric'});
    }

    /* ── RENDER ── */
    function renderMessages(msgs) {
        chatBox.innerHTML = '';
        seenIds.clear();
        if (!msgs.length) { chatBox.appendChild(chatEmpty); chatEmpty.style.display='flex'; return; }
        chatEmpty.style.display = 'none';
        let lastDate = null;
        msgs.forEach(m => {
            const d = fmtDate(m.created_at);
            if (d !== lastDate) {
                const sep = document.createElement('div');
                sep.className = 'msg-sep'; sep.textContent = d;
                chatBox.appendChild(sep); lastDate = d;
            }
            appendBubble(m, false);
        });
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function appendBubble(msg, scroll=true) {
        if (seenIds.has(msg.id)) return;
        seenIds.add(msg.id);
        const mine = msg.sender_type === 'mechanic';
        const name = mine ? 'You' : (msg.sender_name || partnerLabel);
        const icon = mine ? 'fa-user-gear' : partnerIcon;
        const row = document.createElement('div');
        row.className = 'msg-row ' + (mine ? 'mine' : 'theirs');
        row.innerHTML = '<div class="msg-wrap">'
            + '<div class="msg-sender"><i class="fa-solid ' + esc(icon) + '" style="font-size:9px;"></i>' + esc(name) + '</div>'
            + '<div class="msg-bubble">' + esc(msg.message) + '</div>'
            + '<div class="msg-time">' + esc(fmtTime(msg.created_at)) + '</div>'
            + '</div>';
        chatBox.appendChild(row);
        if (scroll) chatBox.scrollTop = chatBox.scrollHeight;
    }

    /* ── LOAD ── */
    function loadMessages() {
        fetch('/api/mechanic/messages/' + DISPATCH_ID + '?conversation_type=' + convType, {
            headers: {'X-CSRF-TOKEN': CSRF}
        })
        .then(r => r.json())
        .then(d => { if (d.success) renderMessages(d.messages); })
        .catch(function(){});
    }

    /* ── SEND ── */
    function sendMessage() {
        const text = msgInput.value.trim();
        if (!text) return;
        sendBtn.disabled = true;
        msgInput.value = ''; autoResize();
        stopTyping();
        fetch('/api/mechanic/messages/send', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
            body: JSON.stringify({dispatch_id: DISPATCH_ID, message: text, conversation_type: convType})
        }).then(function(r){ return r.json(); }).then(function(d){
            if (d.success) {
                if (d.message) appendBubble(d.message);
                loadMessages();
            }
        }).catch(function(){}).finally(function(){ sendBtn.disabled=false; });
    }

    sendBtn.addEventListener('click', sendMessage);
    msgInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
    });
    function autoResize() { msgInput.style.height='auto'; msgInput.style.height=Math.min(msgInput.scrollHeight,120)+'px'; }
    msgInput.addEventListener('input', autoResize);

    /* ── TYPING ── */
    function startTyping() {
        if (!channel || isTyping) return;
        isTyping = true;
        try { channel.trigger('client-typing', {sender:'mechanic', tab:convType}); } catch(e){}
    }
    function stopTyping() {
        if (!channel || !isTyping) return;
        isTyping = false;
        try { channel.trigger('client-stop-typing', {sender:'mechanic', tab:convType}); } catch(e){}
    }
    msgInput.addEventListener('input', function() {
        if (msgInput.value.trim()) {
            startTyping();
            clearTimeout(typingTimer);
            typingTimer = setTimeout(stopTyping, 2500);
        } else { stopTyping(); }
    });
    function showTyping(name) {
        typingText.textContent = name + ' is typing\u2026';
        typingEl.style.display = 'flex';
    }
    function hideTyping() {
        typingEl.style.display = 'none';
        typingText.textContent = '';
    }

    /* ── PUSHER ── */
    function subscribeChannel() {
        if (typeof Pusher === 'undefined' || !window.pusherKey) {
            setTimeout(subscribeChannel, 300); return;
        }
        var pusher = new Pusher(window.pusherKey, {
            cluster: window.pusherCluster,
            forceTLS: true,
            channelAuthorization: {
                endpoint: '/broadcasting/auth',
                transport: 'ajax',
                headers: {'X-CSRF-TOKEN': CSRF}
            }
        });
        channel = pusher.subscribe('private-dispatch.' + DISPATCH_ID);
        channel.bind('pusher:subscription_error', function(err) {
            console.error('[Pusher] sub error', err);
        });
        channel.bind('pusher:subscription_succeeded', function() {
            console.log('[Pusher] subscribed to private-dispatch.' + DISPATCH_ID);
        });

        channel.bind('message.sent', function(event) {
            var m = event.message;
            if (!m || m.dispatch_id !== DISPATCH_ID) return;
            // expectedBackend: when viewing motorist conversation, expect 'mechanic' or 'motorist' conversation_types
            var expectedBackend = convType === 'motorist' ? ['mechanic','motorist'] : ['shop'];
            if (m.conversation_type && expectedBackend.indexOf(m.conversation_type) === -1) return;
            hideTyping();
            appendBubble(m);
        });

        channel.bind('client-typing', function(event) {
            if (event.sender === 'mechanic') return;
            var label = event.sender === 'shop' ? 'Shop' : 'Motorist';
            showTyping(label);
            clearTimeout(hideTypingTmr);
            hideTypingTmr = setTimeout(hideTyping, 3000);
        });
        channel.bind('client-stop-typing', function(event) {
            if (event.sender === 'mechanic') return;
            clearTimeout(hideTypingTmr);
            hideTyping();
        });
    }

    loadMessages();
    subscribeChannel();
})();
</script>
@endsection