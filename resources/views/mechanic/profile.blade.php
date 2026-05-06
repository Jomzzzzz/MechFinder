@extends('layouts.mechanic')

@section('content')

    <div class="mb-8">
        <h2 class="text-white text-3xl heading-font">My Profile</h2>
        <p class="mt-1 text-gray-400">Your details that motorists will see when you're dispatched</p>
    </div>

    @if (session('success'))
        <div class="bg-green-500/10 mb-6 p-4 border border-green-500/30 rounded-lg">
            <p class="text-green-400 text-sm">{{ session('success') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-500/10 mb-6 p-4 border border-red-500/30 rounded-lg">
            <ul class="space-y-1 text-red-400 text-sm">
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white/5 p-8 border border-white/10 rounded-2xl max-w-lg">

        {{-- Account info (read-only) --}}
        <div class="mb-6 pb-6 border-white/10 border-b">
            <p class="mb-4 font-bold text-gray-500 text-xs uppercase tracking-widest">Account</p>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-400 text-sm">Name</span>
                    <span class="font-medium text-white text-sm">{{ $mechanic->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400 text-sm">Email</span>
                    <span class="font-medium text-white text-sm">{{ $mechanic->email }}</span>
                </div>
            </div>
        </div>

        {{-- Editable profile fields --}}
        <p class="mb-4 font-bold text-gray-500 text-xs uppercase tracking-widest">Mechanic Info</p>

        <form method="POST" action="{{ route('mechanic.profile.update') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block mb-2 font-semibold text-gray-300 text-sm">
                    Plate Number
                    <span class="font-normal text-gray-500">(shown to motorists for verification)</span>
                </label>
                <input type="text" name="plate_number" value="{{ old('plate_number', $profile->plate_number ?? '') }}"
                    placeholder="e.g. ABC 1234"
                    class="bg-white/10 px-4 py-3 border border-white/10 focus:border-orange-500 rounded-lg focus:outline-none w-full font-mono text-white uppercase placeholder-gray-500">
            </div>

            <div>
                <label class="block mb-2 font-semibold text-gray-300 text-sm">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone', $profile->phone ?? '') }}"
                    placeholder="09XXXXXXXXX"
                    class="bg-white/10 px-4 py-3 border border-white/10 focus:border-orange-500 rounded-lg focus:outline-none w-full text-white placeholder-gray-500">
            </div>

            <div>
                <label class="block mb-2 font-semibold text-gray-300 text-sm">Status</label>
                <span
                    class="inline-block px-3 py-1 rounded-full text-xs font-bold
                @if (($profile->status ?? '') === 'dispatched') bg-yellow-500/20 text-yellow-400
                @elseif(($profile->status ?? '') === 'off_duty') bg-gray-500/20 text-gray-400
                @else bg-green-500/20 text-green-400 @endif
            ">
                    {{ strtoupper(str_replace('_', ' ', $profile->status ?? 'available')) }}
                </span>
            </div>

            <button type="submit"
                class="bg-orange-500 hover:bg-orange-600 py-3 rounded-lg w-full font-bold text-white transition">
                Save Profile
            </button>
        </form>

    </div>

@endsection
