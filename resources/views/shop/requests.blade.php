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

                {{-- Mechanic Row (active statuses only) --}}
                @if (in_array($req->status, ['accepted', 'en_route', 'arrived']))
                    <div id="mrow-{{ $req->id }}"
                        style="padding:8px 20px 4px;display:flex;align-items:center;gap:8px;">
                        @if (!empty($req->assigned_mechanic_name))
                            <i class="fas fa-user-gear" style="color:#667382;font-size:12px;flex-shrink:0;"></i>
                            <span
                                style="font-size:12px;color:#374151;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $req->assigned_mechanic_name }}</span>
                            <button class="btn btn-secondary btn-sm" style="padding:2px 8px;font-size:11px;flex-shrink:0;"
                                onclick="openMechanicPicker({{ $req->id }})">Change</button>
                        @endif
                    </div>
                @endif

                {{-- Actions --}}
                <div style="padding:12px 20px 16px; display:flex; gap:8px; flex-wrap:wrap; border-top:1px solid #f0f2f5;">
                    <span id="req-actions-{{ $req->id }}">
                        @if ($req->status === 'requested')
                            <button onclick="acceptRequest({{ $req->id }})" class="btn btn-success btn-sm"><i
                                    class="fas fa-check"></i> Accept</button>
                            <button onclick="declineRequest({{ $req->id }})" class="btn btn-danger btn-sm"><i
                                    class="fas fa-xmark"></i> Decline</button>
                        @elseif($req->status === 'accepted')
                            @if (!empty($req->assigned_mechanic_name))
                                <button onclick="updateRequestStatus({{ $req->id }}, 'en_route')"
                                    class="btn btn-primary btn-sm"><i class="fas fa-route"></i> Mechanic En Route</button>
                            @else
                                <button onclick="openMechanicPicker({{ $req->id }})" class="btn btn-warning btn-sm"><i
                                        class="fas fa-user-plus" style="margin-right:4px;"></i>Assign Mechanic</button>
                            @endif
                        @elseif($req->status === 'en_route')
                            <button onclick="updateRequestStatus({{ $req->id }}, 'arrived')"
                                class="btn btn-primary btn-sm"><i class="fas fa-map-pin"></i> Mark Arrived</button>
                        @elseif($req->status === 'arrived')
                            <button onclick="updateRequestStatus({{ $req->id }}, 'completed')"
                                class="btn btn-success btn-sm"><i class="fas fa-check-circle"></i> Mark Complete</button>
                        @elseif($req->status === 'completed')
                            <span class="badge badge-success" style="padding:6px 12px;">Completed</span>
                        @elseif($req->status === 'declined')
                            <span class="badge badge-secondary" style="padding:6px 12px;">Declined</span>
                        @endif
                    </span>
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

        function escHtml(s) {
            return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g,
                '&quot;');
        }

        // --- Badge + action button config ---
        var BADGES = {
            requested: 'badge-danger',
            accepted: 'badge-warning',
            en_route: 'badge-info',
            arrived: 'badge-purple',
            completed: 'badge-success',
            declined: 'badge-secondary'
        };
        var ACTIONS = {
            accepted: '<button onclick="openMechanicPicker(ID)" class="btn btn-warning btn-sm"><i class="fas fa-user-plus" style="margin-right:4px;"></i>Assign Mechanic</button>',
            en_route: '<button onclick="updateRequestStatus(ID, \'arrived\')" class="btn btn-primary btn-sm"><i class="fas fa-map-pin"></i> Mark Arrived</button>',
            arrived: '<button onclick="updateRequestStatus(ID, \'completed\')" class="btn btn-success btn-sm"><i class="fas fa-check-circle"></i> Mark Complete</button>',
            completed: '<span class="badge badge-success" style="padding:6px 12px;">Completed</span>',
            declined: '<span class="badge badge-secondary" style="padding:6px 12px;">Declined</span>',
        };

        function updateCardStatus(id, status) {
            var card = document.getElementById('request-' + id);
            if (!card) return;
            // Update badge
            var badge = card.querySelector('.status-badge');
            if (badge) {
                badge.className = 'badge ' + (BADGES[status] || 'badge-secondary') + ' status-badge';
                badge.textContent = status.replace(/_/g, ' ').toUpperCase();
            }
            // Rebuild action buttons
            var actionsSpan = document.getElementById('req-actions-' + id);
            if (actionsSpan && ACTIONS[status]) {
                actionsSpan.innerHTML = ACTIONS[status].replace(/ID/g, id);
            }
            // Show/hide mechanic row
            var mrow = document.getElementById('mrow-' + id);
            if (mrow) {
                mrow.style.display = ['accepted', 'en_route', 'arrived'].includes(status) ? 'flex' : 'none';
            }
        }

        // --- Mechanic picker state ---
        var _pickerRequestId = null;
        var _pendingEnRoute = null;

        function openMechanicPicker(requestId) {
            _pickerRequestId = requestId;
            document.getElementById('mechPickerModal').style.display = 'flex';
            loadMechanicsForPicker();
        }

        function closeMechanicPicker() {
            document.getElementById('mechPickerModal').style.display = 'none';
            _pickerRequestId = null;
            _pendingEnRoute = null;
        }

        async function loadMechanicsForPicker() {
            var list = document.getElementById('mechPickerList');
            list.innerHTML =
                '<div style="text-align:center;padding:20px;color:#667382;font-size:13px;"><i class="fas fa-spinner fa-spin" style="margin-right:6px;"></i>Loading…</div>';
            try {
                var r = await fetch('/shop/mechanics-list', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    }
                });
                var d = await r.json();
                var mechanics = Array.isArray(d) ? d : (d.mechanics || []);
                if (!mechanics.length) {
                    list.innerHTML =
                        '<div style="text-align:center;padding:20px;color:#667382;font-size:13px;">No mechanics found. <a href="/shop/mechanics" style="color:#f76707;">Add one →</a></div>';
                    return;
                }
                var html = '';
                mechanics.forEach(function(m) {
                    var statusColor = m.status === 'available' ? '#2fb344' : m.status === 'dispatched' ?
                        '#f76707' : '#667382';
                    html += '<div onclick="assignMechanic(' + m.user_id + ', \'' + escHtml(m.name) +
                        '\')" style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;cursor:pointer;transition:background .15s;" onmouseover="this.style.background=\'#f0f4fa\'" onmouseout="this.style.background=\'\'">' +
                        '<div style="width:34px;height:34px;border-radius:50%;background:#e8edf4;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-user-gear" style="font-size:14px;color:#206bc4;"></i></div>' +
                        '<div style="flex:1;min-width:0;">' +
                        '<div style="font-size:13px;font-weight:600;color:#1d273b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
                        escHtml(m.name) + '</div>' +
                        '<div style="font-size:11px;color:#667382;">' + escHtml(m.phone || '') +
                        ' &nbsp;·&nbsp; <span style="color:' + statusColor + ';font-weight:600;">' + escHtml(m
                            .status) + '</span></div>' +
                        '</div></div>';
                });
                list.innerHTML = html;
            } catch (e) {
                list.innerHTML =
                    '<div style="text-align:center;padding:20px;color:#d63939;font-size:13px;">Failed to load mechanics.</div>';
            }
        }

        async function assignMechanic(userId, name) {
            var reqId = _pickerRequestId;
            if (!reqId) return;
            try {
                var r = await fetch('/shop/request/' + reqId + '/dispatch', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        mechanic_id: userId
                    })
                });
                var d = await r.json();
                if (d.success) {
                    var pending = _pendingEnRoute === reqId ? reqId : null;
                    closeMechanicPicker();
                    var mrow = document.getElementById('mrow-' + reqId);
                    if (mrow) {
                        mrow.innerHTML =
                            '<i class="fas fa-user-gear" style="color:#667382;font-size:12px;flex-shrink:0;"></i>' +
                            '<span style="font-size:12px;color:#374151;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
                            escHtml(d.mechanic_name || name) + '</span>' +
                            '<button class="btn btn-secondary btn-sm" style="padding:2px 8px;font-size:11px;flex-shrink:0;" onclick="openMechanicPicker(' +
                            reqId + ')">Change</button>';
                    }
                    showAlert('Mechanic assigned: ' + (d.mechanic_name || name), 'success');
                    // If card was showing "Assign Mechanic" (accepted, no mechanic), swap to En Route
                    if (!pending) {
                        var actSpan = document.getElementById('req-actions-' + reqId);
                        if (actSpan && actSpan.querySelector('.btn-warning')) {
                            actSpan.innerHTML = '<button onclick="updateRequestStatus(' + reqId +
                                ', \'en_route\'" class="btn btn-primary btn-sm"><i class="fas fa-route"></i> Mechanic En Route</button>';
                        }
                    }
                    // Auto-proceed with en_route if triggered by the guard
                    if (pending) updateRequestStatus(pending, 'en_route');
                } else {
                    showAlert(d.message || 'Assignment failed.', 'error');
                }
            } catch (e) {
                showAlert('Network error.', 'error');
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
            // Guard: must assign a mechanic before going en_route
            if (status === 'en_route') {
                var mrow = document.getElementById('mrow-' + id);
                var hasMechanic = mrow && !!mrow.querySelector('span');
                if (!hasMechanic) {
                    showAlert('Assign a mechanic first before marking En Route.', 'error');
                    _pendingEnRoute = id;
                    openMechanicPicker(id);
                    return;
                }
            }
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

    {{-- Mechanic Picker Modal --}}
    <div id="mechPickerModal"
        style="display:none;position:fixed;inset:0;z-index:9998;background:rgba(0,0,0,.45);align-items:center;justify-content:center;padding:16px;">
        <div
            style="background:#fff;border-radius:12px;width:100%;max-width:400px;max-height:80vh;display:flex;flex-direction:column;box-shadow:0 8px 32px rgba(0,0,0,.18);">
            <div
                style="padding:16px 18px 12px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #f0f2f5;">
                <h3 style="margin:0;font-size:15px;font-weight:700;color:#1d273b;">Assign Mechanic</h3>
                <button onclick="closeMechanicPicker()"
                    style="background:none;border:none;font-size:18px;color:#667382;cursor:pointer;padding:2px 6px;line-height:1;">&times;</button>
            </div>
            <div id="mechPickerList" style="padding:10px;overflow-y:auto;flex:1;"></div>
        </div>
    </div>
@endsection
