@extends('layouts.shop')

@section('content')
    @php
        $shopName = $shop->shop_name ?? (auth()->user()->name ?? 'Motor Shop');
        $currentSt = strtolower($shopStatus ?? 'closed');
        $stConf = [
            'open' => ['label' => 'Open', 'badge' => 'badge-success', 'dot' => '#2fb344'],
            'busy' => ['label' => 'Busy', 'badge' => 'badge-warning', 'dot' => '#f76707'],
            'maintenance' => ['label' => 'Maintenance', 'badge' => 'badge-info', 'dot' => '#206bc4'],
            'closed' => ['label' => 'Closed', 'badge' => 'badge-danger', 'dot' => '#d63939'],
        ];
        $sc = $stConf[$currentSt] ?? $stConf['closed'];
    @endphp

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        .stat-card {
            background: #fff;
            border: 1px solid #e6e7eb;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .07);
            padding: 20px;
        }

        .stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
        }

        .stat-value {
            font-size: 30px;
            font-weight: 700;
            color: #1d273b;
            line-height: 1;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 12px;
            font-weight: 500;
            color: #667382;
        }

        .req-card {
            background: #fff;
            border: 1px solid #e6e7eb;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .05);
            overflow: hidden;
            transition: box-shadow .15s;
        }

        .req-card:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, .1);
        }

        .req-card.is-new {
            border-color: #206bc4;
            box-shadow: 0 0 0 2px rgba(32, 107, 196, .15);
        }

        .req-head {
            padding: 14px 16px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            cursor: pointer;
            user-select: none;
        }

        .req-ico {
            width: 38px;
            height: 38px;
            flex-shrink: 0;
            border-radius: 8px;
            background: #daeeff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .req-ico i {
            font-size: 14px;
            color: #206bc4;
        }

        .req-info {
            flex: 1;
            min-width: 0;
        }

        .req-title {
            font-size: 14px;
            font-weight: 600;
            color: #1d273b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .req-meta {
            font-size: 12px;
            color: #667382;
            margin-top: 2px;
        }

        .req-chev {
            font-size: 11px;
            color: #a0a8b1;
            flex-shrink: 0;
            margin-top: 3px;
            transition: transform .2s;
        }

        .req-card.open .req-chev {
            transform: rotate(180deg);
        }

        .req-body {
            padding: 0 16px 14px;
            display: none;
        }

        .req-card.open .req-body {
            display: block;
        }

        .req-div {
            height: 1px;
            background: #f0f2f5;
            margin: 0 0 12px;
        }

        .req-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 16px;
            margin-bottom: 14px;
        }

        .req-grid .s2 {
            grid-column: 1/-1;
        }

        .req-fl {
            font-size: 11px;
            font-weight: 600;
            color: #667382;
            text-transform: uppercase;
            letter-spacing: .04em;
            margin-bottom: 3px;
        }

        .req-fv {
            font-size: 13px;
            color: #1d273b;
        }

        .req-acts {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .sbadge {
            display: inline-flex;
            align-items: center;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }

        #shop-live-map {
            height: 220px;
            border-radius: 6px;
            overflow: hidden;
            background: #e8edf2;
        }

        #modal-live-map {
            flex: 1;
        }

        .map-loader-wrap {
            position: relative;
        }

        .map-loader-overlay {
            position: absolute;
            inset: 0;
            z-index: 500;
            background: #e8edf2;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: opacity .3s;
            pointer-events: none;
        }

        .map-loader-overlay.modal-variant {
            background: #f4f6fb;
            border-radius: 0;
        }

        .map-spinner {
            width: 34px;
            height: 34px;
            border: 3px solid #d0d7de;
            border-top-color: #206bc4;
            border-radius: 50%;
            animation: mapSpin .75s linear infinite;
        }

        @keyframes mapSpin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>

    <div id="dash-toast"></div>

    {{-- Page Header --}}
    <div
        style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:24px;">
        <div>
            <div class="page-pretitle">Shop Overview</div>
            <h1 class="page-title">{{ $shopName }}</h1>
        </div>
        <div style="display:flex; align-items:center; gap:10px;">
            <a href="{{ route('shop.requests') }}" class="btn-outline-primary btn btn-sm">
                <i class="fa-list-ul fas"></i> All Requests
            </a>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(170px,1fr)); gap:16px; margin-bottom:24px;">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fde8e8;">
                <i class="fas fa-bell" style="color:#d63939; font-size:17px;"></i>
            </div>
            <div class="stat-value" id="stat-pending">{{ $pending }}</div>
            <div class="stat-label">Pending Requests</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fff3cd;">
                <i class="fas fa-wrench" style="color:#f76707; font-size:17px;"></i>
            </div>
            <div class="stat-value" id="stat-active">{{ $activeJobsCount }}</div>
            <div class="stat-label">Active Jobs</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#daeeff;">
                <i class="fas fa-calendar-day" style="color:#206bc4; font-size:17px;"></i>
            </div>
            <div class="stat-value">{{ $jobsToday }}</div>
            <div class="stat-label">Today's Jobs</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#d1f7d6;">
                <i class="fas fa-star" style="color:#2fb344; font-size:17px;"></i>
            </div>
            <div class="stat-value">{{ $averageRating > 0 ? $averageRating : '—' }}</div>
            <div class="stat-label">Avg Rating</div>
        </div>
    </div>

    {{-- Main Grid --}}
    <div style="display:grid; grid-template-columns:1fr 360px; gap:20px; align-items:start;">

        {{-- Left: Pending Requests --}}
        <div>
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
                <h2
                    style="font-size:15px; font-weight:700; color:#1d273b; margin:0; display:flex; align-items:center; gap:8px;">
                    <i class="fas fa-inbox" style="color:#206bc4; font-size:13px;"></i> Incoming Requests
                </h2>
                <span class="badge badge-danger" id="pending-badge">{{ $pending }} new</span>
            </div>

            <div id="requests-container" style="display:flex; flex-direction:column; gap:10px;">
                @forelse($requests as $req)
                    @php
                        $mName = $req->owner_name ?? ($req->motorist_name ?? 'Unknown Motorist');
                        $vehicle =
                            implode(
                                ' · ',
                                array_filter([$req->vehicle_make_model ?? null, $req->vehicle_variant_color ?? null]),
                            ) ?:
                            'Not specified';
                        $plate = $req->plate_temp_number ?? 'No plate';
                        $isDisp = ($req->request_type ?? '') === 'dispatch';
                    @endphp
                    <div class="req-card" id="req-{{ $req->id }}">
                        <div class="req-head" onclick="toggleCard(this)">
                            <div class="req-ico"><i class="fas {{ $isDisp ? 'fa-motorcycle' : 'fa-store' }}"></i></div>
                            <div class="req-info">
                                <div class="req-title">{{ $req->issue_type ?? 'Motorcycle Issue' }}</div>
                                <div class="req-meta">{{ $mName }} ·
                                    {{ \Carbon\Carbon::parse($req->created_at)->diffForHumans() }}</div>
                            </div>
                            <span class="sbadge badge-danger">REQUESTED</span>
                            <i class="fas fa-chevron-down req-chev"></i>
                        </div>
                        <div class="req-body">
                            <div class="req-div"></div>
                            <div class="req-grid">
                                <div>
                                    <div class="req-fl">Contact</div>
                                    <div class="req-fv">{{ $req->contact_number ?? '—' }}</div>
                                </div>
                                <div>
                                    <div class="req-fl">Plate No.</div>
                                    <div class="req-fv">{{ $plate }}</div>
                                </div>
                                <div class="s2">
                                    <div class="req-fl">Vehicle</div>
                                    <div class="req-fv">{{ $vehicle }}</div>
                                </div>
                                @if (!empty($req->location))
                                    <div class="s2">
                                        <div class="req-fl">Location</div>
                                        <div class="req-fv">{{ $req->location }}</div>
                                    </div>
                                @endif
                                @if (!empty($req->description))
                                    <div class="s2">
                                        <div class="req-fl">Description</div>
                                        <div class="req-fv">{{ $req->description }}</div>
                                    </div>
                                @endif
                            </div>
                            <div class="req-acts">
                                <button class="btn btn-success btn-sm" onclick="acceptRequest({{ $req->id }})"><i
                                        class="fas fa-check"></i> Accept</button>
                                <button class="btn btn-danger btn-sm" onclick="declineRequest({{ $req->id }})"><i
                                        class="fas fa-xmark"></i> Decline</button>
                                @if (!empty($req->latitude) && !empty($req->longitude))
                                    <a href="https://www.google.com/maps?q={{ $req->latitude }},{{ $req->longitude }}"
                                        target="_blank" rel="noopener" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-map-location-dot"></i> Map
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="t-card" style="padding:32px; text-align:center; color:#667382;">
                        <i class="fas fa-bell-slash"
                            style="font-size:24px; margin-bottom:8px; display:block; opacity:.4;"></i>
                        No pending requests right now
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Right Column --}}
        <div style="display:flex; flex-direction:column; gap:16px;">

            {{-- Live Map --}}
            <div class="t-card" style="padding:16px;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                    <h3
                        style="font-size:14px; font-weight:700; color:#1d273b; margin:0; display:flex; align-items:center; gap:7px;">
                        <i class="fas fa-map-location-dot" style="color:#206bc4; font-size:13px;"></i> Live Map
                    </h3>
                    <button onclick="openLiveMapModal()" class="btn btn-secondary btn-sm"><i class="fas fa-expand"></i>
                        Expand</button>
                </div>
                <div class="map-loader-wrap">
                    <div id="shop-live-map"></div>
                    <div id="small-map-loader" class="map-loader-overlay">
                        <div class="map-spinner"></div>
                    </div>
                </div>
                <div style="display:flex; gap:16px; margin-top:8px;">
                    <span style="display:flex; align-items:center; gap:5px; font-size:11px; color:#667382;"><span
                            style="width:8px; height:8px; border-radius:50%; background:#206bc4; display:inline-block;"></span>
                        Shop</span>
                    <span style="display:flex; align-items:center; gap:5px; font-size:11px; color:#667382;"><span
                            style="width:8px; height:8px; border-radius:50%; background:#d63939; display:inline-block;"></span>
                        Motorist</span>
                </div>
                <p id="map-status-text" style="margin-top:6px; font-size:11px; color:#667382;">Loading…</p>
            </div>

            {{-- Active Jobs --}}
            <div>
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                    <h3
                        style="font-size:14px; font-weight:700; color:#1d273b; margin:0; display:flex; align-items:center; gap:7px;">
                        <i class="fas fa-bolt" style="color:#f76707; font-size:13px;"></i> Active Jobs
                    </h3>
                    <span class="badge badge-warning" id="active-badge">{{ $activeJobsCount }}</span>
                </div>
                <div style="display:flex; flex-direction:column; gap:8px;" id="active-jobs-container">
                    @forelse($jobs as $job)
                        @php
                            $jName = $job->owner_name ?? ($job->motorist_name ?? 'Unknown');
                            $jConf = [
                                'accepted' => [
                                    'label' => 'Accepted',
                                    'badge' => 'badge-warning',
                                    'next' => 'en_route',
                                    'nLabel' => 'Mechanic En Route',
                                    'nBtn' => 'btn-primary',
                                ],
                                'en_route' => [
                                    'label' => 'En Route',
                                    'badge' => 'badge-info',
                                    'next' => 'arrived',
                                    'nLabel' => 'Mark Arrived',
                                    'nBtn' => 'btn-primary',
                                ],
                                'arrived' => [
                                    'label' => 'Arrived',
                                    'badge' => 'badge-purple',
                                    'next' => 'in_progress',
                                    'nLabel' => 'Start Repair',
                                    'nBtn' => 'btn-warning',
                                ],
                                'in_progress' => [
                                    'label' => 'In Progress',
                                    'badge' => 'badge-orange',
                                    'next' => 'completed',
                                    'nLabel' => 'Mark Complete',
                                    'nBtn' => 'btn-success',
                                ],
                            ];
                            $jc = $jConf[$job->status] ?? [
                                'label' => $job->status,
                                'badge' => 'badge-secondary',
                                'next' => null,
                                'nLabel' => null,
                                'nBtn' => 'btn-secondary',
                            ];
                        @endphp
                        <div class="req-card" id="req-{{ $job->id }}">
                            <div class="req-head" onclick="toggleCard(this)">
                                <div class="req-info">
                                    <div class="req-title" style="font-size:13px;">{{ $job->issue_type ?? 'Job' }}</div>
                                    <div class="req-meta">{{ $jName }}</div>
                                </div>
                                <span class="sbadge {{ $jc['badge'] }}">{{ $jc['label'] }}</span>
                                <i class="fas fa-chevron-down req-chev"></i>
                            </div>
                            @if ($jc['next'])
                                <div class="req-body">
                                    <div class="req-div"></div>
                                    <button class="btn {{ $jc['nBtn'] }} btn-sm"
                                        style="width:100%; justify-content:center;"
                                        onclick="updateRequestStatus({{ $job->id }}, '{{ $jc['next'] }}')">{{ $jc['nLabel'] }}</button>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="t-card" style="padding:20px; text-align:center; color:#667382;">
                            <i class="fas fa-clock"
                                style="font-size:18px; margin-bottom:6px; display:block; opacity:.4;"></i>
                            No active jobs
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Map Modal --}}
    <div id="liveMapModal"
        style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,.55); padding:20px; align-items:center; justify-content:center;">
        <div
            style="width:100%; max-width:900px; background:#fff; border-radius:10px; overflow:hidden; display:flex; flex-direction:column; max-height:90vh;">
            <div
                style="padding:16px 20px; border-bottom:1px solid #e6e7eb; display:flex; align-items:center; justify-content:space-between; flex-shrink:0;">
                <div>
                    <h2 style="font-size:16px; font-weight:700; margin:0; color:#1d273b;">Live Map</h2>
                    <p style="font-size:12px; color:#667382; margin:2px 0 0;">Blue = shop · Red = motorist requests</p>
                </div>
                <button onclick="closeLiveMapModal()" class="btn btn-secondary btn-sm"><i class="fas fa-xmark"></i>
                    Close</button>
            </div>
            <div style="flex:1; min-height:400px; position:relative;">
                <div id="modal-live-map" style="width:100%; height:100%; min-height:400px;"></div>
                <div id="modal-map-loader" class="map-loader-overlay modal-variant">
                    <div class="map-spinner" style="width:42px;height:42px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Profile Completion Modal --}}
    @if ($needsProfileCompletion)
        <div
            style="position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,.55); display:flex; align-items:center; justify-content:center; padding:16px;">
            <div style="width:100%; max-width:480px; background:#fff; border-radius:10px; padding:28px;">
                <h2 style="font-size:20px; font-weight:700; color:#1d273b; margin:0 0 6px;">Complete Shop Profile</h2>
                <p style="font-size:14px; color:#667382; margin:0 0 20px;">Fill in your shop details before using the
                    dashboard.</p>
                <form method="POST" action="{{ route('shop.update') }}"
                    style="display:flex; flex-direction:column; gap:14px;">
                    @csrf
                    <div><label class="form-label">Shop Name</label><input type="text" name="shop_name"
                            value="{{ old('shop_name', $shop->shop_name ?? '') }}" required class="form-control"
                            placeholder="e.g. SSJC Motor Shop"></div>
                    <div><label class="form-label">Shop Address</label><input type="text" name="address"
                            value="{{ old('address', $shop->address ?? '') }}" required class="form-control"
                            placeholder="87 Lower Kalaklan, Olongapo City"></div>
                    <div><label class="form-label">Phone Number</label><input type="text" name="phone"
                            value="{{ old('phone', $shop->phone ?? '') }}" required class="form-control"
                            placeholder="+63 917 140 3498"></div>
                    <input type="hidden" name="latitude" value="{{ old('latitude', $shop->latitude ?? 14.8386) }}">
                    <input type="hidden" name="longitude" value="{{ old('longitude', $shop->longitude ?? 120.2842) }}">
                    <input type="hidden" name="status" value="{{ old('status', $shop->status ?? 'closed') }}">
                    <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">Save Shop
                        Info</button>
                </form>
            </div>
        </div>
    @endif

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        function showToast(msg, type, dur) {
            dur = dur || 4500;
            var icons = {
                success: 'fa-circle-check',
                error: 'fa-circle-xmark',
                info: 'fa-circle-info',
                warn: 'fa-triangle-exclamation',
                new: 'fa-bell'
            };
            var item = document.createElement('div');
            item.className = 'toast-item ' + (type || 'info');
            item.innerHTML = '<i class="fa-solid ' + (icons[type] || 'fa-circle-info') +
                '" style="flex-shrink:0;margin-top:1px;"></i><span>' + escHtml(msg) + '</span>';
            document.getElementById('dash-toast').appendChild(item);
            setTimeout(function() {
                item.remove();
            }, dur);
        }

        function escHtml(s) {
            return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g,
                '&quot;').replace(/'/g, '&#039;');
        }

        function getCsrfToken() {
            return (document.querySelector('meta[name="csrf-token"]') || {}).getAttribute('content') || '';
        }

        function toggleCard(h) {
            h.closest('.req-card').classList.toggle('open');
        }

        var SB = {
            requested: {
                label: 'REQUESTED',
                badge: 'badge-danger'
            },
            accepted: {
                label: 'ACCEPTED',
                badge: 'badge-warning'
            },
            en_route: {
                label: 'EN ROUTE',
                badge: 'badge-info'
            },
            arrived: {
                label: 'ARRIVED',
                badge: 'badge-purple'
            },
            in_progress: {
                label: 'IN PROGRESS',
                badge: 'badge-orange'
            },
            completed: {
                label: 'COMPLETED',
                badge: 'badge-success'
            },
            declined: {
                label: 'DECLINED',
                badge: 'badge-secondary'
            },
        };

        function updateCardBadge(id, status) {
            var c = document.getElementById('req-' + id);
            if (!c) return;
            var s = SB[status] || {};
            var b = c.querySelector('.sbadge');
            if (b) {
                b.className = 'sbadge ' + (s.badge || 'badge-secondary');
                b.textContent = s.label || status.toUpperCase().replace(/_/g, ' ');
            }
        }

        function adjustCount(id, delta, suffix) {
            suffix = suffix || '';
            var el = document.getElementById(id);
            if (!el) return;
            el.textContent = Math.max(0, (parseInt(el.textContent) || 0) + delta) + suffix;
        }

        async function acceptRequest(id) {
            showConfirmModal('Accept Request', 'Accept this dispatch request and notify the motorist?',
                async function() {
                    try {
                        var r = await fetch('/shop/accept/' + id, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': getCsrfToken(),
                                'Accept': 'application/json'
                            }
                        });
                        var d = await r.json();
                        if (d.success) {
                            showToast('Request accepted.', 'success');
                            var c = document.getElementById('req-' + id);
                            if (c) c.remove();
                            adjustCount('stat-pending', -1);
                            adjustCount('pending-badge', -1, ' new');
                        } else showToast(d.message || 'Failed.', 'error');
                    } catch (e) {
                        showToast('Network error.', 'error');
                    }
                }, 'Accept', '#2fb344');
        }
        async function declineRequest(id) {
            showConfirmModal('Decline Request', 'Decline this request? The motorist will be notified.',
                async function() {
                    try {
                        var r = await fetch('/shop/decline/' + id, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': getCsrfToken(),
                                'Accept': 'application/json'
                            }
                        });
                        var d = await r.json();
                        if (d.success) {
                            showToast('Request declined.', 'info');
                            var c = document.getElementById('req-' + id);
                            if (c) c.remove();
                            adjustCount('stat-pending', -1);
                            adjustCount('pending-badge', -1, ' new');
                        } else showToast(d.message || 'Failed.', 'error');
                    } catch (e) {
                        showToast('Network error.', 'error');
                    }
                }, 'Decline', '#d63939');
        }
        async function updateRequestStatus(id, status) {
            try {
                var r = await fetch('/shop/request/' + id + '/status', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        status: status
                    })
                });
                var d = await r.json();
                if (d.success) {
                    showToast('Status: ' + status.replace(/_/g, ' '), 'success');
                    updateCardBadge(id, status);
                    if (status === 'completed') {
                        var c = document.getElementById('req-' + id);
                        if (c) {
                            c.style.opacity = '.5';
                            var b = c.querySelector('.req-body');
                            if (b) b.innerHTML = '';
                        }
                        adjustCount('stat-active', -1);
                        adjustCount('active-badge', -1);
                    }
                } else showToast(d.message || 'Update failed.', 'error');
            } catch (e) {
                showToast('Network error.', 'error');
            }
        }



        function prependRequestCard(req) {
            var con = document.getElementById('requests-container');
            if (!con) return;
            var empty = con.querySelector('.t-card');
            if (empty) empty.remove();
            var div = document.createElement('div');
            div.id = 'req-' + req.id;
            div.className = 'req-card is-new open';
            div.innerHTML =
                '<div class="req-head" onclick="toggleCard(this)"><div class="req-ico"><i class="fas fa-motorcycle"></i></div><div class="req-info"><div class="req-title">' +
                escHtml(req.issue_type || 'Motorcycle Issue') + '</div><div class="req-meta">' + escHtml(req.owner_name ||
                    'Motorist') +
                ' · Just now</div></div><span class="sbadge badge-danger">REQUESTED</span><i class="fas fa-chevron-down req-chev"></i></div><div class="req-body" style="display:block;"><div class="req-div"></div><div class="req-grid"><div><div class="req-fl">Contact</div><div class="req-fv">' +
                escHtml(req.contact_number || '—') +
                '</div></div><div><div class="req-fl">Plate No.</div><div class="req-fv">' + escHtml(req
                    .plate_temp_number || '—') +
                '</div></div><div class="s2"><div class="req-fl">Vehicle</div><div class="req-fv">' + escHtml(req
                    .vehicle_make_model || 'Not specified') + '</div></div>' + (req.location ?
                    '<div class="s2"><div class="req-fl">Location</div><div class="req-fv">' + escHtml(req.location) +
                    '</div></div>' : '') +
                '</div><div class="req-acts"><button class="btn btn-success btn-sm" onclick="acceptRequest(' + req.id +
                ')"><i class="fas fa-check"></i> Accept</button><button class="btn btn-danger btn-sm" onclick="declineRequest(' +
                req.id + ')"><i class="fas fa-times"></i> Decline</button></div></div>';
            con.insertBefore(div, con.firstChild);
        }

        var smallMap = null,
            modalMap = null,
            latestShop = null,
            latestRequests = [];
        var smallShopMkr = null,
            modalShopMkr = null,
            smallMotoMkrs = [],
            modalMotoMkrs = [];
        var shopIcon = null,
            motoIcon = null;

        document.addEventListener('DOMContentLoaded', function() {
            shopIcon = L.divIcon({
                className: '',
                html: '<div style="width:14px;height:14px;background:#206bc4;border:3px solid #fff;border-radius:50%;box-shadow:0 0 8px rgba(32,107,196,.5);"></div>',
                iconSize: [14, 14],
                iconAnchor: [7, 7]
            });
            motoIcon = L.divIcon({
                className: '',
                html: '<div style="width:14px;height:14px;background:#d63939;border:3px solid #fff;border-radius:50%;box-shadow:0 0 8px rgba(214,57,57,.5);"></div>',
                iconSize: [14, 14],
                iconAnchor: [7, 7]
            });
            smallMap = L.map('shop-live-map', {
                zoomControl: false,
                attributionControl: false
            }).setView([14.8386, 120.2842], 14);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(smallMap);
            loadLiveMapData();
            setInterval(loadLiveMapData, 5000);
            if (window.Echo && window.shopId) {
                window.Echo.private('shop.' + window.shopId).listen('.dispatch.new', function(req) {
                    showToast('New request from ' + (req.owner_name || 'motorist') + ' — ' + (req
                        .issue_type || 'Issue'), 'new', 7000);
                    prependRequestCard(req);
                    adjustCount('stat-pending', 1);
                    adjustCount('pending-badge', 1, ' new');
                });
            }
        });

        function initModalMap() {
            if (modalMap) return;
            modalMap = L.map('modal-live-map', {
                attributionControl: false
            }).setView([14.8386, 120.2842], 14);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(modalMap);
        }

        function openLiveMapModal() {
            var ml = document.getElementById('modal-map-loader');
            if (ml) {
                ml.style.opacity = '1';
                ml.style.display = 'flex';
            }
            document.getElementById('liveMapModal').style.display = 'flex';
            initModalMap();
            setTimeout(function() {
                modalMap.invalidateSize();
                renderMkrs(modalMap, true);
                fitMap(modalMap);
                if (ml) {
                    ml.style.opacity = '0';
                    setTimeout(function() {
                        ml.style.display = 'none';
                    }, 300);
                }
            }, 200);
        }

        function closeLiveMapModal() {
            document.getElementById('liveMapModal').style.display = 'none';
        }
        async function loadLiveMapData() {
            try {
                var r = await fetch('/shop/dashboard-map-data', {
                    headers: {
                        Accept: 'application/json'
                    }
                });
                var d = await r.json();
                if (!d.success) {
                    document.getElementById('map-status-text').textContent = 'Unable to load map.';
                    return;
                }
                latestShop = d.shop;
                latestRequests = d.requests || [];
                renderMkrs(smallMap, false);
                fitMap(smallMap);
                var sl = document.getElementById('small-map-loader');
                if (sl && sl.style.display !== 'none') {
                    sl.style.opacity = '0';
                    setTimeout(function() {
                        sl.style.display = 'none';
                    }, 300);
                }
                if (modalMap && document.getElementById('liveMapModal').style.display !== 'none') {
                    renderMkrs(modalMap, true);
                    fitMap(modalMap);
                }
                document.getElementById('map-status-text').textContent = latestRequests.length + ' motorist location' +
                    (latestRequests.length !== 1 ? 's' : '') + '.';
            } catch (e) {
                document.getElementById('map-status-text').textContent = 'Map error.';
            }
        }

        function renderMkrs(map, isModal) {
            (isModal ? modalMotoMkrs : smallMotoMkrs).forEach(function(m) {
                map.removeLayer(m);
            });
            if (isModal) modalMotoMkrs = [];
            else smallMotoMkrs = [];
            if (latestShop && latestShop.latitude && latestShop.longitude) {
                var la = parseFloat(latestShop.latitude),
                    ln = parseFloat(latestShop.longitude);
                if (!isNaN(la) && !isNaN(ln)) {
                    var pop = '<strong>' + escHtml(latestShop.shop_name || 'Shop') + '</strong><br>' + escHtml(latestShop
                        .address || '');
                    if (isModal) {
                        modalShopMkr ? modalShopMkr.setLatLng([la, ln]).setPopupContent(pop) : (modalShopMkr = L.marker([la,
                            ln
                        ], {
                            icon: shopIcon
                        }).addTo(map).bindPopup(pop));
                    } else {
                        smallShopMkr ? smallShopMkr.setLatLng([la, ln]).setPopupContent(pop) : (smallShopMkr = L.marker([la,
                            ln
                        ], {
                            icon: shopIcon
                        }).addTo(map).bindPopup(pop));
                    }
                }
            }
            latestRequests.forEach(function(req) {
                var la = parseFloat(req.latitude),
                    ln = parseFloat(req.longitude);
                if (isNaN(la) || isNaN(ln)) return;
                var pop = '<strong>' + escHtml(req.motorist_name || 'Motorist') + '</strong><br>' + escHtml(req
                    .issue_type || '');
                var m = L.marker([la, ln], {
                    icon: motoIcon
                }).addTo(map).bindPopup(pop);
                isModal ? modalMotoMkrs.push(m) : smallMotoMkrs.push(m);
            });
        }

        function fitMap(map) {
            var pts = [];
            if (latestShop && latestShop.latitude && latestShop.longitude) pts.push([parseFloat(latestShop.latitude),
                parseFloat(latestShop.longitude)
            ]);
            latestRequests.forEach(function(r) {
                var la = parseFloat(r.latitude),
                    ln = parseFloat(r.longitude);
                if (!isNaN(la) && !isNaN(ln)) pts.push([la, ln]);
            });
            if (pts.length === 1) map.setView(pts[0], 16);
            else if (pts.length > 1) map.fitBounds(pts, {
                padding: [30, 30],
                maxZoom: 16
            });
        }
    </script>
@endsection
