<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MechFinder Admin</title>

    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">

    @vite('resources/css/app.css')
    @stack('head')

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

        {{-- Admin Sidebar --}}
        <aside class="flex flex-col bg-[#111113] px-4 py-8 border-[#1E1E21] border-r w-64">
            <div class="mb-10 px-2 text-[#F4B942] text-xl heading-font">MechFinder Admin</div>
            <nav class="flex flex-col gap-1">
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.dashboard') ? 'bg-[#F4B942] text-[#0A0A0B] font-semibold' : 'text-[#AAAAAA] hover:bg-[#1E1E21]' }}">
                    Dashboard
                </a>
                <a href="{{ route('admin.users') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.users') ? 'bg-[#F4B942] text-[#0A0A0B] font-semibold' : 'text-[#AAAAAA] hover:bg-[#1E1E21]' }}">
                    Users
                </a>
                <a href="{{ route('admin.shops') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.shops') ? 'bg-[#F4B942] text-[#0A0A0B] font-semibold' : 'text-[#AAAAAA] hover:bg-[#1E1E21]' }}">
                    Shops
                </a>
                <a href="{{ route('admin.requests') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.requests') ? 'bg-[#F4B942] text-[#0A0A0B] font-semibold' : 'text-[#AAAAAA] hover:bg-[#1E1E21]' }}">
                    Dispatch Requests
                </a>
            </nav>

            <div class="mt-auto pt-6 border-[#1E1E21] border-t">
                <p class="mb-3 px-2 text-[#666] text-xs">{{ Auth::user()->name }}</p>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="hover:bg-[#1E1E21] px-3 py-2 rounded-lg w-full text-[#AAAAAA] text-sm text-left">
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 p-8 overflow-hidden">
            <div class="h-full overflow-y-auto pr-2">
                @if (session('success'))
                    <div class="bg-green-900 mb-4 p-3 rounded text-green-200 text-sm">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="bg-red-900 mb-4 p-3 rounded text-red-200 text-sm">{{ session('error') }}</div>

                @endif
                @yield('content')
            </div>
        </main>

    </div>

    @stack('scripts')
</body>

</html>
