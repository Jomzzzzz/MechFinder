<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MechFinder | Motor Shop Portal</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: Inter, Arial, sans-serif;
            background:
                linear-gradient(rgba(15,15,15,.72), rgba(15,15,15,.72)),
                url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1600&q=80');
            background-size: cover;
            background-position: center;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.35);
        }

        .hero-image {
            background:
                linear-gradient(to top, rgba(0,0,0,.72), rgba(0,0,0,.08)),
                url('https://images.unsplash.com/photo-1558981806-ec527fa84c39?auto=format&fit=crop&w=1200&q=80');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>

<body class="text-white">

    <div class="flex justify-center items-center min-h-screen px-4 py-10">

        <div class="grid md:grid-cols-2 w-full max-w-6xl min-h-[680px] overflow-hidden rounded-none glass-panel">

            <!-- LEFT IMAGE PANEL -->
            <div class="relative hidden md:flex flex-col justify-between hero-image p-10">

                <!-- BRAND -->
                <a href="/" class="flex items-center gap-4">
                    <div class="flex justify-center items-center bg-white rounded-full w-12 h-12 text-black text-xl">
                        ⚙
                    </div>

                    <div>
                        <h1 class="font-bold text-2xl tracking-tight">MechFinder</h1>
                        <p class="text-white/75 text-xs">Motor Shop Portal</p>
                    </div>
                </a>

                <!-- HERO TEXT -->
                <div>
                    <h2 class="mb-4 font-black text-5xl leading-tight">
                        Manage Repair Requests
                    </h2>

                    <p class="max-w-md text-white/90 text-lg leading-relaxed">
                        Receive dispatch requests, update your shop availability, and help nearby motorists during breakdowns.
                    </p>

                    <div class="flex items-center gap-2 mt-10">
                        <div class="bg-white rounded-full w-12 h-1"></div>
                        <div class="bg-white/80 rounded-full w-2 h-2"></div>
                        <div class="bg-white/80 rounded-full w-2 h-2"></div>
                    </div>
                </div>
            </div>

            <!-- RIGHT CONTENT PANEL -->
            <div class="relative flex flex-col justify-center bg-white px-8 md:px-20 py-12 text-[#101633]">

            

                <div class="w-full max-w-md mx-auto">
                    <span class="inline-block mb-5 font-semibold text-orange-500 text-sm">
                        Motorcycle Repair Dispatch System
                    </span>

                    <h2 class="mb-4 font-black text-4xl leading-tight">
                        Welcome to MechFinder!
                    </h2>

                    <p class="mb-10 text-gray-500 text-sm leading-relaxed">
                        A clean and simple portal for motor shops to manage repair requests,
                        availability, ratings, and location-based dispatch services.
                    </p>

                    <!-- BUTTONS -->
                    <div class="space-y-4 mb-12">
                        <a href="/login"
                            class="block bg-[#202020] hover:bg-orange-500 px-8 py-4 rounded-lg font-black text-white text-center transition">
                            Log In
                        </a>

                        <a href="{{ route('signup') }}"
   class="block bg-white hover:bg-orange-100 px-8 py-4 border border-orange-200 rounded-lg font-semibold text-gray-700 hover:text-orange-500 text-center transition duration-300">
    Register as Motorist
</a>

                        <a href="{{ route('signup.shop') }}"
                            class="block bg-white hover:bg-orange-100 px-8 py-4 border border-orange-200 rounded-lg font-semibold text-orange-500 text-center transition">
                            Register Your Shop
                        </a>
                    </div>

                    <!-- FEATURES -->
<div class="pt-8 border-gray-200 border-t">

    <div class="gap-6 grid grid-cols-1">

        <!-- MOTORIST FEATURES -->
        <div>
            <div class="flex items-center gap-2 mb-4">
                <div class="bg-orange-500 rounded-full w-2 h-2"></div>

                <p class="font-bold text-[#101633] text-sm uppercase tracking-wide">
                    Motorist Portal
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4">

                <div class="p-4 border border-gray-100 rounded-xl hover:border-orange-200 transition">
                    <div class="mb-2 text-xl">📍</div>

                    <h4 class="font-bold text-sm">
                        Nearby Shops
                    </h4>

                    <p class="text-gray-400 text-xs">
                        Find nearby motorcycle repair shops instantly.
                    </p>
                </div>

                <div class="p-4 border border-gray-100 rounded-xl hover:border-orange-200 transition">
                    <div class="mb-2 text-xl">🚨</div>

                    <h4 class="font-bold text-sm">
                        Emergency Dispatch
                    </h4>

                    <p class="text-gray-400 text-xs">
                        Request roadside mechanic assistance in real-time.
                    </p>
                </div>

                <div class="p-4 border border-gray-100 rounded-xl hover:border-orange-200 transition">
                    <div class="mb-2 text-xl">💬</div>

                    <h4 class="font-bold text-sm">
                        Live Chat
                    </h4>

                    <p class="text-gray-400 text-xs">
                        Communicate directly with motor shops and mechanics.
                    </p>
                </div>

                <div class="p-4 border border-gray-100 rounded-xl hover:border-orange-200 transition">
                    <div class="mb-2 text-xl">⭐</div>

                    <h4 class="font-bold text-sm">
                        Reviews & Ratings
                    </h4>

                    <p class="text-gray-400 text-xs">
                        Rate completed services and trusted shops.
                    </p>
                </div>

            </div>
        </div>

        <!-- SHOP OWNER FEATURES -->
        <div class="pt-2">

            <div class="flex items-center gap-2 mb-4">
                <div class="bg-black rounded-full w-2 h-2"></div>

                <p class="font-bold text-[#101633] text-sm uppercase tracking-wide">
                    Shop Owner Portal
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4">

                <div class="p-4 border border-gray-100 rounded-xl hover:border-orange-200 transition">
                    <div class="mb-2 text-xl">📩</div>

                    <h4 class="font-bold text-sm">
                        Dispatch Requests
                    </h4>

                    <p class="text-gray-400 text-xs">
                        Receive and manage nearby repair requests.
                    </p>
                </div>

                <div class="p-4 border border-gray-100 rounded-xl hover:border-orange-200 transition">
                    <div class="mb-2 text-xl">🟢</div>

                    <h4 class="font-bold text-sm">
                        Shop Status
                    </h4>

                    <p class="text-gray-400 text-xs">
                        Set availability as open, busy, or closed.
                    </p>
                </div>

                <div class="p-4 border border-gray-100 rounded-xl hover:border-orange-200 transition">
                    <div class="mb-2 text-xl">📊</div>

                    <h4 class="font-bold text-sm">
                        Dashboard Analytics
                    </h4>

                    <p class="text-gray-400 text-xs">
                        Track jobs, ratings, and daily performance.
                    </p>
                </div>

                <div class="p-4 border border-gray-100 rounded-xl hover:border-orange-200 transition">
                    <div class="mb-2 text-xl">🛠</div>

                    <h4 class="font-bold text-sm">
                        Mechanic Dispatch
                    </h4>

                    <p class="text-gray-400 text-xs">
                        Send mechanics directly to motorists in need.
                    </p>
                </div>

            </div>

        </div>

    </div>

</div>

                <!-- FOOTER -->
                <footer class="right-0 bottom-8 left-0 absolute text-gray-400 text-xs text-center">
                    © 2026 MechFinder. Built for motor shops and motorists in Olongapo City.
                </footer>

            </div>

        </div>

    </div>

</body>

</html>