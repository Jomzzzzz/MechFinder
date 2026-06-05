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
    // ── Status config map (slug → display config) ────────────────────────────
    const SHOP_STATUS_STYLES = {
        open: {
            label: 'Open',
            color: '#2fb344',
            bg: '#d1f7d6',
            nextLabel: 'Close Shop'
        },
        busy: {
            label: 'Busy',
            color: '#f76707',
            bg: '#ffe4cc',
            nextLabel: 'Set Open'
        },
        maintenance: {
            label: 'Maintenance',
            color: '#206bc4',
            bg: '#daeeff',
            nextLabel: 'Set Open'
        },
        closed: {
            label: 'Closed',
            color: '#d63939',
            bg: '#fde8e8',
            nextLabel: 'Open Shop'
        },
    };
    const SHOP_STATUS_FALLBACK = SHOP_STATUS_STYLES.closed;

    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    // Set the topbar pill and dot to reflect the given slug ('open', 'closed', etc.)
    function setStatusUI(slug) {
        const cfg = SHOP_STATUS_STYLES[slug] || SHOP_STATUS_FALLBACK;
        const pill = document.getElementById('topbar-status-pill');
        const dot = document.getElementById('topbar-status-dot');
        const text = document.getElementById('topbar-status-text');
        if (pill) {
            pill.style.background = cfg.bg;
            pill.dataset.statusSlug = slug;
        }
        if (dot) {
            dot.style.background = cfg.color;
        }
        if (text) {
            text.textContent = cfg.label;
            text.style.color = cfg.color;
        }
        // Notify header component
        window.dispatchEvent(new CustomEvent('statusChanged', {
            detail: {
                status: slug
            }
        }));
    }

    // On page load: fetch real status from server and render it
    document.addEventListener('DOMContentLoaded', function() {
        fetch('/api/shop/status', {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(function(r) {
                return r.json();
            })
            .then(function(data) {
                if (data && data.status) setStatusUI(data.status);
            })
            .catch(function() {
                /* silently ignore if not authenticated */ });
    });

    // Toggle button: POST, update UI, controller handles the broadcast
    async function toggleShopStatus() {
        const currentSlug = document.getElementById('topbar-status-pill')?.dataset.statusSlug || 'closed';
        const cfg = SHOP_STATUS_STYLES[currentSlug] || SHOP_STATUS_FALLBACK;

        showConfirmModal(
            'Toggle Shop Status',
            'Set shop to: ' + cfg.nextLabel + '?',
            async function() {
                    const btn = document.querySelector('[onclick="toggleShopStatus()"]');
                    if (btn) {
                        btn.disabled = true;
                        btn.style.opacity = '.6';
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

                        if (res.ok && data.success && data.status) {
                            setStatusUI(data.status);
                            if (typeof showToast === 'function') showToast('Status set to ' + data.status + '.',
                                'success');
                        } else {
                            const msg = data.message || ('Error ' + res.status);
                            if (typeof showToast === 'function') showToast('Toggle failed: ' + msg, 'error');
                        }
                    } catch (e) {
                        if (typeof showToast === 'function') showToast('Network error — toggle failed.',
                            'error');
                    } finally {
                        if (btn) {
                            btn.disabled = false;
                            btn.style.opacity = '1';
                        }
                    }
                },
                cfg.nextLabel
        );
    }
</script>
