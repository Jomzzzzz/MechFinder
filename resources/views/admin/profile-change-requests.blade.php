@extends('layouts.admin')

@section('content')
    <div class="mb-2 text-[#F4B942] text-2xl heading-font">Profile Change Requests</div>
    <div class="mb-6 text-[#AAA] text-sm">Motorists who have requested to update their locked profile information.</div>

    @if (session('success'))
        <div class="mb-4 rounded-xl bg-green-900/30 border border-green-700 px-4 py-3 text-green-300 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-xl bg-red-900/30 border border-red-700 px-4 py-3 text-red-400 text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if ($requests->isEmpty())
        <div class="bg-[#111113] border border-[#1E1E21] rounded-2xl px-6 py-12 text-center text-[#AAA] text-sm">
            No pending profile change requests.
        </div>
    @else
        <div class="flex flex-col gap-4">
            @foreach ($requests as $req)
                <div class="bg-[#111113] border border-[#1E1E21] rounded-2xl p-5">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[#F4B942] font-semibold text-base">{{ $req->owner_name ?? '—' }}</span>
                                @if ($req->account_name)
                                    <span class="text-[#AAA] text-xs">(Account:
                                        {{ $req->account_name }}{{ $req->account_email ? ' · ' . $req->account_email : '' }})</span>
                                @endif
                            </div>
                            <div class="text-[#AAA] text-xs mb-3">Requested
                                {{ \Carbon\Carbon::parse($req->updated_at)->diffForHumans() }}</div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs mb-3">
                                <div><span class="text-[#666]">Contact:</span> <span
                                        class="text-[#EEE]">{{ $req->contact_number ?? '—' }}</span></div>
                                <div><span class="text-[#666]">Vehicle:</span> <span
                                        class="text-[#EEE]">{{ $req->vehicle_make_model ?? '—' }}{{ $req->vehicle_variant_color ? ' · ' . $req->vehicle_variant_color : '' }}</span>
                                </div>
                                <div><span class="text-[#666]">Plate:</span> <span
                                        class="text-[#EEE]">{{ $req->plate_temp_number ?? '—' }}</span></div>
                            </div>

                            <div
                                class="bg-[#0A0B0D] border border-[#1E1E21] rounded-lg px-3 py-2 text-xs text-[#CCC] italic">
                                <span class="text-[#666] not-italic">Reason: </span>{{ $req->change_request_reason }}
                            </div>
                        </div>

                        <div class="flex-shrink-0">
                            <form method="POST" action="{{ route('admin.profile-change-requests.unlock', $req->id) }}">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center gap-2 rounded-lg bg-[#F4B942] px-4 py-2 text-sm font-semibold text-[#0A0A0B] hover:bg-[#e6b12a] transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" />
                                        <path d="M7 11V7a5 5 0 0 1 9.9-1" />
                                    </svg>
                                    Unlock Profile
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
