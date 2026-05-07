<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motorist Sign Up - MechFinder</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: Inter, Arial, sans-serif;
            background:
                linear-gradient(rgba(15,15,15,.88), rgba(15,15,15,.92)),
                url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1600&q=80');
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

<body class="min-h-screen flex items-center justify-center px-4 py-10 text-white">

    <div class="w-full max-w-6xl grid md:grid-cols-2 overflow-hidden rounded-[2rem] border border-white/10 bg-[#1a1a1a]/95 shadow-2xl">

        <!-- LEFT PANEL -->
        <div class="hidden md:flex min-h-[760px] flex-col justify-between p-10 bg-[#0f0f0f] border-r border-white/10">

            <a href="/" class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-[#F7941D] flex items-center justify-center text-white text-xl font-black shadow-lg shadow-orange-500/30">
                    ⚙
                </div>

                <div>
                    <h1 class="text-2xl font-black text-[#F7941D]">MechFinder</h1>
                    <p class="text-xs text-gray-400">Motorist Portal</p>
                </div>
            </a>

            <div>
                <span class="inline-flex items-center gap-2 bg-white/5 border border-white/10 rounded-full px-5 py-2 text-sm font-bold text-gray-300 mb-6">
                    🏍 Rider Assistance
                </span>

                <h2 class="text-5xl font-black leading-tight mb-5">
                    Find Help Faster
                </h2>

                <p class="max-w-md text-gray-300 text-lg leading-relaxed">
                    Create your account to request emergency motorcycle repair,
                    find nearby shops, and track dispatch updates.
                </p>

                <div class="grid grid-cols-3 gap-3 mt-10">
                    <div class="bg-[#1a1a1a] border border-white/10 rounded-2xl p-4">
                        <div class="text-2xl mb-2">📍</div>
                        <p class="text-xs font-bold text-gray-300">Nearby</p>
                    </div>

                    <div class="bg-[#1a1a1a] border border-white/10 rounded-2xl p-4">
                        <div class="text-2xl mb-2">🚨</div>
                        <p class="text-xs font-bold text-gray-300">Dispatch</p>
                    </div>

                    <div class="bg-[#1a1a1a] border border-white/10 rounded-2xl p-4">
                        <div class="text-2xl mb-2">💬</div>
                        <p class="text-xs font-bold text-gray-300">Chat</p>
                    </div>
                </div>
            </div>

            <p class="text-xs text-gray-500">
                Emergency motorcycle assistance for Olongapo City.
            </p>
        </div>

        <!-- RIGHT PANEL -->
        <div class="relative bg-[#111111] px-6 md:px-16 py-12 flex items-center">

            <a href="/"
               class="absolute top-6 right-6 bg-[#1a1a1a] hover:bg-[#F7941D] border border-white/10 px-6 py-3 rounded-full text-sm font-bold text-white transition">
                Home
            </a>

            <div class="w-full max-w-md mx-auto">

                <span class="inline-block mb-4 text-[#F7941D] text-sm font-black">
                    Motorcycle Emergency Assistance
                </span>

                <h1 class="text-4xl font-black mb-3 text-white">
                    Motorist Sign Up
                </h1>

                <p class="text-gray-400 mb-8">
                    Create your motorist account.
                </p>

                @if ($errors->any())
                    <div class="mb-4 rounded-2xl border border-red-500/30 bg-red-500/10 p-4">
                        <ul class="space-y-1 text-red-400 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('signup.post') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-300">
                            Full Name
                        </label>

                        <input type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            placeholder="Juan dela Cruz"
                            class="w-full rounded-2xl border border-white/10 bg-[#1a1a1a] px-5 py-4 text-white placeholder-gray-500 outline-none transition focus:border-[#F7941D] focus:ring-4 focus:ring-orange-500/10">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-300">
                            Email Address
                        </label>

                        <input type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            placeholder="example@gmail.com"
                            class="w-full rounded-2xl border border-white/10 bg-[#1a1a1a] px-5 py-4 text-white placeholder-gray-500 outline-none transition focus:border-[#F7941D] focus:ring-4 focus:ring-orange-500/10">
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
                                class="w-full rounded-2xl border border-white/10 bg-[#1a1a1a] px-5 py-4 pr-12 text-white placeholder-gray-500 outline-none transition focus:border-[#F7941D] focus:ring-4 focus:ring-orange-500/10">

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
                                placeholder="Repeat your password"
                                class="w-full rounded-2xl border border-white/10 bg-[#1a1a1a] px-5 py-4 pr-12 text-white placeholder-gray-500 outline-none transition focus:border-[#F7941D] focus:ring-4 focus:ring-orange-500/10">

                            <button type="button"
                                onclick="togglePassword('password_confirmation')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#F7941D] transition">
                                👁
                            </button>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full rounded-2xl bg-[#F7941D] hover:bg-orange-600 py-4 font-black text-white transition shadow-lg shadow-orange-500/20">
                        Create Motorist Account
                    </button>
                </form>

                <p class="mt-8 text-center text-sm text-gray-400">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-black text-[#F7941D] hover:text-orange-400 transition">
                        Log in
                    </a>
                </p>

            </div>
        </div>

    </div>

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