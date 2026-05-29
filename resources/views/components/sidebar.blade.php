@php
    if (!function_exists('shopNavActive')) {
        function shopNavActive($path)
        {
            return request()->is($path) || request()->is($path . '/*') ? 'active' : '';
        }
    }
@endphp

<aside class="t-sidebar">

    {{-- Logo --}}
    <div style="padding:18px 20px; border-bottom:1px solid rgba(255,255,255,.08);">
        <div style="display:flex; align-items:center; gap:10px;">
            <div
                style="width:32px; height:32px; background:#206bc4; border-radius:6px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <i class="fas fa-wrench" style="color:#fff; font-size:13px;"></i>
            </div>
            <div>
                <div style="color:#fff; font-weight:700; font-size:15px; line-height:1.2;">MechFinder</div>
                <div style="color:#667382; font-size:11px;">Shop Dashboard</div>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav style="flex:1; padding:12px 10px; overflow-y:auto;">
        <div
            style="font-size:10px; font-weight:700; color:#667382; text-transform:uppercase; letter-spacing:.08em; padding:6px 14px; margin-bottom:4px;">
            Menu</div>

        <a href="/shop/dashboard" class="t-nav-item {{ shopNavActive('shop/dashboard') }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="/shop/requests" class="t-nav-item {{ shopNavActive('shop/requests') }}">
            <i class="fas fa-clipboard-list"></i> Dispatch Requests
        </a>
        <a href="/shop/messages" class="t-nav-item {{ shopNavActive('shop/messages') }}">
            <i class="fas fa-comments"></i> Messages
        </a>
        <a href="/shop/reviews" class="t-nav-item {{ shopNavActive('shop/reviews') }}">
            <i class="fas fa-star"></i> Reviews
        </a>
        <a href="/shop/mechanics" class="t-nav-item {{ shopNavActive('shop/mechanics') }}">
            <i class="fas fa-tools"></i> Mechanics
        </a>
        <a href="/shop/settings" class="t-nav-item {{ shopNavActive('shop/settings') }}">
            <i class="fas fa-cog"></i> Settings
        </a>
    </nav>

    {{-- Footer: Sign out only --}}
    <div style="padding:12px 16px; border-top:1px solid rgba(255,255,255,.08);">
        @auth
            <a href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                style="display:inline-flex; align-items:center; gap:6px; font-size:12px; color:#667382; text-decoration:none;"
                onmouseover="this.style.color='#d63939'" onmouseout="this.style.color='#667382'">
                <i class="fas fa-sign-out-alt"></i> Sign out
            </a>
            <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">@csrf</form>
        @endauth
    </div>

</aside>

<script>
    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    /* STATUS_MAP is injected by layouts/shop.blade.php from the
       shop_statuses DB table (cached 24 h), keyed by integer ID.
       Each entry: { id, slug, label, color, bg, next_label, next_color }
       Edit that table to change labels or colours — no deploy needed. */
    const STATUS_MAP = window.STATUS_MAP || {};
    const STATUS_FALLBACK = Object.values(STATUS_MAP).find(c => c.slug === 'closed') ||
        {
            id: 4,
            slug: 'closed',
            label: 'Closed',
            color: '#d63939',
            bg: '#fde8e8',
            next_label: 'Open Shop',
            next_color: '#2fb344'
        };

    function getStatusConfig(statusKey) {
        // statusKey can be an integer ID (as string or number) or a slug string
        return STATUS_MAP[String(statusKey)] ||
            Object.values(STATUS_MAP).find(c => c.slug === statusKey) ||
            STATUS_FALLBACK;
    }

    async function toggleShopStatus() {
        const pill = document.getElementById('topbar-status-pill');
        const currentStatus = pill ? (pill.dataset.status || '4') : '4';
        const cfg = getStatusConfig(currentStatus);
        showConfirmModal(
            'Toggle Shop Status',
            `Set shop to: ${cfg.next_label}?`,
            async function() {
                    const toggleBtn = document.querySelector('[onclick="toggleShopStatus()"]');
                    if (toggleBtn) {
                        toggleBtn.disabled = true;
                        toggleBtn.style.opacity = '.6';
                    }
                    try {
                        const res = await fetch('/shop/settings/toggle-status', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': getCsrfToken(),
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        });
                        const data = await res.json();
                        if (res.ok && data.success) {
                            updateStatusUI(data.status_id);
                            localStorage.setItem('shopStatus', data.status);
                            localStorage.setItem('shopStatusId', data.status_id);
                            localStorage.setItem('shopStatusUpdated', Date.now());
                            if (typeof showToast === 'function') showToast('Shop status set to ' + data.status +
                                '.', 'success');
                        } else {
                            const msg = data.message || ('Error ' + res.status);
                            if (typeof showToast === 'function') showToast('Toggle failed: ' + msg, 'error');
                            else alert('Toggle failed: ' + msg);
                        }
                    } catch (e) {
                        console.error('Toggle failed:', e);
                        if (typeof showToast === 'function') showToast('Network error — toggle failed.',
                            'error');
                        else alert('Network error — toggle failed.');
                    } finally {
                        if (toggleBtn) {
                            toggleBtn.disabled = false;
                            toggleBtn.style.opacity = '1';
                        }
                    }
                },
                cfg.next_label,
                cfg.next_color
        );
    }

    function updateStatusUI(statusKey) {
        const cfg = getStatusConfig(statusKey);
        const tbPill = document.getElementById('topbar-status-pill');
        const tbDot = document.getElementById('topbar-status-dot');
        const tbText = document.getElementById('topbar-status-text');
        if (tbPill) {
            tbPill.style.background = cfg.bg;
            tbPill.dataset.status = String(cfg.id);
        }
        if (tbDot) tbDot.style.background = cfg.color;
        if (tbText) {
            tbText.textContent = cfg.label;
            tbText.style.color = cfg.color;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const pill = document.getElementById('topbar-status-pill');
        if (pill) updateStatusUI(pill.dataset.status || '4');
    });

    window.addEventListener('storage', (e) => {
        if (e.key === 'shopStatusUpdated') {
            const id = localStorage.getItem('shopStatusId');
            if (id) updateStatusUI(id);
        }
    });
</script>
