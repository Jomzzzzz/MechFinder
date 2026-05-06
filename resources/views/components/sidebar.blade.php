@php
    function active($path)
    {
        return request()->is($path)
            ? 'bg-white/5 border-l-4 border-[#F7941D] text-white'
            : 'text-gray-500 hover:text-white hover:bg-white/5';
    }
@endphp

<aside class="flex flex-col bg-[#121214] border-white/5 border-r w-64">

    <div class="p-8">
        <h1 class="text-[#F7941D] text-2xl heading-font">⚙ MECHFINDER</h1>
    </div>

    <nav class="flex-1 space-y-2 px-4">

        <a href="/shop/dashboard" class="block px-4 py-3 rounded {{ active('shop/dashboard') }}">
            Dashboard
        </a>

        <a href="/shop/requests" class="block px-4 py-3 rounded {{ active('shop/requests') }}">
            Dispatch Requests
        </a>

        <a href="/shop/messages" class="block px-4 py-3 rounded {{ active('shop/messages') }}">
            Messages
        </a>


        <a href="/shop/reviews" class="block px-4 py-3 rounded {{ active('shop/reviews') }}">
            Reviews
        </a>

        <a href="/shop/mechanics" class="block px-4 py-3 rounded {{ active('shop/mechanics') }}">
            Mechanics
        </a>

        <a href="/shop/settings" class="block px-4 py-3 rounded {{ active('shop/settings') }}">>
            Settings
        </a>

    </nav>

</aside>

<script>
    function getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        return token || document.body.getAttribute('data-csrf-token') || '';
    }

    async function toggleShopStatus() {
        const currentStatus = document.getElementById('status-text').textContent.toLowerCase().trim();
        const isOpen = currentStatus === 'open';

        if (!confirm(`Toggle shop to ${isOpen ? 'CLOSED' : 'OPEN'}?`)) return;

        try {
            const response = await fetch('/shop/settings/toggle-status', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Content-Type': 'application/json'
                }
            });

            if (response.ok) {
                const data = await response.json();
                updateStatusUI(data.status);

                // Broadcast to other tabs
                localStorage.setItem('shopStatus', data.status);
                localStorage.setItem('shopStatusUpdated', Date.now());

                // Update all components
                updateDashboardStatus(data.status);
                updateHeaderStatus(data.status);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error toggling shop status');
        }
    }

    function updateStatusUI(status) {
        const isOpen = status === 'open';
        const indicator = document.getElementById('status-toggle-btn');
        const text = document.getElementById('status-text');
        const statusDiv = document.getElementById('shop-status-indicator');

        indicator.textContent = isOpen ? '🟢' : '🔴';
        text.textContent = isOpen ? 'Open' : 'Closed';
        text.className = 'text-sm font-bold ' + (isOpen ? 'text-green-400' : 'text-red-400');

        // Update box colors
        statusDiv.style.borderColor = isOpen ? '#10b981' : '#ef4444';
        statusDiv.style.backgroundColor = isOpen ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)';

        // Update message
        const message = statusDiv.querySelector('p:last-child');
        message.textContent = `Motorists can ${isOpen ? 'see' : 'not see'} your shop`;
    }

    function updateDashboardStatus(status) {
        const isOpen = status === 'open';
        const statusBox = document.getElementById('shop-status-box');
        if (!statusBox) return;

        const emoji = document.getElementById('status-emoji');
        const text = document.getElementById('status-text-dash');

        statusBox.style.borderColor = isOpen ? '#10b981' : '#ef4444';
        statusBox.style.backgroundColor = isOpen ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)';

        emoji.textContent = isOpen ? '🟢' : '🔴';
        text.textContent = isOpen ? 'Open' : 'Closed';
        text.className = 'text-lg font-bold ' + (isOpen ? 'text-green-400' : 'text-red-400');
    }

    function updateHeaderStatus(status) {
        const isOpen = status === 'open';
        const indicator = document.getElementById('header-status-indicator');
        if (!indicator) return;

        const dot = document.getElementById('header-status-dot');
        const text = document.getElementById('header-status-text');
        const color = isOpen ? '#10b981' : '#ef4444';

        indicator.style.borderColor = color;
        dot.style.backgroundColor = color;
        text.style.color = color;
        text.textContent = isOpen ? 'OPEN' : 'CLOSED';
    }

    // Listen for status changes from other tabs/windows
    window.addEventListener('storage', (e) => {
        if (e.key === 'shopStatusUpdated') {
            const newStatus = localStorage.getItem('shopStatus');
            if (newStatus) {
                updateStatusUI(newStatus);
                updateDashboardStatus(newStatus);
            }
        }
    });
</script>
