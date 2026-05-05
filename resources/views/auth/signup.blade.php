<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shop Sign Up - MechFinder</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-[#0f0f0f] flex items-center justify-center px-4">

<div class="w-full max-w-md bg-white/5 border border-white/10 rounded-2xl p-8">

    <h1 class="text-4xl font-black text-orange-500 text-center mb-2">⚙ MECHFINDER</h1>
    <p class="text-gray-400 text-center mb-8">Create Motor Shop Account</p>

    @if(session('error'))
        <div class="bg-red-500/10 border border-red-500/30 rounded-lg p-4 mb-4">
            <p class="text-red-400 text-sm">{{ session('error') }}</p>
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500/30 rounded-lg p-4 mb-4">
            <p class="text-green-400 text-sm">{{ session('success') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-500/10 border border-red-500/30 rounded-lg p-4 mb-4">
            <ul class="text-red-400 text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <a href="{{ route('auth.google.signup') }}"
       class="w-full bg-white text-gray-900 py-3 rounded-lg font-bold flex items-center justify-center hover:bg-gray-200 transition mb-6">
        Sign up with Gmail
    </a>

    <div class="flex items-center my-6">
        <div class="flex-1 border-t border-white/10"></div>
        <span class="px-3 text-gray-500 text-sm">or</span>
        <div class="flex-1 border-t border-white/10"></div>
    </div>

    <form method="POST" action="{{ route('signup.post') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-gray-300 mb-2">Full Name</label>
            <input 
                type="text" 
                name="name" 
                value="{{ old('name') }}"
                required
                placeholder="Your full name"
                class="w-full bg-white/10 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-orange-500"
            >
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-300 mb-2">Gmail Address</label>
            <input 
                type="email" 
                name="email" 
                value="{{ old('email') }}"
                required
                placeholder="example@gmail.com"
                class="w-full bg-white/10 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-orange-500"
            >
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-300 mb-2">Password</label>
            <input 
                type="password" 
                name="password" 
                required
                placeholder="Minimum 6 characters"
                class="w-full bg-white/10 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-orange-500"
            >
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-300 mb-2">Confirm Password</label>
            <input 
                type="password" 
                name="password_confirmation" 
                required
                placeholder="Confirm your password"
                class="w-full bg-white/10 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-orange-500"
            >
        </div>

        <button 
            type="submit"
            class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-lg font-bold transition"
        >
            Create Account
        </button>
    </form>

    <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-4 mt-6">
        <p class="text-blue-300 text-sm">
            Only real Gmail accounts are allowed for MechFinder shop owners.
        </p>
    </div>

    <p class="text-center text-gray-400 text-sm mt-6">
        Already have an account?
        <a href="{{ route('login') }}" class="text-orange-400 font-bold hover:text-orange-300">Login</a>
    </p>

</div>

</body>
</html>