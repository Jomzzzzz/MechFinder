<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registered - MechFinder</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-[#0f0f0f] flex items-center justify-center px-4">

<div class="w-full max-w-md bg-white/5 border border-white/10 rounded-2xl p-8 text-center">

    <h1 class="text-4xl font-black text-orange-500 mb-4">⚙ MECHFINDER</h1>

    <div class="bg-green-500/10 border border-green-500/30 rounded-xl p-5 mb-6">
        <p class="text-green-400 font-bold text-lg">
            You are now registered to MechFinder.
        </p>
        <p class="text-gray-300 text-sm mt-2">
            Go to login to access your shop dashboard.
        </p>
    </div>

    <a href="{{ route('login') }}"
       class="block w-full bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-lg font-bold transition">
        Go to Login
    </a>

</div>

</body>
</html>