@extends('layouts.shop')

@section('content')

<div class="page-header">
    <div class="page-pretitle">Shop</div>
    <h1 class="page-title">Messages</h1>
</div>

<div class="t-card" style="overflow:hidden; height:calc(100vh - 160px); min-height:400px; display:flex;">

    {{-- Conversations List --}}
    <div id="conv-list" style="width:300px; flex-shrink:0; border-right:1px solid #e6e7eb; overflow-y:auto;">
        <div style="padding:14px 16px; border-bottom:1px solid #e6e7eb;">
            <p style="font-size:13px; font-weight:700; color:#1d273b; margin:0;">Conversations</p>
        </div>
        @forelse($conversations as $conv)
        @php
            $partner = ($conv->sender_id === auth()->id()) ? $conv->receiver : $conv->sender;
            $lastMsg  = $conv->messages->last();
        @endphp
        <div onclick="openChat({{ $partner->id ?? 0 }}, '{{ addslashes($partner->name ?? 'Unknown') }}')"
             id="conv-item-{{ $partner->id ?? 0 }}"
             style="padding:12px 16px; cursor:pointer; border-bottom:1px solid #f0f2f5; transition:background .1s;"
             onmouseover="this.style.background='#f4f6fb'" onmouseout="this.style.background=activeConvId==={{ $partner->id ?? 0 }}?'#edf2f9':''">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:36px; height:36px; background:#206bc4; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <span style="font-size:13px; font-weight:700; color:#fff;">{{ strtoupper(substr($partner->name ?? 'U', 0, 1)) }}</span>
                </div>
                <div style="min-width:0; flex:1;">
                    <p style="font-size:13px; font-weight:600; color:#1d273b; margin:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $partner->name ?? 'Unknown' }}</p>
                    <p style="font-size:12px; color:#a0a8b1; margin:2px 0 0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $lastMsg ? Str::limit($lastMsg->message, 30) : 'No messages' }}</p>
                </div>
            </div>
        </div>
        @empty
        <div style="padding:32px 16px; text-align:center;">
            <i class="fas fa-comment-slash" style="font-size:28px; color:#c8ccd0; display:block; margin-bottom:8px;"></i>
            <p style="font-size:13px; color:#a0a8b1; margin:0;">No conversations yet</p>
        </div>
        @endforelse
    </div>

    {{-- Chat Panel --}}
    <div style="flex:1; display:flex; flex-direction:column; min-width:0;">

        {{-- Empty state --}}
        <div id="chat-empty" style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#a0a8b1;">
            <i class="fas fa-comments" style="font-size:40px; margin-bottom:12px;"></i>
            <p style="font-size:14px; margin:0;">Select a conversation to start messaging</p>
        </div>

        {{-- Active chat --}}
        <div id="chat-active" style="display:none; flex:1; flex-direction:column;">
            <div style="padding:12px 18px; border-bottom:1px solid #e6e7eb; display:flex; align-items:center; gap:10px;">
                <div style="width:34px; height:34px; background:#206bc4; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <span id="chat-avatar" style="font-size:13px; font-weight:700; color:#fff;">?</span>
                </div>
                <p id="chat-name" style="font-size:14px; font-weight:700; color:#1d273b; margin:0;">—</p>
            </div>
            <div id="chat-messages" style="flex:1; overflow-y:auto; padding:16px; display:flex; flex-direction:column; gap:8px;"></div>
            <div style="padding:12px 16px; border-top:1px solid #e6e7eb; display:flex; gap:8px;">
                <input id="msg-input" type="text" placeholder="Type a message..." class="form-control" style="flex:1;"
                    onkeydown="if(event.key==='Enter')sendMsg()">
                <button onclick="sendMsg()" class="btn btn-primary" style="flex-shrink:0;"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </div>
</div>

<script>
let activeConvId = null;
let msgInterval  = null;

function openChat(partnerId, partnerName) {
    activeConvId = partnerId;
    document.getElementById('chat-empty').style.display  = 'none';
    document.getElementById('chat-active').style.display = 'flex';
    document.getElementById('chat-name').textContent     = partnerName;
    document.getElementById('chat-avatar').textContent   = partnerName.charAt(0).toUpperCase();
    document.querySelectorAll('[id^="conv-item-"]').forEach(el => el.style.background = '');
    const ci = document.getElementById('conv-item-'+partnerId);
    if(ci) ci.style.background = '#edf2f9';
    loadMessages();
    if(msgInterval) clearInterval(msgInterval);
    msgInterval = setInterval(loadMessages, 3000);
}

function loadMessages() {
    if(!activeConvId) return;
    fetch(`/api/shop/messages/${activeConvId}`)
        .then(r => r.json())
        .then(msgs => {
            const box = document.getElementById('chat-messages');
            const atBottom = box.scrollHeight - box.scrollTop - box.clientHeight < 40;
            box.innerHTML = '';
            msgs.forEach(msg => {
                const mine = msg.sender_id === {{ auth()->id() }};
                const div = document.createElement('div');
                div.style.cssText = `display:flex; justify-content:${mine ? 'flex-end' : 'flex-start'};`;
                div.innerHTML = `<div style="max-width:68%; padding:8px 12px; border-radius:${mine ? '12px 12px 2px 12px' : '12px 12px 12px 2px'};
                    background:${mine ? '#206bc4' : '#f4f6fb'}; color:${mine ? '#fff' : '#1d273b'}; font-size:13px; line-height:1.5;">
                    ${escapeHtml(msg.message)}
                    <div style="font-size:10px; opacity:.65; margin-top:3px; text-align:${mine ? 'right' : 'left'}">${formatTime(msg.created_at)}</div>
                </div>`;
                box.appendChild(div);
            });
            if(atBottom) box.scrollTop = box.scrollHeight;
        }).catch(() => {});
}

function sendMsg() {
    const input = document.getElementById('msg-input');
    const msg   = input.value.trim();
    if(!msg || !activeConvId) return;
    input.value = '';
    fetch('/api/shop/messages/send', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '', 'Content-Type': 'application/json'},
        body: JSON.stringify({ receiver_id: activeConvId, message: msg })
    }).then(() => loadMessages()).catch(() => {});
}

function escapeHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function formatTime(ts) {
    if(!ts) return '';
    const d = new Date(ts);
    return d.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
}
</script>

@endsection
