@extends('layouts.shop')

@section('content')
    <div class="mb-8">
        <h2 class="mb-2 text-3xl heading-font">Dispatch Requests</h2>
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

        @foreach ($tabs as $status => $label)
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
                $motoristName = $req->owner_name ?? ($req->guest_name ?? ($req->motorist_name ?? 'Unknown Motorist'));

                $vehicle = $req->vehicle_make_model ?? ($req->motor_type ?? 'Not specified');

                $variantColor = $req->vehicle_variant_color ?? null;
                $plateNumber = $req->plate_temp_number ?? null;

                $statusClass = match ($req->status) {
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

            <div class="bg-[#121214] p-6 border border-white/5 hover:border-white/10 rounded-xl transition"
                id="request-{{ $req->id }}">

                <!-- HEADER -->
                <div class="flex md:flex-row flex-col md:justify-between md:items-start gap-4 mb-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-3 mb-2">
                            <h3 class="font-black text-white text-xl">
                                {{ $req->issue_type ?? 'Motorcycle Issue' }}
                            </h3>

                            <span class="status-badge text-xs font-black px-3 py-1 rounded-full border {{ $statusClass }}">
                                {{ strtoupper(str_replace('_', ' ', $req->status)) }}
                            </span>
                        </div>

                        <p class="text-gray-400 text-xs">
                            Requested {{ \Carbon\Carbon::parse($req->created_at)->diffForHumans() }}
                        </p>
                    </div>

                    @if (!empty($req->price))
                        <div class="text-right">
                            <p class="text-gray-500 text-xs">Estimated Price</p>
                            <p class="font-black text-[#F7941D] text-2xl">
                                ₱{{ number_format($req->price, 2) }}
                            </p>
                        </div>
                    @endif
                </div>

                <!-- INFO -->
                <div class="gap-4 grid md:grid-cols-2 py-4 border-white/5 border-y">

                    <div>
                        <p class="mb-1 text-gray-500 text-xs">Motorist Name</p>
                        <p class="font-semibold text-white text-sm">{{ $motoristName }}</p>
                    </div>

                    <div>
                        <p class="mb-1 text-gray-500 text-xs">Contact Number</p>
                        <p class="font-semibold text-white text-sm">
                            {{ $req->contact_number ?? 'Not provided' }}
                        </p>
                    </div>

                    <div>
                        <p class="mb-1 text-gray-500 text-xs">Motorist UID</p>
                        <p class="font-semibold text-white text-sm">
                            {{ $req->motorist_uid ?? 'Guest / No UID' }}
                        </p>
                    </div>

                    <div>
                        <p class="mb-1 text-gray-500 text-xs">Request Type</p>
                        <p class="font-semibold text-sm">
                            @if (($req->request_type ?? '') === 'dispatch')
                                <span class="text-blue-400">🚗 Dispatch to Location</span>
                            @else
                                <span class="text-green-400">🏪 Visit Shop / Self-Service</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <p class="mb-1 text-gray-500 text-xs">Vehicle / Motor</p>
                        <p class="font-semibold text-white text-sm">{{ $vehicle }}</p>
                    </div>

                    <div>
                        <p class="mb-1 text-gray-500 text-xs">Variant / Color</p>
                        <p class="font-semibold text-white text-sm">
                            {{ $variantColor ?? 'Not specified' }}
                        </p>
                    </div>

                    <div>
                        <p class="mb-1 text-gray-500 text-xs">Plate / Temp Number</p>
                        <p class="font-semibold text-white text-sm">
                            {{ $plateNumber ?? 'Not specified' }}
                        </p>
                    </div>

                    <div>
                        <p class="mb-1 text-gray-500 text-xs">Distance</p>
                        <p class="font-semibold text-white text-sm">
                            {{ $req->distance ? $req->distance . ' km away' : 'Not calculated' }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="mb-1 text-gray-500 text-xs">Location</p>
                        <p class="font-semibold text-white text-sm">
                            {{ $req->location ?? 'Not specified' }}
                        </p>
                    </div>

                    @if (!empty($req->description))
                        <div class="md:col-span-2">
                            <p class="mb-1 text-gray-500 text-xs">Description</p>
                            <p class="text-gray-300 text-sm leading-relaxed">
                                {{ $req->description }}
                            </p>
                        </div>
                    @endif

                    @if (!empty($req->latitude) && !empty($req->longitude))
                        <div class="flex md:flex-row flex-col md:justify-between md:items-center gap-3 md:col-span-2">
                            <div>
                                <p class="mb-1 text-gray-500 text-xs">Coordinates</p>
                                <p class="text-gray-300 text-sm">
                                    {{ $req->latitude }}, {{ $req->longitude }}
                                </p>
                            </div>

                            <a href="https://www.google.com/maps?q={{ $req->latitude }},{{ $req->longitude }}"
                                target="_blank"
                                class="inline-flex justify-center bg-white/5 hover:bg-white/10 px-4 py-2 border border-white/10 rounded-lg font-bold text-gray-300 text-sm">
                                Open Map
                            </a>
                        </div>
                    @endif

                </div>

                <!-- ACTIONS -->
                <div class="flex md:flex-row flex-col gap-3 mt-5">

                    @if ($req->status === 'requested')
                        <button onclick="acceptRequest({{ $req->id }})"
                            class="flex-1 bg-[#F7941D] hover:bg-[#FF6B35] px-4 py-3 rounded-lg font-black text-black transition">
                            ✓ Accept Request
                        </button>

                        <button onclick="declineRequest({{ $req->id }})"
                            class="flex-1 hover:bg-red-500/10 px-4 py-3 border border-red-500/30 rounded-lg font-black text-red-400 transition">
                            ✕ Decline
                        </button>
                    @elseif($req->status === 'accepted')
                        <button onclick="updateRequestStatus({{ $req->id }}, 'en_route')"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 px-4 py-3 rounded-lg font-black text-white transition">
                            ➜ Mechanic En Route
                        </button>
                    @elseif($req->status === 'en_route')
                        <button onclick="updateRequestStatus({{ $req->id }}, 'arrived')"
                            class="flex-1 bg-purple-600 hover:bg-purple-700 px-4 py-3 rounded-lg font-black text-white transition">
                            📍 Mark Arrived
                        </button>
                    @elseif($req->status === 'arrived')
                        <button onclick="updateRequestStatus({{ $req->id }}, 'in_progress')"
                            class="flex-1 bg-orange-600 hover:bg-orange-700 px-4 py-3 rounded-lg font-black text-white transition">
                            🔧 Start Repair
                        </button>
                    @elseif($req->status === 'in_progress')
                        <button onclick="updateRequestStatus({{ $req->id }}, 'completed')"
                            class="flex-1 bg-green-600 hover:bg-green-700 px-4 py-3 rounded-lg font-black text-white transition">
                            ✓ Mark Completed
                        </button>
                    @elseif($req->status === 'completed')
                        <button disabled
                            class="flex-1 bg-green-500/10 px-4 py-3 border border-green-500/20 rounded-lg font-black text-green-400 cursor-not-allowed">
                            ✓ Completed
                        </button>
                    @elseif($req->status === 'declined')
                        <button disabled
                            class="flex-1 bg-gray-500/10 px-4 py-3 border border-gray-500/20 rounded-lg font-black text-gray-400 cursor-not-allowed">
                            ✕ Declined
                        </button>
                    @endif

                </div>

            </div>

        @empty
            <div class="bg-[#121214] py-16 border border-white/5 rounded-xl text-center">
                <p class="text-gray-400">No dispatch requests found.</p>
            </div>
        @endforelse
    </div>

    <div id="loading" class="hidden top-4 right-4 z-50 fixed bg-blue-600 px-4 py-2 rounded-lg text-white">
        Loading...
    </div>

    <script>
        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        }

        function showAlert(message, type = 'success') {
            const container = document.getElementById('alert-container');

            const alertClass = type === 'success' ?
                'bg-green-500/20 border-green-500/30 text-green-400' :
                'bg-red-500/20 border-red-500/30 text-red-400';

            const alert = document.createElement('div');
            alert.className = `p-4 rounded-lg border ${alertClass} mb-4`;
            alert.textContent = message;

            container.appendChild(alert);

            setTimeout(() => alert.remove(), 3000);
        }

        function showLoading(show) {
            document.getElementById('loading').classList.toggle('hidden', !show);
        }

        function updateCardStatus(id, status) {
            const card = document.getElementById('request-' + id);
            if (!card) return;

            const statusClasses = {
                requested: 'bg-red-500/20 text-red-400 border-red-500/30',
                accepted: 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
                en_route: 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                arrived: 'bg-purple-500/20 text-purple-400 border-purple-500/30',
                in_progress: 'bg-orange-500/20 text-orange-400 border-orange-500/30',
                completed: 'bg-green-500/20 text-green-400 border-green-500/30',
                declined: 'bg-gray-500/20 text-gray-400 border-gray-500/30',
            };

            const badge = card.querySelector('.status-badge');
            if (badge) {
                badge.className = 'status-badge text-xs font-black px-3 py-1 rounded-full border ' + (statusClasses[
                    status] || 'bg-white/10 text-gray-300 border-white/10');
                badge.textContent = status.replace(/_/g, ' ').toUpperCase();
            }
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

                const data = await response.json();
                if (data.success) {
                    showAlert('Request accepted successfully.');
                    updateCardStatus(id, 'accepted');
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

                const data = await response.json();
                if (data.success) {
                    showAlert('Request declined.');
                    updateCardStatus(id, 'declined');
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
                    body: JSON.stringify({
                        status
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showAlert('Status updated successfully.');
                    updateCardStatus(id, status);
                } else {
                    showAlert(data.message || 'Failed to update status.', 'error');
                }
            } catch (error) {
                showAlert(error.message, 'error');
            } finally {
                showLoading(false);
            }
        }

        // Pusher real-time: listen for new dispatch requests
        document.addEventListener('DOMContentLoaded', () => {
            if (!window.Echo || !window.shopId) return;

            window.Echo.private('shop.' + window.shopId)
                .listen('.dispatch.new', (req) => {
                    // Toast notification
                    showAlert('🔔 New dispatch request from ' + (req.owner_name || 'motorist') + ' — ' + (req
                        .issue_type || 'Issue'), 'success');

                    // Prepend new card to the requests list
                    const list = document.querySelector('.space-y-4');
                    if (!list) return;

                    const card = document.createElement('div');
                    card.id = 'request-' + req.id;
                    card.className =
                        'bg-[#121214] p-6 rounded-xl border border-yellow-500/40 hover:border-white/10 transition';
                    card.innerHTML = `
                <div class="flex md:flex-row flex-col md:justify-between md:items-start gap-4 mb-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-3 mb-2">
                            <h3 class="font-black text-white text-xl">${req.issue_type || 'Motorcycle Issue'}</h3>
                            <span class="bg-red-500/20 px-3 py-1 border border-red-500/30 rounded-full font-black text-red-400 text-xs status-badge">REQUESTED</span>
                        </div>
                        <p class="text-gray-400 text-xs">Just now</p>
                    </div>
                </div>
                <div class="gap-4 grid md:grid-cols-2 py-4 border-white/5 border-y text-gray-300 text-sm">
                    <div><p class="mb-1 text-gray-500 text-xs uppercase">Motorist</p><p class="font-bold text-white">${req.owner_name || '—'}</p></div>
                    <div><p class="mb-1 text-gray-500 text-xs uppercase">Contact</p><p>${req.contact_number || '—'}</p></div>
                    <div><p class="mb-1 text-gray-500 text-xs uppercase">Vehicle</p><p>${req.vehicle_make_model || '—'}${req.vehicle_variant_color ? ' · ' + req.vehicle_variant_color : ''}</p></div>
                    <div><p class="mb-1 text-gray-500 text-xs uppercase">Plate</p><p>${req.plate_temp_number || '—'}</p></div>
                    ${req.description ? `<div class="md:col-span-2"><p class="mb-1 text-gray-500 text-xs uppercase">Description</p><p>${req.description}</p></div>` : ''}
                    ${req.location ? `<div class="md:col-span-2"><p class="mb-1 text-gray-500 text-xs uppercase">Location</p><p>${req.location}</p></div>` : ''}
                </div>
                <div class="flex gap-3 mt-4">
                    <button onclick="acceptRequest(${req.id})" class="bg-[#F7941D] hover:opacity-90 px-5 py-2 rounded-lg font-black text-black text-sm transition">Accept</button>
                    <button onclick="declineRequest(${req.id})" class="bg-white/5 hover:bg-white/10 px-5 py-2 border border-white/10 rounded-lg font-bold text-gray-300 text-sm transition">Decline</button>
                </div>
            `;

                    list.insertBefore(card, list.firstChild);
                });
        });
    </script>
@endsection
