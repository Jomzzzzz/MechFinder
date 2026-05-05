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
            background: #0F0F0F;
        }
    </style>
</head>

<body class="text-white">

    <div class="min-h-screen flex flex-col">

        <!-- NAVBAR -->
        <nav class="flex items-center justify-between px-6 md:px-16 py-5 border-b border-white/10">
            <a href="/" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-orange-500 flex items-center justify-center text-black text-xl font-black">
                    ⚙
                </div>

                <div>
                    <h1 class="text-xl md:text-2xl font-black tracking-wide">
                        MECHFINDER
                    </h1>
                    <p class="text-xs text-gray-400">
                        Motor Shop Portal
                    </p>
                </div>
            </a>

            <a href="/login"
               class="px-5 py-2.5 rounded-xl bg-white text-black font-bold text-sm hover:bg-orange-500 transition">
                Shop Login
            </a>
        </nav>

        <!-- HERO -->
        <main class="flex-1 flex items-center justify-center px-6 py-16">
            <div class="max-w-5xl w-full grid md:grid-cols-2 gap-12 items-center">

                <!-- LEFT -->
                <div>
                    <span class="inline-block px-4 py-2 rounded-full bg-orange-500/10 text-orange-400 text-sm font-bold mb-6">
                        Motorcycle Repair Dispatch System
                    </span>

                    <h2 class="text-4xl md:text-6xl font-black leading-tight mb-6">
                        Manage repair requests faster and easier.
                    </h2>

                    <p class="text-gray-400 text-lg leading-relaxed mb-8">
                        MechFinder helps motor shops receive dispatch requests, manage availability,
                        and connect with motorists during motorcycle breakdowns.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="/login"
                           class="px-8 py-4 rounded-xl bg-orange-500 text-black font-black text-center hover:bg-orange-600 transition">
                            Shop Owner Login
                        </a>

                        <a href="/motorist"
                           class="px-8 py-4 rounded-xl border border-white/20 text-white font-bold text-center hover:border-orange-500 hover:text-orange-400 transition">
                            Open Motorist App
                        </a>
                    </div>
                </div>

                <!-- RIGHT CARD -->
                <div class="bg-[#1A1A1A] border border-white/10 rounded-3xl p-8 shadow-xl">
                    <h3 class="text-2xl font-black mb-6">
                        Portal Features
                    </h3>

                    <div class="space-y-5">
                        <div class="flex gap-4">
                            <div class="w-11 h-11 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-400">
                                📩
                            </div>
                            <div>
                                <h4 class="font-bold">Dispatch Requests</h4>
                                <p class="text-sm text-gray-400">View incoming motorist requests.</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-11 h-11 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-400">
                                🟢
                            </div>
                            <div>
                                <h4 class="font-bold">Shop Status</h4>
                                <p class="text-sm text-gray-400">Set your shop as open, busy, or closed.</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-11 h-11 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-400">
                                📍
                            </div>
                            <div>
                                <h4 class="font-bold">Location Based</h4>
                                <p class="text-sm text-gray-400">Help nearby motorists find your shop.</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-11 h-11 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-400">
                                ⭐
                            </div>
                            <div>
                                <h4 class="font-bold">Ratings</h4>
                                <p class="text-sm text-gray-400">Build trust through completed jobs and reviews.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>

        <!-- FOOTER -->
        <footer class="px-6 py-6 border-t border-white/10 text-center text-sm text-gray-500">
            © 2026 MechFinder. Built for motor shops and motorists in Olongapo City.
        </footer>

    </div>

</body>
</html>