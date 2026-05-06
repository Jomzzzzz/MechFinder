<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motorist Sign Up - MechFinder</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex justify-center items-center bg-[#0f0f0f] px-4 min-h-screen">

    <div class="bg-white/5 p-8 border border-white/10 rounded-2xl w-full max-w-md">

        <h1 class="mb-2 font-black text-orange-500 text-4xl text-center">⚙ MECHFINDER</h1>
        <p class="mb-8 text-gray-400 text-center">Create your motorist account</p>

        @if ($errors->any())
            <div class="bg-red-500/10 mb-4 p-4 border border-red-500/30 rounded-lg">
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
                <label class="block mb-2 font-semibold text-gray-300 text-sm">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Juan dela Cruz"
                    class="bg-white/10 px-4 py-3 border border-white/10 focus:border-orange-500 rounded-lg focus:outline-none w-full text-white placeholder-gray-500">
            </div>

            <div>
                <label class="block mb-2 font-semibold text-gray-300 text-sm">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    placeholder="example@gmail.com"
                    class="bg-white/10 px-4 py-3 border border-white/10 focus:border-orange-500 rounded-lg focus:outline-none w-full text-white placeholder-gray-500">
            </div>

            <div>
                <label class="block mb-2 font-semibold text-gray-300 text-sm">Password</label>
                <div class="relative">
                    <input id="password" type="password" name="password" required placeholder="Minimum 6 characters"
                        class="bg-white/10 px-4 py-3 pr-12 border border-white/10 focus:border-orange-500 rounded-lg focus:outline-none w-full text-white placeholder-gray-500">
                    <button type="button" onclick="togglePassword('password')"
                        class="top-1/2 right-3 absolute text-gray-400 hover:text-orange-400 -translate-y-1/2">👁</button>
                </div>
            </div>

            <div>
                <label class="block mb-2 font-semibold text-gray-300 text-sm">Confirm Password</label>
                <div class="relative">
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        placeholder="Repeat your password"
                        class="bg-white/10 px-4 py-3 pr-12 border border-white/10 focus:border-orange-500 rounded-lg focus:outline-none w-full text-white placeholder-gray-500">
                    <button type="button" onclick="togglePassword('password_confirmation')"
                        class="top-1/2 right-3 absolute text-gray-400 hover:text-orange-400 -translate-y-1/2">👁</button>
                </div>
            </div>

            <button type="submit"
                class="bg-orange-500 hover:bg-orange-600 py-3 rounded-lg w-full font-bold text-white transition">
                Create Motorist Account
            </button>
        </form>

        <div class="space-y-2 mt-6 text-center">
            <p class="text-gray-400 text-sm">
                Already have an account?
                <a href="{{ route('login') }}" class="font-semibold text-orange-400 hover:text-orange-300">Log in</a>
            </p>
            <p class="text-gray-400 text-sm">
                Registering a shop?
                <a href="{{ route('signup.shop') }}" class="font-semibold text-orange-400 hover:text-orange-300">Shop
                    Sign Up</a>
            </p>
        </div>

    </div>

    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>

</body>

</html>
