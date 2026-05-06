@extends('layouts.mechanic')

@section('content')

    <div class="mb-8">
        <h2 class="text-white text-3xl heading-font">My Assigned Jobs</h2>
        <p class="mt-1 text-gray-400">Hello, {{ $mechanic->name }}</p>
    </div>

    {{-- Profile quick-info --}}
    @if ($profile)
        <div class="flex items-center gap-4 bg-white/5 mb-8 p-4 border border-white/10 rounded-xl">
            <div
                class="flex justify-center items-center bg-orange-500/20 rounded-full w-12 h-12 font-bold text-orange-400 text-xl">
                {{ strtoupper(substr($mechanic->name, 0, 1)) }}
            </div>
            <div>
                <p class="font-semibold text-white">{{ $mechanic->name }}</p>
                <p class="text-gray-400 text-sm">
                    🚗 Plate: <span class="font-mono text-orange-400">{{ $profile->plate_number ?? 'Not set' }}</span>
                    @if ($profile->phone)
                        &nbsp;·&nbsp; 📞 {{ $profile->phone }}
                    @endif
                </p>
            </div>
            <a href="{{ route('mechanic.profile') }}"
                class="ml-auto text-gray-400 hover:text-orange-400 text-xs underline">Edit profile</a>
        </div>
    @else
        <div class="bg-yellow-500/10 mb-8 p-4 border border-yellow-500/30 rounded-xl">
            <p class="text-yellow-400 text-sm">Your profile is incomplete.
                <a href="{{ route('mechanic.profile') }}" class="hover:text-yellow-300 underline">Add your plate number
                    →</a>
            </p>
        </div>
    @endif

    @if ($jobs->isEmpty())
        <div class="py-16 text-gray-500 text-center">
            <p class="mb-4 text-5xl">🔧</p>
            <p class="text-lg">No jobs assigned yet.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($jobs as $job)
                @php $req = $job->dispatchRequest; @endphp
                <div class="bg-white/5 p-6 border border-white/10 rounded-xl">
                    <div class="flex justify-between items-start gap-4">
                        <div>
                            <p class="font-semibold text-white text-lg">
                                {{ $req->shop->shop_name ?? '—' }}
                            </p>
                            <p class="mt-1 text-gray-400 text-sm">
                                Issue: <span class="text-white">{{ $req->issue_type ?? 'General repair' }}</span>
                            </p>
                            @if ($req->description)
                                <p class="mt-1 text-gray-500 text-sm">{{ $req->description }}</p>
                            @endif
                            @if ($req->motorist)
                                <p class="mt-2 text-gray-400 text-sm">
                                    Motorist: <span class="text-white">{{ $req->motorist->name }}</span>
                                </p>
                            @elseif($req->guest_name)
                                <p class="mt-2 text-gray-400 text-sm">
                                    Motorist (guest): <span class="text-white">{{ $req->guest_name }}</span>
                                </p>
                            @endif
                            <p class="mt-2 text-gray-600 text-xs">Dispatched {{ $job->created_at->diffForHumans() }}</p>
                        </div>
                        <span
                            class="shrink-0 px-3 py-1 rounded-full text-xs font-bold
                @if ($job->status === 'completed') bg-green-500/20 text-green-400
                @elseif($job->status === 'arrived') bg-blue-500/20 text-blue-400
                @elseif($job->status === 'en_route') bg-yellow-500/20 text-yellow-400
                @else bg-orange-500/20 text-orange-400 @endif
            ">
                            {{ strtoupper(str_replace('_', ' ', $job->status)) }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

@endsection
