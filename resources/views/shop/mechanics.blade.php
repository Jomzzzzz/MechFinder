@extends('layouts.shop')

@section('content')

    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-white text-3xl heading-font">Mechanics</h2>
            <p class="mt-1 text-gray-400">Manage your shop's mechanics</p>
        </div>
        <button onclick="document.getElementById('add-mechanic-modal').classList.remove('hidden')"
            class="bg-orange-500 hover:bg-orange-600 px-5 py-2.5 rounded-lg font-semibold text-white text-sm transition">
            + Add Mechanic
        </button>
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

    {{-- Mechanics table --}}
    @if ($mechanics->isEmpty())
        <div class="py-16 text-gray-500 text-center">
            <p class="mb-4 text-5xl">🔧</p>
            <p class="text-lg">No mechanics added yet.</p>
            <p class="mt-2 text-sm">Click <strong class="text-orange-400">+ Add Mechanic</strong> to get started.</p>
        </div>
    @else
        <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-white/10 border-b text-gray-400 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 text-left">Name</th>
                        <th class="px-6 py-4 text-left">Email</th>
                        <th class="px-6 py-4 text-left">Plate Number</th>
                        <th class="px-6 py-4 text-left">Phone</th>
                        <th class="px-6 py-4 text-left">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach ($mechanics as $profile)
                        <tr class="hover:bg-white/5 transition">
                            <td class="px-6 py-4 font-medium text-white">{{ $profile->user->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-400">{{ $profile->user->email ?? '—' }}</td>
                            <td class="px-6 py-4 font-mono text-orange-400">{{ $profile->plate_number ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-400">{{ $profile->phone ?? '—' }}</td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 rounded-full text-xs font-bold
                        @if ($profile->status === 'dispatched') bg-yellow-500/20 text-yellow-400
                        @elseif($profile->status === 'off_duty') bg-gray-500/20 text-gray-400
                        @else bg-green-500/20 text-green-400 @endif
                    ">
                                    {{ strtoupper(str_replace('_', ' ', $profile->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('shop.mechanics.delete', $profile->id) }}"
                                    onsubmit="return confirm('Remove {{ addslashes($profile->user->name ?? 'this mechanic') }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="font-semibold text-red-400 hover:text-red-300 text-xs transition">
                                        Remove
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Add Mechanic Modal --}}
    <div id="add-mechanic-modal" class="hidden z-50 fixed inset-0 flex justify-center items-center bg-black/70 px-4">
        <div class="bg-[#18181b] p-8 border border-white/10 rounded-2xl w-full max-w-md">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-white text-xl heading-font">Add Mechanic</h3>
                <button onclick="document.getElementById('add-mechanic-modal').classList.add('hidden')"
                    class="text-gray-500 hover:text-white text-2xl leading-none">&times;</button>
            </div>

            <form method="POST" action="{{ route('shop.mechanics.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block mb-2 font-semibold text-gray-300 text-sm">Full Name</label>
                    <input type="text" name="name" required placeholder="Juan dela Cruz"
                        class="bg-white/10 px-4 py-3 border border-white/10 focus:border-orange-500 rounded-lg focus:outline-none w-full text-white placeholder-gray-500">
                </div>

                <div>
                    <label class="block mb-2 font-semibold text-gray-300 text-sm">Email Address</label>
                    <input type="email" name="email" required placeholder="mechanic@example.com"
                        class="bg-white/10 px-4 py-3 border border-white/10 focus:border-orange-500 rounded-lg focus:outline-none w-full text-white placeholder-gray-500">
                </div>

                <div>
                    <label class="block mb-2 font-semibold text-gray-300 text-sm">Temporary Password</label>
                    <input type="password" name="password" required placeholder="Minimum 6 characters"
                        class="bg-white/10 px-4 py-3 border border-white/10 focus:border-orange-500 rounded-lg focus:outline-none w-full text-white placeholder-gray-500">
                </div>

                <div>
                    <label class="block mb-2 font-semibold text-gray-300 text-sm">Plate Number <span
                            class="font-normal text-gray-500">(optional)</span></label>
                    <input type="text" name="plate_number" placeholder="e.g. ABC 1234"
                        class="bg-white/10 px-4 py-3 border border-white/10 focus:border-orange-500 rounded-lg focus:outline-none w-full font-mono text-white uppercase placeholder-gray-500">
                </div>

                <div>
                    <label class="block mb-2 font-semibold text-gray-300 text-sm">Phone <span
                            class="font-normal text-gray-500">(optional)</span></label>
                    <input type="text" name="phone" placeholder="09XXXXXXXXX"
                        class="bg-white/10 px-4 py-3 border border-white/10 focus:border-orange-500 rounded-lg focus:outline-none w-full text-white placeholder-gray-500">
                </div>

                <button type="submit"
                    class="bg-orange-500 hover:bg-orange-600 mt-2 py-3 rounded-lg w-full font-bold text-white transition">
                    Add Mechanic
                </button>
            </form>
        </div>
    </div>

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('add-mechanic-modal').classList.remove('hidden');
            });
        </script>
    @endif

@endsection
