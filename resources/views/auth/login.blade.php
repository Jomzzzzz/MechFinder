<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Login - MechFinder</title>

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

        .glass-card {
            background: rgba(255,255,255,0.96);
            box-shadow: 0 30px 80px rgba(0,0,0,.35);
        }

        .left-panel {
    background:
        linear-gradient(to top, rgba(0,0,0,.72), rgba(0,0,0,.18)),
        url('https://images.unsplash.com/photo-1486006920555-c77dcf18193c?auto=format&fit=crop&w=1200&q=80');
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

<body class="flex justify-center items-center px-4 py-10 min-h-screen">

    <div class="grid md:grid-cols-2 w-full max-w-6xl min-h-[720px] overflow-hidden glass-card">

        <!-- LEFT SIDE -->
        <div class="hidden relative md:flex flex-col justify-between left-panel p-10 text-white">

            <div class="flex items-center gap-4">
                <div class="flex justify-center items-center bg-white rounded-full w-12 h-12 text-black text-xl">
                    ⚙
                </div>

                <div>
                    <h1 class="font-bold text-2xl">MechFinder</h1>
                    <p class="text-white/70 text-xs">Motor Shop Portal</p>
                </div>
            </div>

            <div>
                <h2 class="mb-4 font-black text-5xl leading-tight">
                    Welcome Back
                </h2>

                <p class="max-w-md text-white/90 text-lg leading-relaxed">
                    Manage dispatch requests, update shop availability,
                    and connect with motorists in real-time.
                </p>

                <div class="flex items-center gap-2 mt-10">
                    <div class="bg-white rounded-full w-12 h-1"></div>
                    <div class="bg-white/70 rounded-full w-2 h-2"></div>
                    <div class="bg-white/70 rounded-full w-2 h-2"></div>
                </div>
            </div>

        </div>

        <!-- RIGHT SIDE -->
        <div class="relative flex flex-col justify-center bg-white px-8 md:px-20 py-12">

            <div class="top-8 right-8 absolute">
                <a href="/"
                    class="inline-flex justify-center items-center bg-black hover:bg-orange-500 px-8 py-3 rounded-full font-semibold text-white text-sm transition">
                    Home
                </a>
            </div>

            <div class="mx-auto w-full max-w-md">

                <span class="inline-block mb-4 font-semibold text-orange-500 text-sm">
                    Motorcycle Repair Dispatch System
                </span>

                <h1 class="mb-2 font-black text-[#101633] text-4xl">
                    Shop Login
                </h1>

                <p class="mb-10 text-gray-500">
                    Sign in to your motor shop account.
                </p>

                @if (session('error'))
                    <div class="bg-red-50 mb-4 p-4 border border-red-200 rounded-xl">
                        <p class="text-red-500 text-sm">{{ session('error') }}</p>
                    </div>
                @endif

                @if (session('success'))
                    <div class="bg-green-50 mb-4 p-4 border border-green-200 rounded-xl">
                        <p class="text-green-500 text-sm">{{ session('success') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-50 mb-4 p-4 border border-red-200 rounded-xl">
                        <ul class="space-y-1 text-red-500 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block mb-2 font-semibold text-gray-700 text-sm">
                            Gmail Address
                        </label>

                        <input type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            placeholder="example@gmail.com"
                            class="bg-white px-4 py-4 border border-gray-200 focus:border-orange-500 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-100 w-full text-gray-700 placeholder-gray-400 transition">
                    </div>

                    <div>
                        <label class="block mb-2 font-semibold text-gray-700 text-sm">
                            Password
                        </label>

                        <div class="relative">
                            <input id="password"
                                type="password"
                                name="password"
                                required
                                placeholder="Enter your password"
                                class="bg-white px-4 py-4 pr-12 border border-gray-200 focus:border-orange-500 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-100 w-full text-gray-700 placeholder-gray-400 transition">

                            <button type="button"
                                onclick="togglePassword()"
                                class="top-1/2 right-4 absolute flex justify-center items-center text-gray-400 hover:text-orange-500 -translate-y-1/2 transition">

                                <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg"
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

                                <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg"
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

                    <button type="submit"
                        class="bg-[#202020] hover:bg-orange-500 py-4 rounded-xl w-full font-black text-white transition duration-300">
                        Login
                    </button>
                </form>

                <p class="mt-8 text-gray-500 text-sm text-center">
                    New motor shop?

                    <a href="{{ route('signup') }}"
                        class="font-bold text-orange-500 hover:text-orange-600 transition">
                        Sign up
                    </a>
                </p>

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