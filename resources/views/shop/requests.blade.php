@extends('layouts.shop')

@section('content')
    <div class="page-header"
        style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <div>
            <div class="page-pretitle">Shop</div>
            <h1 class="page-title">Dispatch Requests</h1>
        </div>
    </div>

    {{-- Filter Tabs --}}
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
    <div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:20px; padding:4px 0; overflow-x:auto;">
        @foreach ($tabs as $st => $label)
            @php
                $isActive = request('status') === $st || (!request('status') && $st === '');
            @endphp
            <a href="{{ route('shop.requests', ['status' => $st]) }}"
                style="padding:6px 14px; border-radius:20px; font-size:13px; font-weight:600; text-decoration:none; white-space:nowrap; transition:all .15s;
            {{ $isActive ? 'background:#206bc4; color:#fff;' : 'background:#fff; color:#667382; border:1px solid #e6e7eb;' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div id="alert-container"></div>

    {{-- Request Cards --}}
    <div style="display:flex; flex-direction:column; gap:10px;">
        @forelse($data as $req)
            @php
                $motoristName = $req->owner_name ?? ($req->guest_name ?? ($req->motorist_name ?? 'Unknown Motorist'));
                $vehicle = $req->vehicle_make_model ?? ($req->motor_type ?? 'Not specified');
                $variantColor = $req->vehicle_variant_color ?? null;
                $plateNumber = $req->plate_temp_number ?? null;

                $statusCfg = [
                    'requested' => ['badge' => 'badge-danger', 'label' => 'Pending'],
                    'accepted' => ['badge' => 'badge-warning', 'label' => 'Accepted'],
                    'en_route' => ['badge' => 'badge-info', 'label' => 'En Route'],
                    'arrived' => ['badge' => 'badge-purple', 'label' => 'Arrived'],
                    'in_progress' => ['badge' => 'badge-orange', 'label' => 'In Progress'],
                    'completed' => ['badge' => 'badge-success', 'label' => 'Completed'],
                    'declined' => ['badge' => 'badge-secondary', 'label' => 'Declined'],
                ];
                $sc = $statusCfg[$req->status] ?? ['badge' => 'badge-secondary', 'label' => ucfirst($req->status)];
            @endphp

            <div class="t-card" id="request-{{ $req->id }}" style="padding:0; overflow:hidden;">

                {{-- Card Header --}}
                <div
                    style="padding:16px 20px; display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap;">
                    <div>
                        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:4px;">
                            <h3 style="font-size:15px; font-weight:700; color:#1d273b; margin:0;">
                                {{ $req->issue_type ?? 'Motorcycle Issue' }}</h3>
                            <span class="badge {{ $sc['badge'] }} status-badge"
                                style="font-size:11px;">{{ strtoupper(str_replace('_', ' ', $req->status)) }}</span>
                        </div>
                        <p style="font-size:12px; color:#667382; margin:0;">Requested
                            {{ \Carbon\Carbon::parse($req->created_at)->diffForHumans() }}</p>
                    </div>
                    @if (!empty($req->price))
                        <div style="text-align:right;">
                            <p style="font-size:11px; color:#667382; margin:0;">Estimated</p>
                            <p style="font-size:20px; font-weight:700; color:#206bc4; margin:2px 0 0;">
                                ₱{{ number_format($req->price, 2) }}</p>
                        </div>
                    @endif
                </div>

                {{-- Divider --}}
                <div style="height:1px; background:#f0f2f5; margin:0 20px;"></div>

                {{-- Details Grid --}}
                <div
                    style="padding:14px 20px; display:grid; grid-template-columns:repeat(auto-fill, minmax(180px,1fr)); gap:12px 20px;">
                    <div>
                        <p
                            style="font-size:11px; font-weight:600; color:#667382; text-transform:uppercase; margin:0 0 3px;">
                            Motorist</p>
                        <p style="font-size:13px; font-weight:600; color:#1d273b; margin:0;">{{ $motoristName }}</p>
                    </div>
                    <div>
                        <p
                            style="font-size:11px; font-weight:600; color:#667382; text-transform:uppercase; margin:0 0 3px;">
                            Contact</p>
                        <p style="font-size:13px; color:#1d273b; margin:0;">{{ $req->contact_number ?? 'Not provided' }}
                        </p>
                    </div>
                    <div>
                        <p
                            style="font-size:11px; font-weight:600; color:#667382; text-transform:uppercase; margin:0 0 3px;">
                            Vehicle</p>
                        <p style="font-size:13px; color:#1d273b; margin:0;">{{ $vehicle }}</p>
                    </div>
                    @if ($variantColor)
                        <div>
                            <p
                                style="font-size:11px; font-weight:600; color:#667382; text-transform:uppercase; margin:0 0 3px;">
                                Color</p>
                            <p style="font-size:13px; color:#1d273b; margin:0;">{{ $variantColor }}</p>
                        </div>
                    @endif
                    @if ($plateNumber)
                        <div>
                            <p
                                style="font-size:11px; font-weight:600; color:#667382; text-transform:uppercase; margin:0 0 3px;">
                                Plate No.</p>
                            <p style="font-size:13px; font-family:monospace; color:#1d273b; margin:0;">{{ $plateNumber }}
                            </p>
                        </div>
                    @endif
                    <div>
                        <p
                            style="font-size:11px; font-weight:600; color:#667382; text-transform:uppercase; margin:0 0 3px;">
                            Type</p>
                        <p style="font-size:13px; color:#1d273b; margin:0;">
                            @if (($req->request_type ?? '') === 'dispatch')
                                <i class="fas fa-motorcycle" style="color:#206bc4;"></i> Dispatch
                            @else
                                <i class="fas fa-store" style="color:#2fb344;"></i> Shop Visit
                            @endif
                        </p>
                    </div>
                    @if ($req->distance ?? null)
                        <div>
                            <p
                                style="font-size:11px; font-weight:600; color:#667382; text-transform:uppercase; margin:0 0 3px;">
                                Distance</p>
                            <p style="font-size:13px; color:#1d273b; margin:0;">{{ $req->distance }} km away</p>
                        </div>
                    @endif
                    @if (!empty($req->location))
                        <div style="grid-column:1/-1;">
                            <p
                                style="font-size:11px; font-weight:600; color:#667382; text-transform:uppercase; margin:0 0 3px;">
                                Location</p>
                            <p style="font-size:13px; color:#1d273b; margin:0;">{{ $req->location }}</p>
                        </div>
                    @endif
                    @if (!empty($req->description))
                        <div style="grid-column:1/-1;">
                            <p
                                style="font-size:11px; font-weight:600; color:#667382; text-transform:uppercase; margin:0 0 3px;">
                                Description</p>
                            <p style="font-size:13px; color:#1d273b; line-height:1.5; margin:0;">{{ $req->description }}
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div style="padding:12px 20px 16px; display:flex; gap:8px; flex-wrap:wrap; border-top:1px solid #f0f2f5;">
                    @if ($req->status === 'requested')
                        <button onclick="acceptRequest({{ $req->id }})" class="btn btn-success btn-sm"><i
                                class="fas fa-check"></i> Accept</button>
                        <button onclick="declineRequest({{ $req->id }})" class="btn btn-danger btn-sm"><i
                                class="fas fa-xmark"></i> Decline</button>
                    @elseif($req->status === 'accepted')
                        <button onclick="updateRequestStatus({{ $req->id }}, 'en_route')"
                            class="btn btn-primary btn-sm"><i class="fas fa-route"></i> Mechanic En Route</button>
                    @elseif($req->status === 'en_route')
                        <button onclick="updateRequestStatus({{ $req->id }}, 'arrived')"
                            class="btn btn-primary btn-sm"><i class="fas fa-map-pin"></i> Mark Arrived</button>
                    @elseif($req->status === 'arrived')
                        <button onclick="updateRequestStatus({{ $req->id }}, 'in_progress')"
                            class="btn btn-warning btn-sm"><i class="fas fa-wrench"></i> Start Repair</button>
                    @elseif($req->status === 'in_progress')
                        <button onclick="updateRequestStatus({{ $req->id }}, 'completed')"
                            class="btn btn-success btn-sm"><i class="fas fa-check-circle"></i> Mark Completed</button>
                    @elseif($req->status === 'completed')
                        <span class="badge badge-success" style="padding:6px 12px;">Completed</span>
                    @elseif($req->status === 'declined')
                        <span class="badge badge-secondary" style="padding:6px 12px;">Declined</span>
                    @endif
                    @if (!empty($req->latitude) && !empty($req->longitude))
                        <a href="https://www.google.com/maps?q={{ $req->latitude }},{{ $req->longitude }}" target="_blank"
                            rel="noopener" class="btn btn-secondary btn-sm">
                            <i class="fas fa-map-location-dot"></i> Open Map
                        </a>
                    @endif
                </div>
            </div>

        @empty
            <div class="t-card" style="padding:48px 24px; text-align:center;">
                <i class="fas fa-inbox" style="font-size:32px; color:#c8ccd0; margin-bottom:12px; display:block;"></i>
                <p style="color:#667382; margin:0; font-size:15px;">No dispatch requests found.</p>
                <p style="color:#a0a8b1; margin:6px 0 0; font-size:13px;">Requests will appear here when motorists submit
                    them.</p>
            </div>
        @endforelse
    </div>

    <div id="loading"
        style="display:none; position:fixed; top:20px; right:20px; z-index:9999; background:#206bc4; color:#fff; padding:8px 16px; border-radius:6px; font-size:13px; font-weight:600;">
        Loading...
    </div>

    <script>
        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        }

        function showAlert(message, type = 'success') {
            const con = document.getElementById('alert-container');
            const cls = type === 'success' ? 'alert-success' : 'alert-danger';
            const el = document.createElement('div');
            el.className = 'alert ' + cls;
            el.textContent = message;
            con.appendChild(el);
            setTimeout(() => el.remove(), 3500);
        }

        function showLoading(show) {
            document.getElementById('loading').style.display = show ? 'block' : 'none';
        }

        function updateCardStatus(id, status) {
            const card = document.getElementById('request-' + id);
            if (!card) return;
            const badges = {
                requested: 'badge-danger',
                accepted: 'badge-warning',
                en_route: 'badge-info',
                arrived: 'badge-purple',
                in_progress: 'badge-orange',
                completed: 'badge-success',
                declined: 'badge-secondary'
            };
            const badge = card.querySelector('.status-badge');
            if (badge) {
                badge.className = 'badge ' + (badges[status] || 'badge-secondary') + ' status-badge';
                badge.textContent = status.replace(/_/g, ' ').toUpperCase();
            }
        }

        async function acceptRequest(id) {
            showConfirmModal('Accept Request', 'Accept this dispatch request and notify the motorist?',
        async function() {
                showLoading(true);
                try {
                    const r = await fetch(`/shop/accept/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });
                    const d = await r.json();
                    if (d.success) {
                        showAlert('Request accepted successfully.', 'success');
                        updateCardStatus(id, 'accepted');
                    } else showAlert(d.message || 'Failed to accept.', 'error');
                } catch (e) {
                    showAlert('Network error.', 'error');
                } finally {
                    showLoading(false);
                }
            }, 'Accept', '#2fb344');
        }
        async function declineRequest(id) {
            showConfirmModal('Decline Request', 'Decline this request? The motorist will be notified.',
        async function() {
                showLoading(true);
                try {
                    const r = await fetch(`/shop/decline/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });
                    const d = await r.json();
                    if (d.success) {
                        showAlert('Request declined.', 'success');
                        updateCardStatus(id, 'declined');
                    } else showAlert(d.message || 'Failed to decline.', 'error');
                } catch (e) {
                    showAlert('Network error.', 'error');
                } finally {
                    showLoading(false);
                }
            }, 'Decline', '#d63939');
        }
        async function updateRequestStatus(id, status) {
            showLoading(true);
            try {
                const r = await fetch(`/shop/request/${id}/status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        status
                    })
                });
                const d = await r.json();
                if (d.success) {
                    showAlert('Status updated to ' + status.replace(/_/g, ' ') + '.', 'success');
                    updateCardStatus(id, status);
                } else showAlert(d.message || 'Update failed.', 'error');
            } catch (e) {
                showAlert('Network error.', 'error');
            } finally {
                showLoading(false);
            }
        }

        if (window.Echo && window.shopId) {
            window.Echo.private('shop.' + window.shopId).listen('.dispatch.new', function(req) {
                window.location.reload();
            });
        }
    </script>
@endsection
