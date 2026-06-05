<div class="flex justify-between items-center mb-10">

    <h2 class="heading-font text-2xl">Dashboard Overview</h2>

    <div class="flex items-center gap-4">
        <div id="header-status-indicator"
            class="flex items-center gap-2 bg-[#121214] px-4 py-2 border border-white/5 rounded transition-all"
            style="border-color: {{ isset($shopStatus) && $shopStatus === 'open' ? '#10b981' : '#ef4444' }}">
            <span id="header-status-dot" class="w-2 h-2 rounded-full"
                style="background-color: {{ isset($shopStatus) && $shopStatus === 'open' ? '#10b981' : '#ef4444' }}"></span>
            <span id="header-status-text" class="text-xs font-bold"
                style="color: {{ isset($shopStatus) && $shopStatus === 'open' ? '#10b981' : '#ef4444' }}">
                {{ isset($shopStatus) ? strtoupper($shopStatus) : 'LOADING' }}
            </span>
        </div>

        <!-- USER PROFILE -->
        <div class="relative group">
            <button class="flex items-center gap-2 bg-white/5 hover:bg-white/10 px-4 py-2 rounded-lg transition">
                <div
                    class="w-8 h-8 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
                <span class="text-sm text-gray-300 hidden md:block">{{ Auth::user()->name ?? 'User' }}</span>
            </button>

            <!-- DROPDOWN MENU -->
            <div
                class="absolute right-0 mt-2 w-48 bg-[#0f0f0f] border border-white/10 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                <div class="px-4 py-3 border-b border-white/5">
                    <p class="text-sm text-gray-400">Signed in as</p>
                    <p class="text-white font-semibold">{{ Auth::user()->email ?? 'Shop Admin' }}</p>
                </div>
                <a href="/shop/settings"
                    class="block px-4 py-2 text-gray-300 hover:text-white hover:bg-white/5 transition">
                    Settings
                </a>
                <form method="POST" action="{{ route('logout') }}" class="border-t border-white/5">
                    @csrf
                    <button type="submit"
                        class="w-full text-left px-4 py-2 text-red-400 hover:text-red-300 hover:bg-red-500/10 transition">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
    // Listen for status change events (dispatched by other UI components)
    window.addEventListener('statusChanged', (e) => {
        updateHeaderStatus(e.detail.status);
    });

    function updateHeaderStatus(status) {
        const COLORS = {
            open: '#10b981',
            busy: '#f76707',
            maintenance: '#206bc4',
            closed: '#ef4444'
        };
        const indicator = document.getElementById('header-status-indicator');
        const dot = document.getElementById('header-status-dot');
        const text = document.getElementById('header-status-text');

        if (!indicator) return;

        const color = COLORS[status] || COLORS.closed;

        indicator.style.borderColor = color;
        dot.style.backgroundColor = color;
        text.style.color = color;
        text.textContent = status.toUpperCase();
    }
</script>
