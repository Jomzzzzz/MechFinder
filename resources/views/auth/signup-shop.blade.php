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
        linear-gradient(to top, rgba(0,0,0,.58), rgba(0,0,0,.12)),
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

<body class="flex justify-center items-center px-4 py-10 min-h-screen">

    <div class="grid md:grid-cols-2 w-full max-w-7xl min-h-[850px] overflow-hidden glass-card">

        <!-- LEFT PANEL -->
        <div class="hidden relative md:flex flex-col justify-between left-panel p-10 text-white">

            <a href="/" class="flex items-center gap-4">
                <div class="flex justify-center items-center bg-white rounded-full w-12 h-12 text-black text-xl">
                    ⚙
                </div>

                <div>
                    <h1 class="font-bold text-2xl">
                        MechFinder
                    </h1>

                    <p class="text-white/70 text-xs">
                        Shop Owner Portal
                    </p>
                </div>
            </a>

            <div>
                <h2 class="mb-4 font-black text-5xl leading-tight">
                    Grow Your Motorcycle Shop
                </h2>

                <p class="max-w-md text-white/90 text-lg leading-relaxed">
                    Connect with nearby motorists, receive repair dispatch requests,
                    and manage your mechanic services in real-time.
                </p>

                <div class="flex items-center gap-2 mt-10">
                    <div class="bg-white rounded-full w-12 h-1"></div>
                    <div class="bg-white/70 rounded-full w-2 h-2"></div>
                    <div class="bg-white/70 rounded-full w-2 h-2"></div>
                </div>
            </div>

        </div>

        <!-- RIGHT PANEL -->
        <div class="relative flex flex-col justify-center bg-white px-8 md:px-16 py-12">

            <!-- HOME BUTTON -->
            <div class="top-8 right-8 absolute">
                <a href="/"
                    class="inline-flex justify-center items-center bg-black hover:bg-orange-500 px-8 py-3 rounded-full font-semibold text-white text-sm transition">
                    Home
                </a>
            </div>

            <div class="mx-auto w-full max-w-xl">

                <span class="inline-block mb-4 font-semibold text-orange-500 text-sm">
                    Motorcycle Repair Dispatch System
                </span>

                <h1 class="mb-2 font-black text-[#101633] text-4xl">
                    Shop Registration
                </h1>

                <p class="mb-8 text-gray-500">
                    Register your mechanic shop and start receiving repair requests.
                </p>

                <!-- ERRORS -->
                @if ($errors->any())
                    <div class="bg-red-50 mb-5 p-4 border border-red-200 rounded-xl">
                        <ul class="space-y-1 text-red-500 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- FORM -->
                <form method="POST"
                    action="{{ route('signup.shop.post') }}"
                    class="space-y-5">

                    @csrf

                    <!-- SHOP DETAILS -->
                    <div>
                        <p class="mb-4 font-bold text-gray-400 text-xs uppercase tracking-[3px]">
                            Shop Details
                        </p>

                        <div class="space-y-4">

                            <div>
                                <label class="block mb-2 font-semibold text-gray-700 text-sm">
                                    Shop Name
                                </label>

                                <input type="text"
                                    name="shop_name"
                                    value="{{ old('shop_name') }}"
                                    required
                                    placeholder="Juan's Auto Repair"
                                    class="bg-white px-4 py-4 border border-gray-200 focus:border-orange-500 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-100 w-full text-gray-700 placeholder-gray-400 transition">
                            </div>

                            <div>
                                <label class="block mb-2 font-semibold text-gray-700 text-sm">
                                    Address
                                </label>

                                <input type="text"
                                    name="address"
                                    value="{{ old('address') }}"
                                    required
                                    placeholder="123 Rizal Ave, Olongapo City"
                                    class="bg-white px-4 py-4 border border-gray-200 focus:border-orange-500 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-100 w-full text-gray-700 placeholder-gray-400 transition">
                            </div>

                            <div>
                                <label class="block mb-2 font-semibold text-gray-700 text-sm">
                                    Phone Number
                                    <span class="text-gray-400">(optional)</span>
                                </label>

                                <input type="text"
                                    name="phone"
                                    value="{{ old('phone') }}"
                                    placeholder="09XXXXXXXXX"
                                    class="bg-white px-4 py-4 border border-gray-200 focus:border-orange-500 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-100 w-full text-gray-700 placeholder-gray-400 transition">
                            </div>

                        </div>
                    </div>

                    <!-- OWNER ACCOUNT -->
                    <div class="pt-2">

                        <p class="mb-4 font-bold text-gray-400 text-xs uppercase tracking-[3px]">
                            Owner Account
                        </p>

                        <div class="space-y-4">

                            <div>
                                <label class="block mb-2 font-semibold text-gray-700 text-sm">
                                    Full Name
                                </label>

                                <input type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    required
                                    placeholder="Juan dela Cruz"
                                    class="bg-white px-4 py-4 border border-gray-200 focus:border-orange-500 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-100 w-full text-gray-700 placeholder-gray-400 transition">
                            </div>

                            <div>
                                <label class="block mb-2 font-semibold text-gray-700 text-sm">
                                    Email Address
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
                                        placeholder="Minimum 6 characters"
                                        class="bg-white px-4 py-4 pr-12 border border-gray-200 focus:border-orange-500 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-100 w-full text-gray-700 placeholder-gray-400 transition">

                                    <button type="button"
                                        onclick="togglePassword('password')"
                                        class="top-1/2 right-4 absolute text-gray-400 hover:text-orange-500 -translate-y-1/2 transition">
                                        👁
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block mb-2 font-semibold text-gray-700 text-sm">
                                    Confirm Password
                                </label>

                                <div class="relative">
                                    <input id="password_confirmation"
                                        type="password"
                                        name="password_confirmation"
                                        required
                                        placeholder="Repeat your password"
                                        class="bg-white px-4 py-4 pr-12 border border-gray-200 focus:border-orange-500 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-100 w-full text-gray-700 placeholder-gray-400 transition">

                                    <button type="button"
                                        onclick="togglePassword('password_confirmation')"
                                        class="top-1/2 right-4 absolute text-gray-400 hover:text-orange-500 -translate-y-1/2 transition">
                                        👁
                                    </button>
                                </div>
                            </div>

                        </div>

                    </div>

                    <!-- BUTTON -->
                    <button type="submit"
                        class="bg-[#202020] hover:bg-orange-500 mt-2 py-4 rounded-xl w-full font-black text-white transition duration-300">
                        Register Shop
                    </button>

                </form>

                <!-- LINKS -->
                <div class="space-y-2 mt-8 text-center">

                    <p class="text-gray-500 text-sm">
                        Already have an account?

                        <a href="{{ route('login') }}"
                            class="font-bold text-orange-500 hover:text-orange-600 transition">
                            Log in
                        </a>
                    </p>

                

                </div>

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