@extends('layouts.shop')

@section('content')



<style>
    .shop-chat-panel {
        display: none;
        flex: 1 1 auto;
        flex-direction: column;
        min-height: 0;
        overflow: hidden;
    }
    #chat-messages {
        flex: 1 1 0;
        min-height: 0;
        overflow-y: auto;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .hidden {
        display: none;
    }
    .shop-messages-card {
        overflow: hidden;
        display: flex;
        min-width: 0;
        min-height: 0;
        height: calc(90vh - 88px);
        max-height: calc(90vh - 88px);
    }
    @media (max-width: 1200px) {
        .shop-messages-card {
            height: calc(100vh - 96px);
            max-height: calc(100vh - 96px);
        }
    }
    @media (max-width: 900px) {
        .shop-messages-card {
            height: calc(100vh - 112px);
            max-height: calc(100vh - 112px);
        }
    }
    .conversation-badge {
        position: absolute;
        top: 14px;
        right: 16px;
        min-width: 20px;
        padding: 2px 8px;
        border-radius: 999px;
        background:#f97316;
        color:#fff;
        font-size:11px;
        font-weight:700;
        text-align:center;
    }
</style>

<div style="display:flex; gap:5px; margin-bottom:8px;">
    <button id="tab-motorist" onclick="switchShopTab('motorist')" style="flex:1; padding:5px 5px; border:1px solid #e6e7eb; border-radius:8px; background:#fff; color:#1d273b; font-weight:700; cursor:pointer;">Motorist Chats</button>
    <button id="tab-mechanic" onclick="switchShopTab('mechanic')" style="flex:1; padding:5px 5px; border:1px solid #e6e7eb; border-radius:8px; background:#f4f6fb; color:#6c7280; font-weight:700; cursor:pointer;">Mechanic Chats</button>
</div>

<div class="t-card shop-messages-card">

    {{-- Conversations List --}}
    <div id="conv-list" style="width:300px; flex-shrink:0; border-right:1px solid #e6e7eb; overflow-y:auto;">
        <div style="padding:14px 16px; border-bottom:1px solid #e6e7eb;">
            <p style="font-size:13px; font-weight:700; color:#1d273b; margin:0;">Conversations</p>
        </div>

        <div id="motorist-list">
            @forelse($conversations as $conv)
            <div onclick="openChat({{ $conv->dispatch_id }}, '{{ addslashes($conv->motorist_name) }}', 'motorist')"
                 id="conv-motorist-{{ $conv->dispatch_id }}"
                 data-unread="{{ $conv->unread_count ?? 0 }}"
                 style="padding:12px 16px; cursor:pointer; border-bottom:1px solid #f0f2f5; transition:background .1s; position:relative;"
                 onmouseover="this.style.background='#f4f6fb'" onmouseout="this.style.background='';">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="width:36px; height:36px; background:#206bc4; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <span style="font-size:13px; font-weight:700; color:#fff;">{{ strtoupper(substr($conv->motorist_name ?? 'U', 0, 1)) }}</span>
                    </div>
                    <div style="min-width:0; flex:1;">
                        <p style="font-size:13px; font-weight:600; color:#1d273b; margin:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $conv->motorist_name ?? 'Unknown Motorist' }}</p>
                        <p style="font-size:12px; color:#a0a8b1; margin:2px 0 0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ ucfirst(str_replace('_', ' ', $conv->issue_type ?? 'New request')) }}</p>
                    </div>
                </div>
                @if(!empty($conv->unread_count) && $conv->unread_count > 0)
                <div class="conversation-badge">{{ $conv->unread_count }}</div>
                @endif
            </div>
            @empty
            <div style="padding:32px 16px; text-align:center;">
                <i class="fas fa-comment-slash" style="font-size:28px; color:#c8ccd0; display:block; margin-bottom:8px;"></i>
                <p style="font-size:13px; color:#a0a8b1; margin:0;">No motorist conversations yet</p>
            </div>
            @endforelse
        </div>

        <div id="mechanic-list" class="hidden">
            @forelse($mechanicConversations as $conv)
            <div onclick="openChat({{ $conv->dispatch_id }}, '{{ addslashes($conv->mechanic_name) }}', 'mechanic')"
                 id="conv-mechanic-{{ $conv->dispatch_id }}"
                 data-unread="{{ $conv->unread_count ?? 0 }}"
                 style="padding:12px 16px; cursor:pointer; border-bottom:1px solid #f0f2f5; transition:background .1s; position:relative;"
                 onmouseover="this.style.background='#f4f6fb'" onmouseout="this.style.background='';">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="width:36px; height:36px; background:#f97316; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <span style="font-size:13px; font-weight:700; color:#fff;">{{ strtoupper(substr($conv->mechanic_name ?? 'M', 0, 1)) }}</span>
                    </div>
                    <div style="min-width:0; flex:1;">
                        <p style="font-size:13px; font-weight:600; color:#1d273b; margin:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $conv->mechanic_name ?? 'Mechanic' }}</p>
                        <p style="font-size:12px; color:#a0a8b1; margin:2px 0 0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ ucfirst(str_replace('_', ' ', $conv->issue_type ?? 'New request')) }}</p>
                    </div>
                </div>
                @if(!empty($conv->unread_count) && $conv->unread_count > 0)
                <div class="conversation-badge">{{ $conv->unread_count }}</div>
                @endif
            </div>
            @empty
            <div style="padding:32px 16px; text-align:center;">
                <i class="fas fa-comment-slash" style="font-size:28px; color:#c8ccd0; display:block; margin-bottom:8px;"></i>
                <p style="font-size:13px; color:#a0a8b1; margin:0;">No mechanic conversations yet</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Chat Panel --}}
    <div style="flex:1; display:flex; flex-direction:column; min-width:0;">

        {{-- Empty state --}}
        <div id="chat-empty" style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#a0a8b1;">
            <i class="fas fa-comments" style="font-size:40px; margin-bottom:12px;"></i>
            <p style="font-size:14px; margin:0;">Select a conversation to start messaging</p>
        </div>

        {{-- Active chat --}}
        <div id="chat-active" class="shop-chat-panel" style="height:100%; min-height:0; overflow:hidden;">
            <div style="padding:12px 18px; border-bottom:1px solid #e6e7eb; display:flex; align-items:center; gap:10px; flex-shrink:0; background:#fff; z-index:1;">
                <div style="width:34px; height:34px; background:#206bc4; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <span id="chat-avatar" style="font-size:13px; font-weight:700; color:#fff;">?</span>
                </div>
                <p id="chat-name" style="font-size:14px; font-weight:700; color:#1d273b; margin:0;">—</p>
            </div>
            <div id="chat-messages" style="flex:1 1 0; min-height:0; overflow-y:auto; padding:16px; display:flex; flex-direction:column; gap:8px; box-sizing:border-box;"></div>
            <div style="padding:12px 16px; border-top:1px solid #e6e7eb; display:flex; gap:8px; flex-shrink:0; background:#fff; position:sticky; bottom:0; z-index:2;">
                <input id="msg-input" type="text" placeholder="Type a message..." class="form-control" style="flex:1;"
                    onkeydown="if(event.key==='Enter')sendMsg()">
                <button onclick="sendMsg()" class="btn btn-primary" style="flex-shrink:0;"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </div>
</div>

<script>
let activeDispatchId = null;
let activeTab = 'motorist';
let activeConversationType = null;
let channel = null;
const messageIds = new Set();
const currentUserId = {{ auth()->id() ?? 'null' }};

function switchShopTab(tab) {
    const validTabs = ['motorist', 'mechanic'];
    if (!validTabs.includes(tab)) {
        tab = 'motorist';
    }

    activeTab = tab;
    activeConversationType = tab === 'motorist' ? 'motorist' : 'shop';
    document.getElementById('tab-motorist').style.background = tab === 'motorist' ? '#fff' : '#f4f6fb';
    document.getElementById('tab-motorist').style.color = tab === 'motorist' ? '#1d273b' : '#6c7280';
    document.getElementById('tab-mechanic').style.background = tab === 'mechanic' ? '#fff' : '#f4f6fb';
    document.getElementById('tab-mechanic').style.color = tab === 'mechanic' ? '#1d273b' : '#6c7280';
    document.getElementById('motorist-list').style.display = tab === 'motorist' ? 'block' : 'none';
    document.getElementById('mechanic-list').style.display = tab === 'mechanic' ? 'block' : 'none';
    activeDispatchId = null;
    document.getElementById('chat-empty').style.display = 'flex';
    document.getElementById('chat-active').style.display = 'none';

    const hash = `#${tab}`;
    if (window.location.hash !== hash) {
        history.replaceState(null, '', window.location.pathname + window.location.search + hash);
    }
}

function openChat(dispatchId, partnerName, tabType) {
    activeDispatchId = dispatchId;
    document.getElementById('chat-empty').style.display  = 'none';
    document.getElementById('chat-active').style.display = 'flex';

    const label = tabType === 'mechanic' ? 'Mechanic' : 'Motorist';
    document.getElementById('chat-name').textContent     = `${label}: ${partnerName}`;
    document.getElementById('chat-avatar').textContent   = partnerName.charAt(0).toUpperCase();

    document.querySelectorAll('[id^="conv-motorist-"]').forEach(el => el.style.background = '');
    document.querySelectorAll('[id^="conv-mechanic-"]').forEach(el => el.style.background = '');
    const ci = document.getElementById(`conv-${tabType}-${dispatchId}`);
    if (ci) ci.style.background = '#edf2f9';
    clearConversationBadge(tabType, dispatchId);

    const params = new URLSearchParams(window.location.search);
    params.set('dispatch', dispatchId);
    const newUrl = `${window.location.pathname}?${params.toString()}#${tabType}`;
    history.replaceState(null, '', newUrl);

    // Motorist tab shows motorist↔shop chat, mechanic tab shows shop↔mechanic chat.
    activeConversationType = tabType === 'motorist' ? 'motorist' : 'shop';
    loadMessages();
    subscribeDispatchChannel();
}

function clearConversationBadge(tabType, dispatchId) {
    const el = document.getElementById(`conv-${tabType}-${dispatchId}`);
    if (!el) return;
    el.dataset.unread = '0';
    const badge = el.querySelector('.conversation-badge');
    if (badge) badge.remove();
}

function renderMessages(messages) {
    const box = document.getElementById('chat-messages');
    box.innerHTML = '';
    messageIds.clear();
    messages.forEach(msg => appendMessage(msg, false));
    box.scrollTop = box.scrollHeight;
}

function appendMessage(msg, scroll = true) {
    if (messageIds.has(msg.id)) {
        return;
    }
    messageIds.add(msg.id);
    const box = document.getElementById('chat-messages');
    const isMine = msg.sender_type === 'shop';
    const wrapper = document.createElement('div');
    wrapper.style.display = 'flex';
    wrapper.style.justifyContent = isMine ? 'flex-end' : 'flex-start';

    const bubble = document.createElement('div');
    bubble.style.maxWidth = '84%';
    bubble.innerHTML = `
        <div style="margin-bottom:6px; font-size:11px; color:#6c7280; ${isMine ? 'text-align:right;' : 'text-align:left;'}">
            ${escapeHtml(msg.sender_name || (isMine ? 'You' : 'Shop'))} · ${escapeHtml(formatTime(msg.created_at))}
        </div>
        <div style="border-radius:12px; padding:10px 14px; font-size:14px; line-height:1.4; border:1px solid ${isMine ? '#d9e7ff' : '#d2d9e8'}; background:${isMine ? '#ebf1ff' : '#f4f6fb'}; color:${isMine ? '#1d273b' : '#1d273b'};">
            ${escapeHtml(msg.message)}
        </div>
    `;

    wrapper.appendChild(bubble);
    box.appendChild(wrapper);
    if (scroll) {
        box.scrollTop = box.scrollHeight;
    }
}

function loadMessages() {
    if (!activeDispatchId) return;
    const query = activeConversationType ? `?conversation_type=${activeConversationType}` : '';
    fetch(`/api/shop/messages/${activeDispatchId}${query}`)
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                return;
            }
            renderMessages(data.messages);
            clearConversationBadge(activeTab, activeDispatchId);
        }).catch(() => {});
}

function subscribeDispatchChannel() {
    if (!window.Echo || !window.Echo.private || !activeDispatchId) {
        return;
    }

    if (channel) {
        window.Echo.leave(`private-dispatch.${activeDispatchId}`);
    }

    channel = window.Echo.private(`dispatch.${activeDispatchId}`);
    channel.listen('.message.sent', (event) => {
        if (event.message?.dispatch_id !== activeDispatchId) {
            return;
        }
        // If conversation type is set, ignore messages that don't match
        if (activeConversationType && event.message?.conversation_type && event.message.conversation_type !== activeConversationType) {
            return;
        }
        appendMessage(event.message);
    });
}

function sendMsg() {
    const input = document.getElementById('msg-input');
    const msg   = input.value.trim();
    if(!msg || !activeDispatchId) return;
    input.value = '';
    fetch('/api/shop/messages/send', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '', 'Content-Type': 'application/json'},
        body: JSON.stringify({ dispatch_id: activeDispatchId, message: msg, conversation_type: activeConversationType })
    }).then(() => {
        loadMessages();
    }).catch(() => {});
}

function escapeHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function formatTime(ts) {
    if(!ts) return '';
    let d = new Date(ts);
    if (isNaN(d.getTime()) && typeof ts === 'string') {
        d = new Date(ts.replace(' ', 'T'));
    }
    return isNaN(d.getTime()) ? ts : d.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
}

const params = new URLSearchParams(window.location.search);
const hashTab = window.location.hash.replace('#', '');
const requestedTab = params.get('tab');
const dispatchParam = params.get('dispatch');
const initialTab = ['motorist', 'mechanic'].includes(hashTab)
    ? hashTab
    : (['motorist', 'mechanic'].includes(requestedTab) ? requestedTab : 'motorist');

switchShopTab(initialTab);

if (dispatchParam) {
    const convEl = document.getElementById(`conv-${initialTab}-${dispatchParam}`);
    if (convEl) {
        const nameEl = convEl.querySelector('p');
        const partnerName = nameEl ? nameEl.textContent.trim().split('\n')[0].trim() : '';
        openChat(dispatchParam, partnerName || (initialTab === 'mechanic' ? 'Mechanic' : 'Motorist'), initialTab);
    }
}
</script>

@endsection
