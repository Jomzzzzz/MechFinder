<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MechFinder | Fast Motorcycle Repair Dispatch</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'DM Sans', sans-serif;
            background: #0A0E14; /* midnight background */
            color: #E6EDF3; /* light text */
        }

        h1,
        h2,
        h3 {
            font-family: 'Syne', sans-serif;
        }

        .mf-accent { color: #FF8A00; }
        .mf-charcoal { color: #333333; }
        .bg-mf-accent { background: linear-gradient(90deg,#262626 0%, #FF8A00 100%); }

        .btn {
            transition: transform .15s, background .2s;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .card {
            transition: transform .25s, box-shadow .25s;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 48px rgba(0, 0, 0, .07);
        }

        .hero-glow {
            background: radial-gradient(ellipse at 60% 50%, rgba(247, 148, 29, .12) 0%, transparent 70%);
        }

        /* Nav responsive — guaranteed regardless of CDN */
        .nav-desktop {
            display: none;
        }

        .nav-mobile {
            display: flex;
        }

        @media (min-width: 768px) {
            .nav-desktop {
                display: flex;
            }

            .nav-mobile {
                display: none;
            }
        }
    </style>
</head>

<body class="overflow-x-hidden antialiased">

    <!-- NAV -->
    <nav class="sticky top-0 z-50 border-b border-transparent bg-[#071017]/80 backdrop-blur-xl">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">

            <!-- Logo -->
            <a href="/" class="flex items-center gap-2.5 flex-shrink-0">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-[#262626] to-[#FF8A00] flex items-center justify-center text-base">⚙️</div>
                <span class="font-bold text-base tracking-tight" style="font-family:Syne,sans-serif"><span class="mf-charcoal">Mech</span><span class="mf-accent">Finder</span></span>
            </a>

            <!-- Desktop Nav Links -->
            <div class="nav-desktop items-center gap-0.5 flex-1 justify-center">
                <a href="#features"
                    class="px-3.5 py-2 text-sm font-medium text-slate-400 hover:text-white rounded-lg transition-all">Features</a>
                <a href="#how-it-works"
                    class="px-3.5 py-2 text-sm font-medium text-slate-400 hover:text-white rounded-lg transition-all">How It Works</a>
                <a href="{{ route('signup') }}"
                    class="px-3.5 py-2 text-sm font-medium text-slate-400 hover:text-white rounded-lg transition-all">For Motorists</a>
                <a href="{{ route('signup.shop') }}"
                    class="px-3.5 py-2 text-sm font-medium text-slate-400 hover:text-white rounded-lg transition-all">For Shops</a>
            </div>

            <!-- Desktop Actions -->
            <div class="nav-desktop items-center gap-2 flex-shrink-0">
                <a href="{{ route('login') }}"
                    class="px-4 py-2 text-sm font-medium text-slate-200 bg-gradient-to-r from-[#111827] via-[#0C1119] to-[#071017] border border-white/10 rounded-lg hover:brightness-110 transition-all">Sign in</a>
            </div>

            <!-- Mobile: Sign in + Hamburger -->
            <div class="nav-mobile items-center gap-2">
                <a href="{{ route('login') }}"
                    class="px-3 py-1.5 text-sm font-medium text-slate-200 bg-gradient-to-r from-[#111827] via-[#0C1119] to-[#071017] border border-white/10 rounded-lg hover:brightness-110 transition-all">Sign
                    in</a>
                <button id="mobile-menu-btn"
                    class="w-9 h-9 flex items-center justify-center rounded-lg hover:bg-[#F5F4F1] transition-all"
                    aria-label="Toggle menu">
                    <svg id="hamburger-icon" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg id="close-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Panel -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-[#E8E6E1] bg-[#FAFAF8]">
            <div class="max-w-6xl mx-auto px-4 py-3 flex flex-col gap-0.5">
                <a href="#features"
                    class="px-3 py-2.5 text-sm font-medium text-[#6B6963] hover:text-[#0D0D0D] hover:bg-[#F5F4F1] rounded-lg transition-all">Features</a>
                <a href="#how-it-works"
                    class="px-3 py-2.5 text-sm font-medium text-[#6B6963] hover:text-[#0D0D0D] hover:bg-[#F5F4F1] rounded-lg transition-all">How
                    It Works</a>
                <a href="{{ route('signup') }}"
                    class="px-3 py-2.5 text-sm font-medium text-[#6B6963] hover:text-[#0D0D0D] hover:bg-[#F5F4F1] rounded-lg transition-all">For
                    Motorists</a>
                <a href="{{ route('signup.shop') }}"
                    class="px-3 py-2.5 text-sm font-medium text-[#6B6963] hover:text-[#0D0D0D] hover:bg-[#F5F4F1] rounded-lg transition-all">For
                    Shops</a>
                <div class="pt-2.5 mt-1.5 border-t border-[#E8E6E1]">
                    <a href="{{ route('signup') }}"
                        class="btn flex items-center justify-center gap-1 bg-[#0D0D0D] text-white text-sm font-medium px-4 py-2.5 rounded-lg hover:bg-[#2a2a2a] transition-all w-full">Get
                        started <span class="text-[#F7941D]">&rarr;</span></a>
                </div>
            </div>
        </div>
    </nav>

    <script>
        (function() {
            const btn = document.getElementById('mobile-menu-btn');
            const menu = document.getElementById('mobile-menu');
            const hamburger = document.getElementById('hamburger-icon');
            const closeIcon = document.getElementById('close-icon');

            function openMenu() {
                menu.classList.remove('hidden');
                hamburger.style.display = 'none';
                closeIcon.style.display = '';
            }

            function closeMenu() {
                menu.classList.add('hidden');
                hamburger.style.display = '';
                closeIcon.style.display = 'none';
            }
            btn.addEventListener('click', function() {
                menu.classList.contains('hidden') ? openMenu() : closeMenu();
            });
            menu.querySelectorAll('a').forEach(function(a) {
                a.addEventListener('click', closeMenu);
            });
        })();
    </script>

    <!-- HERO -->
    <section
        class="max-w-4xl mx-auto px-4 sm:px-6 py-8 sm:py-12 md:py-16 lg:py-20 flex flex-col justify-center items-center text-center min-h-[calc(100vh-60px)] sm:min-h-[calc(100vh-68px)]">

        <!-- HERO CONTENT -->
        <div class="flex flex-col justify-center items-center">
            <!-- Badge -->
            <div
                class="inline-flex items-center gap-2 rounded-full px-3 sm:px-3.5 py-1 sm:py-1.5 text-[10px] sm:text-xs font-medium text-slate-200 uppercase tracking-widest mb-4 sm:mb-6 w-fit bg-gradient-to-r from-[#111827] via-[#0C1119] to-[#071017] border border-white/10 shadow-[0_10px_30px_-24px_rgba(255,138,0,0.9)] backdrop-blur-sm">
                <span class="w-1.5 h-1.5 rounded-full bg-[#FF8A00]"></span>
                <span class="hidden sm:inline">Motorcycle Repair Dispatch</span>
                <span class="sm:hidden">Repair Dispatch</span>
            </div>

            <!-- Headline -->
            <h1
                class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold leading-[1.1] sm:leading-[1.08] lg:leading-[1.05] tracking-tight mb-4 sm:mb-6">
                Fast repair help,<br><span class="mf-accent">anytime</span> you need it.
            </h1>

            <!-- Description -->
            <p class="text-base sm:text-lg text-slate-300 font-light leading-relaxed max-w-md mb-6 sm:mb-10">
                Connect instantly with nearby motorcycle repair shops. Emergency dispatch, live chat, and real-time
                tracking — all in one place.
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row flex-wrap gap-2.5 sm:gap-3 mb-8 sm:mb-10">
                <a href="{{ route('signup') }}"
                    class="btn bg-[#FF8A00] text-[#071017] font-medium px-5 sm:px-7 py-3 sm:py-3.5 rounded-full hover:brightness-95 text-center text-sm sm:text-base">Motorist</a>
                <a href="{{ route('signup.shop') }}"
                    class="btn bg-gradient-to-r from-[#111827] via-[#0C1119] to-[#071017] border border-white/10 text-slate-200 font-medium px-5 sm:px-7 py-3 sm:py-3.5 rounded-full hover:brightness-110 transition text-center text-sm sm:text-base">Shop</a>
            </div>

        </div>
    </section>

    <!-- PROOF STRIP -->
    <div class="bg-[#071017] border-y border-transparent py-4 sm:py-5 px-4 sm:px-6">
        <div class="max-w-6xl mx-auto flex flex-wrap justify-center gap-x-6 sm:gap-x-10 gap-y-2.5 sm:gap-y-3">
            @foreach (['Live Mechanic Tracking', 'Verified Repair Shops', 'Olongapo City Coverage'] as $item)
                <span class="flex items-center gap-2 text-xs sm:text-sm text-slate-300 font-medium">
                    <svg class="text-[#FF8A00] w-3.5 h-3.5 sm:w-[15px] sm:h-[15px]" fill="none"
                        stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4" />
                        <circle cx="12" cy="12" r="10" />
                    </svg>
                    {{ $item }}
                </span>
            @endforeach
        </div>
    </div>

    <!-- FEATURES -->
    <section id="features" class="max-w-6xl mx-auto px-4 sm:px-6 py-12 sm:py-16 md:py-24">
        <span id="how-it-works" class="block invisible h-0"></span>
        <div class="mb-10 sm:mb-14">
            <p
                class="flex items-center gap-2.5 text-xs font-semibold text-[#F7941D] uppercase tracking-widest mb-3 sm:mb-4">
                <span class="w-4 sm:w-6 h-0.5 bg-[#F7941D]"></span>How it works
            </p>
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight leading-tight mb-3 sm:mb-4">Built
                for motorists<br>and shops alike</h2>
            <p class="text-sm sm:text-base text-[#6B6963] font-light leading-relaxed max-w-md">One unified platform to
                make motorcycle repair faster, simpler, and more reliable for everyone.</p>
        </div>

        <div class="grid md:grid-cols-2 gap-4 sm:gap-6 mb-6">
            @foreach ([['icon' => '🏍', 'title' => 'For Motorists', 'sub' => 'Get back on the road fast', 'items' => [['📍', 'Find Nearby Shops', 'Discover verified repair shops closest to your exact location in seconds.'], ['🚨', 'Emergency Dispatch', 'Tap once to request urgent roadside assistance wherever you are.'], ['💬', 'Live Chat', 'Describe your problem and get real-time guidance before arrival.'], ['⭐', 'Ratings & Reviews', 'Rate services, read feedback, and make informed choices every time.']]], ['icon' => '🛠', 'title' => 'For Shop Owners', 'sub' => 'Grow your customer base', 'items' => [['📩', 'Receive Requests', 'Get notified instantly for jobs near your shop — never miss a customer.'], ['🟢', 'Real-time Availability', 'Toggle open/closed status and manage your capacity on the fly.'], ['📊', 'Analytics Dashboard', 'Monitor jobs completed, average ratings, and revenue over time.'], ['👨‍🔧', 'Mechanic Management', 'Assign and dispatch your team to customer locations with one tap.']]]] as $card)
                <div class="card bg-gradient-to-br from-[#111827] to-[#0C1119] border border-white/10 rounded-xl sm:rounded-2xl p-5 sm:p-8 shadow-lg hover:border-white/20 transition">
                    <div class="flex items-start gap-3 sm:gap-4 pb-4 sm:pb-6 mb-4 sm:mb-6 border-b border-white/10">
                        <div
                            class="w-11 h-11 sm:w-13 sm:h-13 bg-[#FF8A00]/10 rounded-lg sm:rounded-xl flex items-center justify-center text-xl sm:text-2xl flex-shrink-0">
                            {{ $card['icon'] }}</div>
                        <div>
                            <h3 class="text-base sm:text-xl font-bold text-white">{{ $card['title'] }}</h3>
                            <p class="text-xs sm:text-sm text-slate-400">{{ $card['sub'] }}</p>
                        </div>
                    </div>
                    <ul class="space-y-3 sm:space-y-5">
                        @foreach ($card['items'] as [$icon, $title, $desc])
                            <li class="flex gap-2 sm:gap-3">
                                <div
                                    class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-[#FF8A00]/20 flex items-center justify-center text-xs sm:text-sm flex-shrink-0 mt-0.5">
                                    {{ $icon }}</div>
                                <div>
                                    <h4 class="text-xs sm:text-sm font-semibold text-slate-200 mb-0.5">{{ $title }}</h4>
                                    <p class="text-[11px] sm:text-xs text-slate-400 leading-relaxed">
                                        {{ $desc }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>

    </section>

    <!-- CTA -->
    <div class="bg-[#0D0D0D] py-12 sm:py-16 md:py-24 px-4 sm:px-6">
        <div class="max-w-2xl mx-auto text-center">
            <h2
                class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-white tracking-tight leading-tight mb-4 sm:mb-5">
                Ready to get<br><span class="orange">back on the road?</span>
            </h2>
            <p class="text-sm sm:text-base text-white/50 font-light leading-relaxed mb-8 sm:mb-10">Join MechFinder
                today and experience seamless motorcycle repair dispatch built for Olongapo City.</p>
            <div class="flex flex-col sm:flex-row flex-wrap justify-center gap-2.5 sm:gap-3">
                <a href="{{ route('signup') }}"
                    class="btn bg-white text-[#0D0D0D] font-medium px-6 sm:px-7 py-3 sm:py-3.5 rounded-full hover:bg-[#F5F4F1] transition text-sm sm:text-base">Motorist</a>
                <a href="{{ route('signup.shop') }}"
                    class="btn bg-gradient-to-r from-[#111827] via-[#0C1119] to-[#071017] border border-white/10 text-slate-200 font-medium px-6 sm:px-7 py-3 sm:py-3.5 rounded-full hover:brightness-110 transition text-sm sm:text-base">Shop</a>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="bg-[#071017] border-t border-transparent py-4 sm:py-6 px-4 sm:px-6">
        <div class="max-w-6xl mx-auto flex flex-col sm:flex-row flex-wrap items-center justify-between gap-3 sm:gap-4">
            <a href="/" class="flex items-center gap-2.5">
                <div
                    class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-gradient-to-br from-[#262626] to-[#FF8A00] flex items-center justify-center text-xs sm:text-sm">
                    ⚙️</div>
                <span class="font-bold tracking-tight text-sm sm:text-base text-slate-200"
                    style="font-family:Syne,sans-serif"><span class="mf-charcoal">Mech</span><span class="mf-accent">Finder</span></span>
            </a>
            <p class="text-xs sm:text-sm text-slate-400 text-center sm:text-left">© 2026 MechFinder. Fast motorcycle
                repair dispatch for Olongapo City.</p>
        </div>
    </footer>

</body>

</html>
