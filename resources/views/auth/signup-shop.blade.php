{{-- resources/views/auth/signup-shop.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Sign Up - MechFinder</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: Inter, Arial, sans-serif;
            background: #0f0f0f;
        }

        .auth-bg {
            background:
                linear-gradient(90deg, rgba(15,15,15,.96), rgba(15,15,15,.82)),
                url('/images/clean-shop.jpg');
            background-size: cover;
            background-position: center;
        }

        input::-ms-reveal,
        input::-ms-clear {
            display: none;
        }

        input::-webkit-credentials-auto-fill-button {
            visibility: hidden;
            display: none !important;
            pointer-events: none;
        }
    </style>
</head>

<body class="min-h-screen bg-[#0f0f0f] text-white">

    <main class="min-h-screen grid lg:grid-cols-2">

        <!-- LEFT BRAND PANEL -->
        <section class="auth-bg hidden lg:flex flex-col justify-between p-12">

            <a href="/" class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-[#F7941D] flex items-center justify-center text-white text-xl font-black shadow-lg shadow-orange-500/30">
                    ⚙
                </div>

                <div>
                    <h1 class="text-2xl font-black text-[#F7941D]">
                        MechFinder
                    </h1>
                    <p class="text-xs text-gray-300">
                        Shop Owner Portal
                    </p>
                </div>
            </a>

            <div class="max-w-xl">
                <span class="inline-flex items-center gap-2 bg-white/10 border border-white/10 px-5 py-2 rounded-full text-sm font-bold mb-8">
                    🛠 Motor Shop Registration
                </span>

                <h2 class="text-5xl xl:text-6xl font-black leading-tight mb-6">
                    Grow your motorcycle repair shop.
                </h2>

                <p class="text-lg text-gray-300 leading-relaxed">
                    Register your shop, receive nearby dispatch requests, manage your availability,
                    and connect with motorists who need emergency repair assistance.
                </p>
            </div>

            <div class="grid grid-cols-3 gap-4 max-w-xl">
                <div class="bg-white/10 border border-white/10 rounded-2xl p-4">
                    <div class="text-2xl mb-2">📩</div>
                    <p class="text-xs font-black">Requests</p>
                </div>

                <div class="bg-white/10 border border-white/10 rounded-2xl p-4">
                    <div class="text-2xl mb-2">🟢</div>
                    <p class="text-xs font-black">Status</p>
                </div>

                <div class="bg-white/10 border border-white/10 rounded-2xl p-4">
                    <div class="text-2xl mb-2">📊</div>
                    <p class="text-xs font-black">Analytics</p>
                </div>
            </div>

        </section>

        <!-- RIGHT FORM PANEL -->
        <section class="flex items-center justify-center px-5 py-10 lg:px-12">

            <div class="w-full max-w-2xl">

                <!-- MOBILE BRAND -->
                <div class="lg:hidden mb-10 flex items-center justify-between">
                    <a href="/" class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-full bg-[#F7941D] flex items-center justify-center text-white text-xl font-black">
                            ⚙
                        </div>
                        <div>
                            <h1 class="text-2xl font-black text-[#F7941D]">MechFinder</h1>
                            <p class="text-xs text-gray-400">Shop Owner Portal</p>
                        </div>
                    </a>

                    <a href="/"
                       class="bg-[#1a1a1a] hover:bg-[#222] border border-white/10 px-5 py-2 rounded-full text-sm font-bold transition">
                        Home
                    </a>
                </div>

                <!-- DESKTOP HOME -->
                <div class="hidden lg:flex justify-end mb-8">
                    <a href="/"
                       class="bg-[#1a1a1a] hover:bg-[#222] border border-white/10 px-6 py-3 rounded-full text-sm font-bold transition">
                        Home
                    </a>
                </div>

                <div class="bg-[#1a1a1a] border border-white/10 rounded-[2rem] p-6 md:p-10 shadow-2xl">

                    <div class="mb-8">
                        <span class="inline-block mb-3 text-[#F7941D] text-sm font-black">
                            MOTORCYCLE REPAIR DISPATCH SYSTEM
                        </span>

                        <h1 class="text-4xl md:text-5xl font-black text-white mb-3">
                            Shop Registration
                        </h1>

                        <p class="text-gray-400">
                            Create your motor shop account and start receiving repair requests.
                        </p>
                    </div>

                    <!-- ERRORS -->
                    @if ($errors->any())
                        <div class="mb-6 bg-red-500/10 border border-red-500/30 rounded-2xl p-4">
                            <ul class="space-y-1 text-red-400 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- FORM -->
                    <form method="POST" action="{{ route('signup.shop.post') }}" class="space-y-8">
                        @csrf

                        <!-- SHOP DETAILS -->
                        <div>
                            <div class="flex items-center gap-3 mb-5">
                                <div class="w-9 h-9 rounded-full bg-[#F7941D] flex items-center justify-center text-white font-black">
                                    1
                                </div>
                                <div>
                                    <h2 class="font-black text-white">
                                        Shop Details
                                    </h2>
                                    <p class="text-xs text-gray-400">
                                        Basic information about your motorcycle repair shop.
                                    </p>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block mb-2 text-sm font-bold text-gray-300">
                                        Shop Name
                                    </label>

                                    <input type="text"
                                        name="shop_name"
                                        value="{{ old('shop_name') }}"
                                        required
                                        placeholder="Juan's Auto Repair"
                                        class="w-full bg-[#0f0f0f] border border-white/10 focus:border-[#F7941D] focus:ring-4 focus:ring-orange-500/10 rounded-2xl px-5 py-4 text-white placeholder-gray-500 outline-none transition">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block mb-2 text-sm font-bold text-gray-300">
                                        Address
                                    </label>

                                    <input type="text"
                                        name="address"
                                        value="{{ old('address') }}"
                                        required
                                        placeholder="123 Rizal Ave, Olongapo City"
                                        class="w-full bg-[#0f0f0f] border border-white/10 focus:border-[#F7941D] focus:ring-4 focus:ring-orange-500/10 rounded-2xl px-5 py-4 text-white placeholder-gray-500 outline-none transition">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block mb-2 text-sm font-bold text-gray-300">
                                        Phone Number
                                        <span class="text-gray-500">(optional)</span>
                                    </label>

                                    <input type="text"
                                        name="phone"
                                        value="{{ old('phone') }}"
                                        placeholder="09XXXXXXXXX"
                                        class="w-full bg-[#0f0f0f] border border-white/10 focus:border-[#F7941D] focus:ring-4 focus:ring-orange-500/10 rounded-2xl px-5 py-4 text-white placeholder-gray-500 outline-none transition">
                                </div>
                            </div>
                        </div>

                        <!-- OWNER ACCOUNT -->
                        <div>
                            <div class="flex items-center gap-3 mb-5">
                                <div class="w-9 h-9 rounded-full bg-[#F7941D] flex items-center justify-center text-white font-black">
                                    2
                                </div>
                                <div>
                                    <h2 class="font-black text-white">
                                        Owner Account
                                    </h2>
                                    <p class="text-xs text-gray-400">
                                        Login information for the shop owner.
                                    </p>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block mb-2 text-sm font-bold text-gray-300">
                                        Full Name
                                    </label>

                                    <input type="text"
                                        name="name"
                                        value="{{ old('name') }}"
                                        required
                                        placeholder="Juan dela Cruz"
                                        class="w-full bg-[#0f0f0f] border border-white/10 focus:border-[#F7941D] focus:ring-4 focus:ring-orange-500/10 rounded-2xl px-5 py-4 text-white placeholder-gray-500 outline-none transition">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block mb-2 text-sm font-bold text-gray-300">
                                        Email Address
                                    </label>

                                    <input type="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        required
                                        placeholder="example@gmail.com"
                                        class="w-full bg-[#0f0f0f] border border-white/10 focus:border-[#F7941D] focus:ring-4 focus:ring-orange-500/10 rounded-2xl px-5 py-4 text-white placeholder-gray-500 outline-none transition">
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-bold text-gray-300">
                                        Password
                                    </label>

                                    <div class="relative">
                                        <input id="password"
                                            type="password"
                                            name="password"
                                            required
                                            placeholder="Minimum 6 characters"
                                            class="w-full bg-[#0f0f0f] border border-white/10 focus:border-[#F7941D] focus:ring-4 focus:ring-orange-500/10 rounded-2xl px-5 py-4 pr-12 text-white placeholder-gray-500 outline-none transition">

                                        <button type="button"
                                            onclick="togglePassword('password')"
                                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#F7941D] transition">
                                            👁
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-bold text-gray-300">
                                        Confirm Password
                                    </label>

                                    <div class="relative">
                                        <input id="password_confirmation"
                                            type="password"
                                            name="password_confirmation"
                                            required
                                            placeholder="Repeat password"
                                            class="w-full bg-[#0f0f0f] border border-white/10 focus:border-[#F7941D] focus:ring-4 focus:ring-orange-500/10 rounded-2xl px-5 py-4 pr-12 text-white placeholder-gray-500 outline-none transition">

                                        <button type="button"
                                            onclick="togglePassword('password_confirmation')"
                                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#F7941D] transition">
                                            👁
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full bg-[#F7941D] hover:bg-orange-600 py-4 rounded-2xl font-black text-white transition shadow-lg shadow-orange-500/20">
                            Register Shop
                        </button>
                    </form>

                    <p class="mt-8 text-center text-sm text-gray-400">
                        Already have an account?
                        <a href="{{ route('login') }}"
                           class="font-black text-[#F7941D] hover:text-orange-400 transition">
                            Log in
                        </a>
                    </p>

                </div>

            </div>

        </section>

    </main>
    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);

            input.type =
                input.type === 'password'
                ? 'text'
                : 'password';
        }
    </script>

</body>
</html>