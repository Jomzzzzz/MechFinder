<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MechFinder | Login</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: Inter, Arial, sans-serif;
            background:
                linear-gradient(rgba(15,15,15,.90), rgba(15,15,15,.94)),
                url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1600&q=80');
            background-size: cover;
            background-position: center;
        }

        * {
            letter-spacing: -0.01em;
        }

        .clean-title {
            letter-spacing: -0.04em;
        }

        .clean-text {
            line-height: 1.75;
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

<body class="min-h-screen flex items-center justify-center px-4 py-8 text-white">

    <div class="w-full max-w-7xl overflow-hidden rounded-[2rem] border border-white/10 bg-[#111111]/95 shadow-2xl grid lg:grid-cols-2">

        <!-- LEFT PANEL -->
        <div class="hidden lg:flex min-h-[760px] flex-col justify-between bg-[#0f0f0f] border-r border-white/10 p-10 xl:p-14">

            <!-- LOGO -->
            <a href="/" class="flex items-center gap-4">

                <div class="w-12 h-12 rounded-full bg-[#F7941D] flex items-center justify-center text-white text-xl font-black shadow-lg shadow-orange-500/20">
                    ⚙
                </div>

                <div>
                    <h1 class="text-2xl font-black text-[#F7941D]">
                        MechFinder
                    </h1>

                    <p class="text-xs text-gray-500">
                        Motorcycle Repair Dispatch System
                    </p>
                </div>

            </a>

            <!-- CONTENT -->
            <div>

                <span class="inline-flex items-center gap-2 bg-white/5 border border-white/10 px-4 py-2 rounded-full text-xs font-bold text-gray-300 mb-8">
                    🏍 For Motorists and Shop Owners
                </span>

                <h2 class="clean-title text-4xl xl:text-5xl font-black leading-[1.05] mb-6 text-white max-w-xl">
                    One login for repair help and shop management.
                </h2>

                <p class="clean-text max-w-xl text-gray-400 text-base">
                    Access MechFinder as a motorist or shop owner.
                    Find nearby repair shops, request roadside assistance,
                    manage dispatch requests, and stay updated in real time.
                </p>

                <!-- FEATURE GRID -->
                <div class="grid grid-cols-3 gap-4 mt-12">

                    <!-- CARD -->
                    <div class="bg-[#1a1a1a] border border-white/5 rounded-3xl p-5">

                        <div class="text-3xl mb-4">
                            🏍
                        </div>

                        <h3 class="font-bold text-white text-sm mb-2">
                            Motorists
                        </h3>

                        <p class="text-gray-500 text-xs leading-relaxed">
                            Find nearby repair shops and request roadside assistance.
                        </p>

                    </div>

                    <!-- CARD -->
                    <div class="bg-[#1a1a1a] border border-white/5 rounded-3xl p-5">

                        <div class="text-3xl mb-4">
                            🛠
                        </div>

                        <h3 class="font-bold text-white text-sm mb-2">
                            Shop Owners
                        </h3>

                        <p class="text-gray-500 text-xs leading-relaxed">
                            Manage requests and update repair shop availability.
                        </p>

                    </div>

                    <!-- CARD -->
                    <div class="bg-[#1a1a1a] border border-white/5 rounded-3xl p-5">

                        <div class="text-3xl mb-4">
                            💬
                        </div>

                        <h3 class="font-bold text-white text-sm mb-2">
                            Live Updates
                        </h3>

                        <p class="text-gray-500 text-xs leading-relaxed">
                            Chat and track dispatch progress in real-time.
                        </p>

                    </div>

                </div>

            </div>

            <!-- FOOTER -->
            <p class="text-xs text-gray-600">
                © 2026 MechFinder. Built for motorcycle repair services in Olongapo City.
            </p>

        </div>

        <!-- RIGHT PANEL -->
        <div class="relative flex items-center bg-[#111111] px-6 sm:px-8 md:px-14 lg:px-16 py-10 md:py-12">

            <!-- HOME -->
            <a href="/"
               class="absolute top-5 right-5 bg-[#1a1a1a] hover:bg-[#F7941D] border border-white/10 px-5 py-2.5 rounded-full text-xs font-bold text-white transition">
                Home
            </a>

            <div class="w-full max-w-md mx-auto">

                <!-- MOBILE LOGO -->
                <div class="lg:hidden mb-10">

                    <a href="/" class="flex items-center gap-4">

                        <div class="w-12 h-12 rounded-full bg-[#F7941D] flex items-center justify-center text-white text-xl font-black shadow-lg shadow-orange-500/20">
                            ⚙
                        </div>

                        <div>
                            <h1 class="text-2xl font-black text-[#F7941D]">
                                MechFinder
                            </h1>

                            <p class="text-xs text-gray-500">
                                Motorcycle Repair Dispatch System
                            </p>
                        </div>

                    </a>

                </div>

                <!-- HEADER -->
                <span class="inline-block mb-4 text-[#F7941D] text-xs font-black uppercase tracking-[0.2em]">
                    Secure Access
                </span>

                <h1 class="clean-title text-4xl md:text-5xl font-black mb-3 text-white">
                    Welcome back
                </h1>

                <p class="clean-text text-gray-400 mb-8 text-sm">
                    Sign in to continue using MechFinder.
                </p>

                <!-- ERRORS -->
                @if (session('error'))
                    <div class="mb-4 rounded-2xl border border-red-500/20 bg-red-500/10 p-4">
                        <p class="text-red-400 text-sm">{{ session('error') }}</p>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-4 rounded-2xl border border-green-500/20 bg-green-500/10 p-4">
                        <p class="text-green-400 text-sm">{{ session('success') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-2xl border border-red-500/20 bg-red-500/10 p-4">
                        <ul class="space-y-1 text-red-400 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- FORM -->
                <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                    @csrf

                    <!-- EMAIL -->
                    <div>

                        <label class="block mb-2 text-sm font-medium text-gray-300">
                            Gmail Address
                        </label>

                        <input type="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               placeholder="example@gmail.com"
                               class="w-full rounded-2xl border border-white/10 bg-[#1a1a1a] px-5 py-4 text-white placeholder-gray-500 outline-none transition focus:border-[#F7941D] focus:ring-4 focus:ring-orange-500/10">

                    </div>

                    <!-- PASSWORD -->
                    <div>

                        <label class="block mb-2 text-sm font-medium text-gray-300">
                            Password
                        </label>

                        <div class="relative">

                            <input id="password"
                                   type="password"
                                   name="password"
                                   required
                                   placeholder="Enter your password"
                                   class="w-full rounded-2xl border border-white/10 bg-[#1a1a1a] px-5 py-4 pr-12 text-white placeholder-gray-500 outline-none transition focus:border-[#F7941D] focus:ring-4 focus:ring-orange-500/10">

                            <button type="button"
                                    onclick="togglePassword()"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-[#F7941D] transition">

                                <svg id="eyeOpen"
                                     xmlns="http://www.w3.org/2000/svg"
                                     class="w-5 h-5"
                                     fill="none"
                                     viewBox="0 0 24 24"
                                     stroke="currentColor">

                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />

                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5
                                          c4.478 0 8.268 2.943 9.542 7
                                          -1.274 4.057-5.064 7-9.542 7
                                          -4.477 0-8.268-2.943-9.542-7z" />
                                </svg>

                                <svg id="eyeClosed"
                                     xmlns="http://www.w3.org/2000/svg"
                                     class="hidden w-5 h-5"
                                     fill="none"
                                     viewBox="0 0 24 24"
                                     stroke="currentColor">

                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M13.875 18.825A10.05 10.05 0 0112 19
                                          c-4.478 0-8.268-2.943-9.542-7
                                          a9.956 9.956 0 012.293-3.95m3.087-2.523
                                          A9.953 9.953 0 0112 5
                                          c4.478 0 8.268 2.943 9.542 7
                                          a9.97 9.97 0 01-4.132 5.411M15 12
                                          a3 3 0 11-6 0 3 3 0 016 0zm6 6L3 3" />
                                </svg>

                            </button>

                        </div>

                    </div>

                    <!-- BUTTON -->
                    <button type="submit"
                            class="w-full rounded-2xl bg-[#F7941D] hover:bg-orange-600 py-4 font-black text-white transition shadow-lg shadow-orange-500/20">
                        Login
                    </button>

                </form>

                <!-- LINKS -->
                <div class="mt-8 space-y-3 text-center text-sm text-gray-500">

                    <p>
                        New shop owner?

                        <a href="{{ route('signup.shop') }}"
                           class="font-bold text-[#F7941D] hover:text-orange-400 transition">
                            Register your shop
                        </a>
                    </p>

                    <p>
                        New motorist?

                        <a href="{{ route('signup') }}"
                           class="font-bold text-[#F7941D] hover:text-orange-400 transition">
                            Sign up
                        </a>
                    </p>

                </div>

            </div>

        </div>

    </div>

    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const eyeOpen = document.getElementById('eyeOpen');
            const eyeClosed = document.getElementById('eyeClosed');

            if (password.type === 'password') {
                password.type = 'text';

                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                password.type = 'password';

                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }
    </script>

</body>
</html>