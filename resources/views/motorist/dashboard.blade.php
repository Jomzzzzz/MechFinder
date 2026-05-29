@extends('layouts.motorist')

@section('content')
<div class="flex flex-col gap-6 py-2">

    <!-- Welcome -->
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-widest mb-0.5">Motorist Dashboard</p>
            <h1 class="text-xl font-black text-white">Welcome, {{ Auth::user()->name }} 👋</h1>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-xs text-gray-400 hover:text-red-400 transition">Logout</button>
        </form>
    </div>

    <!-- Quick Actions -->
    <div>
        <h2 class="text-sm font-bold text-gray-300 mb-3 uppercase tracking-widest">Quick Actions</h2>
        <div class="space-y-3">
            <a href="{{ route('motorist.index') }}"
                class="flex items-center gap-4 glass rounded-2xl p-4 hover:border-[#F7941D] transition">
                <div class="text-2xl">🏍</div>
                <div>
                    <h3 class="font-bold text-sm">Find Shops</h3>
                    <p class="text-xs text-gray-400">Browse nearby repair shops</p>
                </div>
            </a>
            <a href="{{ route('motorist.index') }}"
                class="flex items-center gap-4 glass rounded-2xl p-4 hover:border-[#F7941D] transition">
                <div class="text-2xl">🚨</div>
                <div>
                    <h3 class="font-bold text-sm">Request Dispatch</h3>
                    <p class="text-xs text-gray-400">Get emergency assistance now</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Account Info -->
    <div>
        <h2 class="text-sm font-bold text-gray-300 mb-3 uppercase tracking-widest">Account</h2>
        <div class="glass rounded-2xl p-5 space-y-3">
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Name</p>
                <p class="font-bold text-sm">{{ Auth::user()->name }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Email</p>
                <p class="font-bold text-sm">{{ Auth::user()->email }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Role</p>
                <p class="font-bold text-sm capitalize">{{ Auth::user()->role }}</p>
            </div>
        </div>
    </div>

    <!-- Features -->
    <div>
        <h2 class="text-sm font-bold text-gray-300 mb-3 uppercase tracking-widest">Features</h2>
        <div class="glass rounded-2xl p-5 space-y-4">
            @foreach([
                ['📍', 'Find Nearby Shops', 'Locate repair shops closest to your location'],
                ['💬', 'Live Chat', 'Communicate with shop owners in real-time'],
                ['⭐', 'Leave Reviews', 'Share your experience with other motorists'],
            ] as [$icon, $title, $desc])
            <div class="flex items-start gap-3">
                <div class="text-xl">{{ $icon }}</div>
                <div>
                    <h3 class="font-bold text-sm mb-0.5">{{ $title }}</h3>
                    <p class="text-xs text-gray-400">{{ $desc }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
