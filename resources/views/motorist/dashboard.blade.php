<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motorist Dashboard - MechFinder</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#0f0f0f] text-white">
    <div class="min-h-screen flex flex-col">
        <!-- HEADER -->
        <header class="bg-[#1a1a1a] border-b border-white/10 px-6 py-4">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-[#F7941D] flex items-center justify-center font-black">
                        ⚙
                    </div>
                    <div>
                        <h1 class="text-2xl font-black text-[#F7941D]">MechFinder</h1>
                        <p class="text-xs text-gray-400">Motorist Dashboard</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-300">Welcome, {{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="bg-[#F7941D] hover:bg-orange-600 px-5 py-2 rounded-lg font-bold text-sm transition">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <main class="flex-1 max-w-7xl mx-auto w-full px-6 py-12">
            <div class="grid md:grid-cols-2 gap-8 mb-12">
                <!-- QUICK ACTIONS -->
                <div>
                    <h2 class="text-2xl font-black mb-6">Quick Actions</h2>

                    <div class="space-y-4">
                        <a href="{{ route('motorist.index') }}"
                            class="block bg-[#1a1a1a] border border-white/10 hover:border-[#F7941D] rounded-2xl p-6 transition">
                            <div class="text-3xl mb-3">🏍</div>
                            <h3 class="font-bold text-lg mb-1">Find Shops</h3>
                            <p class="text-sm text-gray-400">Browse nearby repair shops</p>
                        </a>

                        <a href="{{ route('motorist.index') }}"
                            class="block bg-[#1a1a1a] border border-white/10 hover:border-[#F7941D] rounded-2xl p-6 transition">
                            <div class="text-3xl mb-3">🚨</div>
                            <h3 class="font-bold text-lg mb-1">Request Dispatch</h3>
                            <p class="text-sm text-gray-400">Get emergency assistance now</p>
                        </a>
                    </div>
                </div>

                <!-- ACCOUNT INFO -->
                <div>
                    <h2 class="text-2xl font-black mb-6">Account Information</h2>

                    <div class="bg-[#1a1a1a] border border-white/10 rounded-2xl p-6">
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-400 mb-1">Name</p>
                                <p class="font-bold text-lg">{{ Auth::user()->name }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-400 mb-1">Email</p>
                                <p class="font-bold text-lg">{{ Auth::user()->email }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-400 mb-1">Role</p>
                                <p class="font-bold text-lg capitalize">{{ Auth::user()->role }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FEATURES -->
            <div class="bg-[#1a1a1a] border border-white/10 rounded-2xl p-8">
                <h2 class="text-2xl font-black mb-6">Features Available</h2>

                <div class="grid md:grid-cols-3 gap-6">
                    <div class="flex items-start gap-4">
                        <div class="text-3xl">📍</div>
                        <div>
                            <h3 class="font-bold mb-2">Find Nearby Shops</h3>
                            <p class="text-sm text-gray-400">Locate repair shops closest to your location</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="text-3xl">💬</div>
                        <div>
                            <h3 class="font-bold mb-2">Live Chat</h3>
                            <p class="text-sm text-gray-400">Communicate with shop owners in real-time</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="text-3xl">⭐</div>
                        <div>
                            <h3 class="font-bold mb-2">Leave Reviews</h3>
                            <p class="text-sm text-gray-400">Share your experience with other motorists</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
