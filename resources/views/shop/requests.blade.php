@extends('layouts.shop')

@section('content')

<div class="mb-8">
    <h2 class="heading-font text-3xl mb-2">Dispatch Requests</h2>
    <p class="text-gray-400">Manage all incoming motorist requests for your shop.</p>
</div>

<!-- FILTER TABS -->
<div class="flex gap-3 mb-6 overflow-x-auto">
    @php
        $tabs = [
            '' => 'All',
            'requested' => 'Pending',
            'accepted' => 'Accepted',
            'en_route' => 'En Route',
            'arrived' => 'Arrived',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'declined' => 'Declined',
        ];
    @endphp

    @foreach($tabs as $status => $label)
        <a href="{{ route('shop.requests', ['status' => $status]) }}"
           class="px-4 py-2 rounded-lg border text-sm font-bold transition whitespace-nowrap
           {{ request('status') === $status || (!request('status') && $status === '')
                ? 'bg-[#F7941D] border-[#F7941D] text-black'
                : 'bg-white/5 border-white/10 text-gray-300 hover:bg-white/10' }}">
            {{ $label }}
        </a>
    @endforeach
</div>

<div id="alert-container"></div>

<!-- REQUESTS LIST -->
<div class="space-y-4">
    @forelse($data as $req)

        @php
            $motoristName = $req->owner_name
                ?? $req->guest_name
                ?? $req->motorist_name
                ?? 'Unknown Motorist';

            $vehicle = $req->vehicle_make_model
                ?? $req->motor_type
                ?? 'Not specified';

            $variantColor = $req->vehicle_variant_color ?? null;
            $plateNumber = $req->plate_temp_number ?? null;

            $statusClass = match($req->status) {
                'requested' => 'bg-red-500/20 text-red-400 border-red-500/30',
                'accepted' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
                'en_route' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                'arrived' => 'bg-purple-500/20 text-purple-400 border-purple-500/30',
                'in_progress' => 'bg-orange-500/20 text-orange-400 border-orange-500/30',
                'completed' => 'bg-green-500/20 text-green-400 border-green-500/30',
                'declined' => 'bg-gray-500/20 text-gray-400 border-gray-500/30',
                default => 'bg-white/10 text-gray-300 border-white/10',
            };
        @endphp

        <div class="bg-[#121214] p-6 rounded-xl border border-white/5 hover:border-white/10 transition"
             id="request-{{ $req->id }}">

            <!-- HEADER -->
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-4">
                <div>
                    <div class="flex flex-wrap items-center gap-3 mb-2">
                        <h3 class="text-xl font-black text-white">
                            {{ $req->issue_type ?? 'Motorcycle Issue' }}
                        </h3>

                        <span class="text-xs font-black px-3 py-1 rounded-full border {{ $statusClass }}">
                            {{ strtoupper(str_replace('_', ' ', $req->status)) }}
                        </span>
                    </div>

                    <p class="text-xs text-gray-400">
                        Requested {{ \Carbon\Carbon::parse($req->created_at)->diffForHumans() }}
                    </p>
                </div>

                @if(!empty($req->price))
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Estimated Price</p>
                        <p class="text-2xl font-black text-[#F7941D]">
                            ₱{{ number_format($req->price, 2) }}
                        </p>
                    </div>
                @endif
            </div>

            <!-- INFO -->
            <div class="grid md:grid-cols-2 gap-4 py-4 border-y border-white/5">

                <div>
                    <p class="text-xs text-gray-500 mb-1">Motorist Name</p>
                    <p class="text-sm font-semibold text-white">{{ $motoristName }}</p>
                </div>

                <div>
                    <p class="text-xs text-gray-500 mb-1">Contact Number</p>
                    <p class="text-sm font-semibold text-white">
                        {{ $req->contact_number ?? 'Not provided' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500 mb-1">Motorist UID</p>
                    <p class="text-sm font-semibold text-white">
                        {{ $req->motorist_uid ?? 'Guest / No UID' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500 mb-1">Request Type</p>
                    <p class="text-sm font-semibold">
                        @if(($req->request_type ?? '') === 'dispatch')
                            <span class="text-blue-400">🚗 Dispatch to Location</span>
                        @else
                            <span class="text-green-400">🏪 Visit Shop / Self-Service</span>
                        @endif
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500 mb-1">Vehicle / Motor</p>
                    <p class="text-sm font-semibold text-white">{{ $vehicle }}</p>
                </div>

                <div>
                    <p class="text-xs text-gray-500 mb-1">Variant / Color</p>
                    <p class="text-sm font-semibold text-white">
                        {{ $variantColor ?? 'Not specified' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500 mb-1">Plate / Temp Number</p>
                    <p class="text-sm font-semibold text-white">
                        {{ $plateNumber ?? 'Not specified' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500 mb-1">Distance</p>
                    <p class="text-sm font-semibold text-white">
                        {{ $req->distance ? $req->distance.' km away' : 'Not calculated' }}
                    </p>
                </div>

                <div class="md:col-span-2">
                    <p class="text-xs text-gray-500 mb-1">Location</p>
                    <p class="text-sm font-semibold text-white">
                        {{ $req->location ?? 'Not specified' }}
                    </p>
                </div>

                @if(!empty($req->description))
                    <div class="md:col-span-2">
                        <p class="text-xs text-gray-500 mb-1">Description</p>
                        <p class="text-sm text-gray-300 leading-relaxed">
                            {{ $req->description }}
                        </p>
                    </div>
                @endif

                @if(!empty($req->latitude) && !empty($req->longitude))
                    <div class="md:col-span-2 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Coordinates</p>
                            <p class="text-sm text-gray-300">
                                {{ $req->latitude }}, {{ $req->longitude }}
                            </p>
                        </div>

                        <a href="https://www.google.com/maps?q={{ $req->latitude }},{{ $req->longitude }}"
                           target="_blank"
                           class="inline-flex justify-center px-4 py-2 rounded-lg bg-white/5 border border-white/10 text-sm font-bold text-gray-300 hover:bg-white/10">
                            Open Map
                        </a>
                    </div>
                @endif

            </div>

            <!-- ACTIONS -->
            <div class="mt-5 flex flex-col md:flex-row gap-3">

                @if($req->status === 'requested')
                    <button onclick="acceptRequest({{ $req->id }})"
                        class="flex-1 bg-[#F7941D] text-black px-4 py-3 rounded-lg font-black hover:bg-[#FF6B35] transition">
                        ✓ Accept Request
                    </button>

                    <button onclick="declineRequest({{ $req->id }})"
                        class="flex-1 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg font-black hover:bg-red-500/10 transition">
                        ✕ Decline
                    </button>

                @elseif($req->status === 'accepted')
                    <button onclick="updateRequestStatus({{ $req->id }}, 'en_route')"
                        class="flex-1 bg-blue-600 text-white px-4 py-3 rounded-lg font-black hover:bg-blue-700 transition">
                        ➜ Mechanic En Route
                    </button>

                @elseif($req->status === 'en_route')
                    <button onclick="updateRequestStatus({{ $req->id }}, 'arrived')"
                        class="flex-1 bg-purple-600 text-white px-4 py-3 rounded-lg font-black hover:bg-purple-700 transition">
                        📍 Mark Arrived
                    </button>

                @elseif($req->status === 'arrived')
                    <button onclick="updateRequestStatus({{ $req->id }}, 'in_progress')"
                        class="flex-1 bg-orange-600 text-white px-4 py-3 rounded-lg font-black hover:bg-orange-700 transition">
                        🔧 Start Repair
                    </button>

                @elseif($req->status === 'in_progress')
                    <button onclick="updateRequestStatus({{ $req->id }}, 'completed')"
                        class="flex-1 bg-green-600 text-white px-4 py-3 rounded-lg font-black hover:bg-green-700 transition">
                        ✓ Mark Completed
                    </button>

                @elseif($req->status === 'completed')
                    <button disabled
                        class="flex-1 bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-lg font-black cursor-not-allowed">
                        ✓ Completed
                    </button>

                @elseif($req->status === 'declined')
                    <button disabled
                        class="flex-1 bg-gray-500/10 border border-gray-500/20 text-gray-400 px-4 py-3 rounded-lg font-black cursor-not-allowed">
                        ✕ Declined
                    </button>
                @endif

            </div>

        </div>

    @empty
        <div class="text-center py-16 bg-[#121214] rounded-xl border border-white/5">
            <p class="text-gray-400">No dispatch requests found.</p>
        </div>
    @endforelse
</div>

<div id="loading" class="hidden fixed top-4 right-4 bg-blue-600 text-white px-4 py-2 rounded-lg z-50">
    Loading...
</div>

<script>
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
}

function showAlert(message, type = 'success') {
    const container = document.getElementById('alert-container');

    const alertClass = type === 'success'
        ? 'bg-green-500/20 border-green-500/30 text-green-400'
        : 'bg-red-500/20 border-red-500/30 text-red-400';

    const alert = document.createElement('div');
    alert.className = `p-4 rounded-lg border ${alertClass} mb-4`;
    alert.textContent = message;

    container.appendChild(alert);

    setTimeout(() => alert.remove(), 3000);
}

function showLoading(show) {
    document.getElementById('loading').classList.toggle('hidden', !show);
}

async function acceptRequest(id) {
    if (!confirm('Accept this request?')) return;

    showLoading(true);

    try {
        const response = await fetch(`/shop/accept/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            showAlert('Request accepted successfully.');
            setTimeout(() => location.reload(), 800);
        } else {
            showAlert('Failed to accept request.', 'error');
        }
    } catch (error) {
        showAlert(error.message, 'error');
    } finally {
        showLoading(false);
    }
}

async function declineRequest(id) {
    if (!confirm('Decline this request?')) return;

    showLoading(true);

    try {
        const response = await fetch(`/shop/decline/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            showAlert('Request declined.');
            setTimeout(() => location.reload(), 800);
        } else {
            showAlert('Failed to decline request.', 'error');
        }
    } catch (error) {
        showAlert(error.message, 'error');
    } finally {
        showLoading(false);
    }
}

async function updateRequestStatus(id, status) {
    if (!confirm('Update request status?')) return;

    showLoading(true);

    try {
        const response = await fetch(`/shop/request/${id}/status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ status })
        });

        const data = await response.json();

        if (data.success) {
            showAlert('Status updated successfully.');
            setTimeout(() => location.reload(), 800);
        } else {
            showAlert(data.message || 'Failed to update status.', 'error');
        }
    } catch (error) {
        showAlert(error.message, 'error');
    } finally {
        showLoading(false);
    }
}
</script>

@endsection