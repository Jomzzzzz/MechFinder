@php
    if (!function_exists('shopNavActive')) {
        function shopNavActive($path)
        {
            return request()->is($path) || request()->is($path . '/*') ? 'active' : '';
        }
    }

    $sidebarStatus = 'closed';
    if (Auth::check()) {
        $user = Auth::user();
        $shopId = !empty($user->shop_id)
            ? $user->shop_id
            : \Illuminate\Support\Facades\DB::table('shops')->where('owner_id', $user->id)->value('id');
        if ($shopId) {
            $sidebarStatus = strtolower(
                \Illuminate\Support\Facades\DB::table('shops')->where('id', $shopId)->value('status') ?? 'closed',
            );
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

    async function toggleShopStatus() {
        const textEl = document.getElementById('topbar-status-text');
        const currentStatus = textEl ? textEl.textContent.trim().toLowerCase() : 'unknown';
        const isOpen = currentStatus === 'open';
        const nextLabel = isOpen ? 'CLOSED' : 'OPEN';
        showConfirmModal(
            `Toggle Shop Status`,
            `Are you sure you want to set the shop to ${nextLabel}?`,
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
                            updateStatusUI(data.status);
                            localStorage.setItem('shopStatus', data.status);
                            localStorage.setItem('shopStatusUpdated', Date.now());
                            if (typeof showToast === 'function') {
                                showToast('Shop status set to ' + data.status + '.', 'success');
                            }
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
                isOpen ? 'Set Closed' : 'Set Open',
                isOpen ? '#d63939' : '#2fb344'
        );
    }

    function updateStatusUI(status) {
        const isOpen = status === 'open';
        const isBusy = status === 'busy';
        const color = isOpen ? '#2fb344' : (isBusy ? '#f76707' : '#d63939');
        const bg = isOpen ? '#d1f7d6' : (isBusy ? '#ffe4cc' : '#fde8e8');
        const label = isOpen ? 'Open' : (isBusy ? 'Busy' : 'Closed');
        const tbDot = document.getElementById('topbar-status-dot');
        const tbText = document.getElementById('topbar-status-text');
        const tbPill = tbDot ? tbDot.parentElement : null;
        if (tbDot) tbDot.style.background = color;
        if (tbText) {
            tbText.textContent = label;
            tbText.style.color = color;
        }
        if (tbPill) tbPill.style.background = bg;
    }

    window.addEventListener('storage', (e) => {
        if (e.key === 'shopStatusUpdated') {
            const s = localStorage.getItem('shopStatus');
            if (s) updateStatusUI(s);
        }
    });
</script>
