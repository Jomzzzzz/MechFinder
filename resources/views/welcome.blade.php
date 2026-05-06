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

    <div class="flex flex-col min-h-screen">

        <!-- NAVBAR -->
        <nav class="flex justify-between items-center px-6 md:px-16 py-5 border-white/10 border-b">
            <a href="/" class="flex items-center gap-3">
                <div
                    class="flex justify-center items-center bg-orange-500 rounded-xl w-10 h-10 font-black text-black text-xl">
                    ⚙
                </div>

                <div>
                    <h1 class="font-black text-xl md:text-2xl tracking-wide">
                        MECHFINDER
                    </h1>
                    <p class="text-gray-400 text-xs">
                        Motor Shop Portal
                    </p>
                </div>
            </a>

            <a href="/login"
                class="bg-white hover:bg-orange-500 px-5 py-2.5 rounded-xl font-bold text-black text-sm transition">
                Shop Login
            </a>
        </nav>

        <!-- HERO -->
        <main class="flex flex-1 justify-center items-center px-6 py-16">
            <div class="items-center gap-12 grid md:grid-cols-2 w-full max-w-5xl">

                <!-- LEFT -->
                <div>
                    <span
                        class="inline-block bg-orange-500/10 mb-6 px-4 py-2 rounded-full font-bold text-orange-400 text-sm">
                        Motorcycle Repair Dispatch System
                    </span>

                    <h2 class="mb-6 font-black text-4xl md:text-6xl leading-tight">
                        Manage repair requests faster and easier.
                    </h2>

                    <p class="mb-8 text-gray-400 text-lg leading-relaxed">
                        MechFinder helps motor shops receive dispatch requests, manage availability,
                        and connect with motorists during motorcycle breakdowns.
                    </p>

                    <div class="flex sm:flex-row flex-col gap-4">
                        <a href="/login"
                            class="bg-orange-500 hover:bg-orange-600 px-8 py-4 rounded-xl font-black text-black text-center transition">
                            Log In
                        </a>

                        <a href="{{ route('signup') }}"
                            class="px-8 py-4 border border-white/20 hover:border-orange-500 rounded-xl font-bold text-white hover:text-orange-400 text-center transition">
                            Register as Motorist
                        </a>

                        <a href="{{ route('signup.shop') }}"
                            class="hover:bg-orange-500/10 px-8 py-4 border border-orange-500/40 rounded-xl font-bold text-orange-400 text-center transition">
                            Register Your Shop
                        </a>
                    </div>
                </div>

                <!-- RIGHT CARD -->
                <div class="bg-[#1A1A1A] shadow-xl p-8 border border-white/10 rounded-3xl">
                    <h3 class="mb-6 font-black text-2xl">
                        Portal Features
                    </h3>

                    <div class="space-y-5">
                        <div class="flex gap-4">
                            <div
                                class="flex justify-center items-center bg-orange-500/10 rounded-xl w-11 h-11 text-orange-400">
                                📩
                            </div>
                            <div>
                                <h4 class="font-bold">Dispatch Requests</h4>
                                <p class="text-gray-400 text-sm">View incoming motorist requests.</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div
                                class="flex justify-center items-center bg-orange-500/10 rounded-xl w-11 h-11 text-orange-400">
                                🟢
                            </div>
                            <div>
                                <h4 class="font-bold">Shop Status</h4>
                                <p class="text-gray-400 text-sm">Set your shop as open, busy, or closed.</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div
                                class="flex justify-center items-center bg-orange-500/10 rounded-xl w-11 h-11 text-orange-400">
                                📍
                            </div>
                            <div>
                                <h4 class="font-bold">Location Based</h4>
                                <p class="text-gray-400 text-sm">Help nearby motorists find your shop.</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div
                                class="flex justify-center items-center bg-orange-500/10 rounded-xl w-11 h-11 text-orange-400">
                                ⭐
                            </div>
                            <div>
                                <h4 class="font-bold">Ratings</h4>
                                <p class="text-gray-400 text-sm">Build trust through completed jobs and reviews.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>

        <!-- FOOTER -->
        <footer class="px-6 py-6 border-white/10 border-t text-gray-500 text-sm text-center">
            © 2026 MechFinder. Built for motor shops and motorists in Olongapo City.
        </footer>

    </div>

</body>

</html>
