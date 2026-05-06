<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Login - MechFinder</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex justify-center items-center bg-[#0f0f0f] px-4 min-h-screen">

    <div class="bg-white/5 p-8 border border-white/10 rounded-2xl w-full max-w-md">

        <h1 class="mb-2 font-black text-orange-500 text-4xl text-center">⚙ MECHFINDER</h1>
        <p class="mb-8 text-gray-400 text-center">Shop Admin Login</p>

        @if (session('error'))
            <div class="bg-red-500/10 mb-4 p-4 border border-red-500/30 rounded-lg">
                <p class="text-red-400 text-sm">{{ session('error') }}</p>
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-500/10 mb-4 p-4 border border-green-500/30 rounded-lg">
                <p class="text-green-400 text-sm">{{ session('success') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-500/10 mb-4 p-4 border border-red-500/30 rounded-lg">
                <ul class="space-y-1 text-red-400 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block mb-2 font-semibold text-gray-300 text-sm">Gmail Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    placeholder="example@gmail.com"
                    class="bg-white/10 px-4 py-3 border border-white/10 focus:border-orange-500 rounded-lg focus:outline-none w-full text-white placeholder-gray-500">
            </div>

            <div>
                <label class="block mb-2 font-semibold text-gray-300 text-sm">Password</label>

                <div class="relative">
                    <input id="password" type="password" name="password" required placeholder="Enter your password"
                        class="bg-white/10 px-4 py-3 pr-12 border border-white/10 focus:border-orange-500 rounded-lg focus:outline-none w-full text-white placeholder-gray-500">

                    <button type="button" onclick="togglePassword()"
                        class="top-1/2 right-3 absolute text-gray-400 hover:text-orange-400 -translate-y-1/2">
                        👁
                    </button>
                </div>
            </div>

            <button type="submit"
                class="bg-orange-500 hover:bg-orange-600 py-3 rounded-lg w-full font-bold text-white transition">
                Login
            </button>
        </form>

        <p class="mt-6 text-gray-400 text-sm text-center">
            New motor shop?
            <a href="{{ route('signup') }}" class="font-bold text-orange-400 hover:text-orange-300">Sign up</a>
        </p>

    </div>

    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            password.type = password.type === 'password' ? 'text' : 'password';
        }
    </script>

</body>

</html>
