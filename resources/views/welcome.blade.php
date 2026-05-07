<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MechFinder | Motorcycle Repair Dispatch</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: Inter, Arial, sans-serif;
            background: #0f0f0f;
        }

        .hero-bg {
            background:
                linear-gradient(90deg, rgba(15,15,15,.96), rgba(15,15,15,.76)),
                url('https://images.unsplash.com/photo-1558981806-ec527fa84c39?auto=format&fit=crop&w=1600&q=80');
            background-size: cover;
            background-position: center;
        }

        .glass-card {
            background: rgba(26,26,26,.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,.06);
        }
    </style>
</head>

<body class="min-h-screen bg-[#0f0f0f] text-white">

    <!-- NAVBAR -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-[#0f0f0f]/90 backdrop-blur border-b border-white/5">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">

            <!-- LOGO -->
            <a href="/" class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-full bg-[#F7941D] flex items-center justify-center text-white text-xl font-black shadow-lg shadow-orange-500/30">
                    ⚙
                </div>

                <div>
                    <h1 class="text-2xl font-black tracking-tight text-[#F7941D]">
                        MechFinder
                    </h1>

                    <p class="text-xs text-gray-400 -mt-1">
                        Motorcycle Repair Dispatch
                    </p>
                </div>
            </a>

            <!-- NAV LINKS -->
            <div class="hidden md:flex items-center gap-8 text-sm font-bold text-gray-300">
                <a href="#features" class="hover:text-[#F7941D] transition">
                    Features
                </a>

                <a href="{{ route('signup') }}" class="hover:text-[#F7941D] transition">
                    Motorist
                </a>

                <a href="{{ route('signup.shop') }}" class="hover:text-[#F7941D] transition">
                    Shop Owner
                </a>
            </div>

            <!-- LOGIN -->
            <a href="/login"
               class="bg-[#F7941D] hover:bg-orange-600 px-6 py-3 rounded-full font-black text-white text-sm transition shadow-lg shadow-orange-500/20">
                Log In
            </a>

        </div>
    </header>

    <!-- HERO -->
    <main class="pt-20">

        <section class="hero-bg min-h-[760px] flex items-center">

            <div class="max-w-7xl mx-auto px-6 py-20 grid lg:grid-cols-2 gap-14 items-center">

                <!-- LEFT -->
                <div>

                    <span class="inline-flex items-center gap-2 bg-white/10 border border-white/10 backdrop-blur px-5 py-2 rounded-full text-sm font-bold mb-8">
                        🏍 Emergency Motorcycle Assistance
                    </span>

                    <h2 class="text-5xl md:text-7xl font-black leading-[0.95] mb-8">
                        Fast repair help,
                        anytime anywhere.
                    </h2>

                    <p class="text-lg md:text-xl text-gray-300 leading-relaxed max-w-xl mb-10">
                        MechFinder connects motorists with nearby motorcycle repair shops
                        for emergency dispatch, roadside assistance, live chat,
                        and real-time repair tracking.
                    </p>

                    <!-- CTA -->
                    <div class="flex flex-col sm:flex-row gap-4">

                        <a href="{{ route('signup') }}"
                           class="bg-[#F7941D] hover:bg-orange-600 text-white px-8 py-4 rounded-full font-black text-center transition shadow-2xl shadow-orange-500/20">
                            Register as Motorist
                        </a>

                        <a href="{{ route('signup.shop') }}"
                           class="bg-[#1a1a1a] hover:bg-[#222] border border-white/10 text-white px-8 py-4 rounded-full font-black text-center transition">
                            Register Your Shop
                        </a>

                    </div>

                </div>

                <!-- RIGHT -->
                <div class="glass-card rounded-[2rem] p-6 md:p-8 shadow-2xl">

                    <!-- IMAGE -->
                    <div class="rounded-[1.5rem] overflow-hidden mb-6">
                        <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1200&q=80"
                             class="w-full h-72 object-cover"
                             alt="Motorcycle Repair">
                    </div>

                    <!-- MINI FEATURES -->
                    <div class="grid grid-cols-3 gap-3 mb-6">

                        <div class="bg-[#222222] border border-white/5 rounded-2xl p-4 text-center">
                            <div class="text-2xl mb-2">📍</div>

                            <p class="text-xs font-black text-gray-300">
                                Nearby Shops
                            </p>
                        </div>

                        <div class="bg-[#222222] border border-white/5 rounded-2xl p-4 text-center">
                            <div class="text-2xl mb-2">🚨</div>

                            <p class="text-xs font-black text-gray-300">
                                Dispatch
                            </p>
                        </div>

                        <div class="bg-[#222222] border border-white/5 rounded-2xl p-4 text-center">
                            <div class="text-2xl mb-2">💬</div>

                            <p class="text-xs font-black text-gray-300">
                                Live Chat
                            </p>
                        </div>

                    </div>

                    <!-- STATUS -->
                    <div class="bg-[#111111] border border-white/5 rounded-3xl p-5">

                        <p class="text-sm text-gray-400 mb-2">
                            Current system status
                        </p>

                        <div class="flex items-center justify-between">

                            <h3 class="text-xl font-black text-white">
                                Ready for Dispatch
                            </h3>

                            <span class="bg-[#F7941D] text-white text-xs font-black px-4 py-2 rounded-full">
                                ONLINE
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </section>

        <!-- FEATURES -->
        <section id="features" class="bg-[#0f0f0f] py-20">

            <div class="max-w-7xl mx-auto px-6">

                <!-- TITLE -->
                <div class="text-center max-w-2xl mx-auto mb-16">

                    <p class="text-[#F7941D] font-black mb-3 tracking-wide">
                        MECHFINDER PORTALS
                    </p>

                    <h2 class="text-4xl md:text-5xl font-black mb-5 text-white">
                        One platform for motorists and repair shops.
                    </h2>

                    <p class="text-gray-400 leading-relaxed">
                        A modern roadside assistance system designed for
                        emergency motorcycle repairs and live mechanic dispatch.
                    </p>

                </div>

                <!-- GRID -->
                <div class="grid lg:grid-cols-2 gap-8">

                    <!-- MOTORIST -->
                    <div class="bg-[#1a1a1a] border border-white/5 rounded-[2rem] p-8">

                        <div class="flex items-center justify-between mb-8">

                            <div>
                                <p class="text-[#F7941D] font-black text-sm mb-1">
                                    MOTORIST PORTAL
                                </p>

                                <h3 class="text-3xl font-black text-white">
                                    For Riders
                                </h3>
                            </div>

                            <div class="w-14 h-14 bg-[#F7941D] rounded-full flex items-center justify-center text-white text-2xl shadow-lg shadow-orange-500/20">
                                🏍
                            </div>

                        </div>

                        <div class="grid sm:grid-cols-2 gap-4">

                            <div class="bg-[#222222] border border-white/5 p-5 rounded-3xl">
                                <div class="text-3xl mb-3">📍</div>

                                <h4 class="font-black mb-1 text-white">
                                    Nearby Shops
                                </h4>

                                <p class="text-sm text-gray-400">
                                    Find nearby motorcycle repair shops instantly.
                                </p>
                            </div>

                            <div class="bg-[#222222] border border-white/5 p-5 rounded-3xl">
                                <div class="text-3xl mb-3">🚨</div>

                                <h4 class="font-black mb-1 text-white">
                                    Emergency Dispatch
                                </h4>

                                <p class="text-sm text-gray-400">
                                    Request roadside mechanic assistance in real-time.
                                </p>
                            </div>

                            <div class="bg-[#222222] border border-white/5 p-5 rounded-3xl">
                                <div class="text-3xl mb-3">💬</div>

                                <h4 class="font-black mb-1 text-white">
                                    Live Chat
                                </h4>

                                <p class="text-sm text-gray-400">
                                    Communicate directly with repair shops.
                                </p>
                            </div>

                            <div class="bg-[#222222] border border-white/5 p-5 rounded-3xl">
                                <div class="text-3xl mb-3">⭐</div>

                                <h4 class="font-black mb-1 text-white">
                                    Reviews & Ratings
                                </h4>

                                <p class="text-sm text-gray-400">
                                    Rate completed repair services and shops.
                                </p>
                            </div>

                        </div>

                    </div>

                    <!-- SHOP OWNER -->
                    <div class="bg-[#1a1a1a] border border-white/5 rounded-[2rem] p-8">

                        <div class="flex items-center justify-between mb-8">

                            <div>
                                <p class="text-[#F7941D] font-black text-sm mb-1">
                                    SHOP OWNER PORTAL
                                </p>

                                <h3 class="text-3xl font-black text-white">
                                    For Repair Shops
                                </h3>
                            </div>

                            <div class="w-14 h-14 bg-[#F7941D] rounded-full flex items-center justify-center text-white text-2xl shadow-lg shadow-orange-500/20">
                                🛠
                            </div>

                        </div>

                        <div class="grid sm:grid-cols-2 gap-4">

                            <div class="bg-[#222222] border border-white/5 p-5 rounded-3xl">
                                <div class="text-3xl mb-3">📩</div>

                                <h4 class="font-black mb-1 text-white">
                                    Dispatch Requests
                                </h4>

                                <p class="text-sm text-gray-400">
                                    Receive and manage nearby repair requests.
                                </p>
                            </div>

                            <div class="bg-[#222222] border border-white/5 p-5 rounded-3xl">
                                <div class="text-3xl mb-3">🟢</div>

                                <h4 class="font-black mb-1 text-white">
                                    Shop Status
                                </h4>

                                <p class="text-sm text-gray-400">
                                    Set availability as open, busy, or closed.
                                </p>
                            </div>

                            <div class="bg-[#222222] border border-white/5 p-5 rounded-3xl">
                                <div class="text-3xl mb-3">📊</div>

                                <h4 class="font-black mb-1 text-white">
                                    Dashboard Analytics
                                </h4>

                                <p class="text-sm text-gray-400">
                                    Track jobs, ratings, and shop performance.
                                </p>
                            </div>

                            <div class="bg-[#222222] border border-white/5 p-5 rounded-3xl">
                                <div class="text-3xl mb-3">🛠</div>

                                <h4 class="font-black mb-1 text-white">
                                    Mechanic Dispatch
                                </h4>

                                <p class="text-sm text-gray-400">
                                    Send mechanics directly to motorists in need.
                                </p>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>

        <!-- CTA -->
        <section class="bg-[#1a1a1a] py-16 border-t border-white/10">

            <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-6">

                <div>
                    <h2 class="text-3xl md:text-4xl font-black mb-2 text-white">
                        Start using MechFinder today.
                    </h2>

                    <p class="text-gray-400">
                        Built for motorists and repair shops in Olongapo City.
                    </p>
                </div>

                <a href="/login"
                   class="bg-[#F7941D] hover:bg-orange-600 px-8 py-4 rounded-full font-black text-white transition shadow-lg shadow-orange-500/20">
                    Log In Now
                </a>

            </div>

        </section>

    </main>

    <!-- FOOTER -->
    <footer class="bg-[#0f0f0f] py-6 text-center text-sm text-gray-400 border-t border-white/5">
        © 2026 MechFinder. Built for motor shops and motorists in Olongapo City.
    </footer>

</body>
</html>