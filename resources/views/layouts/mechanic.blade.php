<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MechFinder – Mechanic</title>

    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">

    @vite('resources/css/app.css')

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .heading-font {
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
    </style>
</head>

<body class="bg-[#0A0A0B] text-[#EEEEEE]">

    <div class="flex min-h-screen">

        {{-- Mechanic sidebar --}}
        <aside class="flex flex-col bg-[#121214] border-white/5 border-r w-64">

            <div class="p-8">
                <h1 class="text-[#F7941D] text-2xl heading-font">⚙ MECHFINDER</h1>
                <p class="mt-1 text-gray-500 text-xs">Mechanic Portal</p>
            </div>

            <nav class="flex-1 space-y-2 px-4">
                <a href="{{ route('mechanic.dashboard') }}"
                    class="block px-4 py-3 rounded {{ request()->is('mechanic/dashboard') || request()->is('mechanic') ? 'bg-white/5 border-l-4 border-[#F7941D] text-white' : 'text-gray-500 hover:text-white hover:bg-white/5' }}">
                    My Jobs
                </a>
                <a href="{{ route('mechanic.profile') }}"
                    class="block px-4 py-3 rounded {{ request()->is('mechanic/profile') ? 'bg-white/5 border-l-4 border-[#F7941D] text-white' : 'text-gray-500 hover:text-white hover:bg-white/5' }}">
                    My Profile
                </a>
            </nav>

            <div class="p-4 border-white/5 border-t">
                <p class="mb-1 text-gray-400 text-sm">{{ Auth::user()->name }}</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-500 hover:text-red-400 text-xs transition">
                        Log out
                    </button>
                </form>
            </div>

        </aside>

        <main class="flex-1 p-8 overflow-y-auto">
            @yield('content')
        </main>

    </div>

</body>

</html>
