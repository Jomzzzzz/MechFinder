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

        {{-- Left: Standby / Listening indicator --}}
        <div>
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
                <h2
                    style="font-size:15px; font-weight:700; color:#1d273b; margin:0; display:flex; align-items:center; gap:8px;">
                    <i class="fas fa-inbox" style="color:#206bc4; font-size:13px;"></i> Incoming Requests
                </h2>
                <span class="badge badge-danger" id="pending-badge"
                    style="{{ $pending > 0 ? '' : 'display:none;' }}">{{ $pending }} unclaimed</span>
            </div>
            <div class="t-card" style="padding:32px 24px; text-align:center;">
                <div
                    style="width:48px;height:48px;border-radius:50%;background:#ebf0fb;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                    <i class="fas fa-bell" style="color:#206bc4; font-size:20px;"></i>
                </div>
                <div style="font-weight:700; color:#1d273b; font-size:15px; margin-bottom:6px;">Listening for rescue
                    requests</div>
                <div style="font-size:13px; color:#667382; line-height:1.6;">When a motorist nearby needs help, a popup will
                    appear here. First shop to accept gets the job.</div>
                @if ($pending > 0)
                    <div
                        style="margin-top:18px; padding:12px 16px; background:#fff5f5; border:1px solid #fecaca; border-radius:8px; display:flex; align-items:center; justify-content:space-between;">
                        <span style="font-size:13px; color:#d63939; font-weight:600;"><i class="fas fa-exclamation-circle"
                                style="margin-right:5px;"></i>{{ $pending }} pending
                            request{{ $pending > 1 ? 's' : '' }}</span>
                        <button onclick="loadUnclaimedRequests()"
                            style="background:#d63939;color:#fff;border:none;border-radius:6px;padding:6px 14px;font-size:12px;font-weight:700;cursor:pointer;">View</button>
                    </div>
                @else
                    <div style="margin-top:18px;">
                        <button onclick="loadUnclaimedRequests()"
                            style="background:#f0f4ff;color:#206bc4;border:1px solid #c5d3f0;border-radius:6px;padding:8px 18px;font-size:12px;font-weight:700;cursor:pointer;width:100%;">
                            <i class="fas fa-rotate" style="margin-right:5px;"></i>Check for requests
                        </button>
                    </div>
                @endif
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
                                    {{-- Mechanic assignment row --}}
                                    <div class="mechanic-row" id="mrow-{{ $job->id }}"
                                        style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                                        @if ($job->assigned_mechanic_name)
                                            <i class="fas fa-user-gear"
                                                style="color:#667382;font-size:12px;flex-shrink:0;"></i>
                                            <span id="mname-{{ $job->id }}"
                                                style="font-size:12px;color:#374151;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $job->assigned_mechanic_name }}</span>
                                            <button class="btn btn-secondary btn-sm"
                                                style="padding:2px 8px;font-size:11px;flex-shrink:0;"
                                                onclick="event.stopPropagation();openMechanicPicker({{ $job->id }})">Change</button>
                                        @else
                                            <button class="btn btn-secondary btn-sm"
                                                style="width:100%;justify-content:center;"
                                                onclick="event.stopPropagation();openMechanicPicker({{ $job->id }})">
                                                <i class="fas fa-user-plus" style="margin-right:5px;"></i>Assign Mechanic
                                            </button>
                                        @endif
                                    </div>
                                    <button id="en-route-btn-{{ $job->id }}" class="btn {{ $jc['nBtn'] }} btn-sm"
                                        style="width:100%; justify-content:center;{{ $jc['next'] === 'en_route' && empty($job->assigned_mechanic_name) ? ' display:none;' : '' }}"
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

    {{-- Mechanic Picker Modal --}}
    <div id="mechanicPickerModal"
        style="display:none;position:fixed;inset:0;z-index:9998;background:rgba(0,0,0,.45);align-items:flex-end;justify-content:center;"
        onclick="closeMechanicPicker()">
        <div onclick="event.stopPropagation()"
            style="width:100%;max-width:480px;background:#fff;border-radius:14px 14px 0 0;padding:20px 16px 24px;max-height:70vh;display:flex;flex-direction:column;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                <h3 id="mechPickerTitle" style="font-size:15px;font-weight:700;margin:0;color:#1d273b;">Assign Mechanic
                </h3>
                <button onclick="closeMechanicPicker()"
                    style="background:none;border:none;cursor:pointer;padding:4px;color:#667382;">
                    <i class="fas fa-xmark" style="font-size:16px;"></i>
                </button>
            </div>
            <div id="mechanicPickerList" style="overflow-y:auto;flex:1;display:flex;flex-direction:column;gap:8px;">
                <div style="text-align:center;padding:20px;color:#667382;font-size:13px;">
                    <i class="fas fa-spinner fa-spin" style="margin-right:6px;"></i>Loading mechanics…
                </div>
            </div>
            <button id="mechPickerSkip" onclick="skipMechanicAndAccept()"
                style="display:none;margin-top:12px;width:100%;padding:10px;border:1px dashed #cdd1d9;background:none;border-radius:8px;color:#667382;font-size:13px;cursor:pointer;">
                <i class="fas fa-forward" style="margin-right:6px;"></i>Skip — Accept without assigning a mechanic
            </button>
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
                    <input type="hidden" name="status_id" value="{{ old('status_id', $shop->status_id ?? 4) }}">
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
            completed: {
                label: 'COMPLETED',
                badge: 'badge-success'
            },
            declined: {
                label: 'DECLINED',
                badge: 'badge-secondary'
            },
        };

        var JCONF = {
            accepted: {
                next: 'en_route',
                nLabel: 'Mechanic En Route',
                nBtn: 'btn-primary'
            },
            en_route: {
                next: 'arrived',
                nLabel: 'Mark Arrived',
                nBtn: 'btn-primary'
            },
            arrived: {
                next: 'completed',
                nLabel: 'Mark Complete',
                nBtn: 'btn-success'
            },
        };

        function updateCardBadge(id, status) {
            var c = document.getElementById('req-' + id);
            if (!c) return;
            var sb = SB[status] || {};
            var b = c.querySelector('.sbadge');
            if (b) {
                b.className = 'sbadge ' + (sb.badge || 'badge-secondary');
                b.textContent = sb.label || status.toUpperCase().replace(/_/g, ' ');
            }
            // Rebuild req-body with the correct next-step button
            var jc = JCONF[status];
            var body = c.querySelector('.req-body');
            if (!body) return;
            if (!jc) {
                body.innerHTML = '';
                return;
            }
            // Preserve existing mechanic row content
            var mrow = document.getElementById('mrow-' + id);
            var mechInner = mrow ? mrow.innerHTML :
                '<button class="btn btn-secondary btn-sm" style="width:100%;justify-content:center;" onclick="event.stopPropagation();openMechanicPicker(' +
                id + ')"><i class="fas fa-user-plus" style="margin-right:5px;"></i>Assign Mechanic</button>';
            var hasMech = mechInner.indexOf('<span') !== -1;
            var enRouteBtnStyle = (jc.next === 'en_route' && !hasMech) ?
                'width:100%;justify-content:center;display:none;' :
                'width:100%;justify-content:center;';
            body.innerHTML = '<div class="req-div"></div>' +
                '<div class="mechanic-row" id="mrow-' + id +
                '" style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">' + mechInner + '</div>' +
                '<button id="en-route-btn-' + id + '" class="btn ' + jc.nBtn + ' btn-sm" style="' + enRouteBtnStyle +
                '" onclick="updateRequestStatus(' + id + ', \'' + jc.next + '\')">' +
                jc.nLabel + '</button>';
        }

        function adjustCount(id, delta, suffix) {
            suffix = suffix || '';
            var el = document.getElementById(id);
            if (!el) return;
            el.textContent = Math.max(0, (parseInt(el.textContent) || 0) + delta) + suffix;
        }

        function handleDispatchNewEvent(req) {
            if (!req || !req.id) return;
            queueDispatchPopup(req);
            adjustCount('stat-pending', 1);
            var pb = document.getElementById('pending-badge');
            if (pb) {
                var n = (parseInt(pb.textContent) || 0) + 1;
                pb.textContent = n + ' unclaimed';
                pb.style.display = '';
            }
        }

        function handleDispatchStatusEvent(payload) {
            if (!payload || !payload.dispatch_id) return;
            var id = payload.dispatch_id;
            var status = payload.status;
            var c = document.getElementById('req-' + id);
            if (!c) return;
            updateCardBadge(id, status);
            if (status === 'completed') {
                if (c.dataset.completed !== 'true') {
                    c.dataset.completed = 'true';
                    c.style.opacity = '.5';
                    var b = c.querySelector('.req-body');
                    if (b) b.innerHTML = '';
                    adjustCount('stat-active', -1);
                    adjustCount('active-badge', -1);
                }
            }
        }

        // ---------- Dispatch Popup ----------
        var _currentPopupReqId = null;
        var _currentPopupReq = null;
        var _popupTimer = null;
        var _popupQueue = [];
        var _popupSelectedMechId = null;
        var _popupSelectedMechName = null;

        function queueDispatchPopup(req) {
            _popupQueue.push(req);
            if (!_currentPopupReqId) processPopupQueue();
        }

        function processPopupQueue() {
            if (_popupQueue.length === 0) return;
            showDispatchPopup(_popupQueue.shift());
        }

        function showDispatchPopup(req) {
            _currentPopupReqId = req.id;
            _currentPopupReq = req;
            document.getElementById('popup-issue').textContent = req.issue_type || 'Motorcycle Issue';
            var parts = [];
            if (req.owner_name) parts.push('<strong>' + escHtml(req.owner_name) + '</strong>');
            if (req.contact_number) parts[0] = (parts[0] || '') + ' <span style="color:#667382;">· ' + escHtml(req
                .contact_number) + '</span>';
            if (req.vehicle_make_model) parts.push(
                '<span style="color:#667382;font-size:12px;"><i class="fas fa-motorcycle" style="margin-right:4px;"></i>' +
                escHtml(req.vehicle_make_model) + '</span>');
            if (req.location) parts.push(
                '<span style="color:#667382;font-size:12px;"><i class="fas fa-location-dot" style="color:#d63939;margin-right:4px;"></i>' +
                escHtml(req.location) + '</span>');
            if (req.description) parts.push('<em style="color:#667382;font-size:12px;">' + escHtml(req.description) +
                '</em>');
            document.getElementById('popup-details').innerHTML = parts.join('<br>') || '—';
            var acceptBtn = document.getElementById('popup-accept-btn');
            acceptBtn.disabled = false;
            acceptBtn.innerHTML = '<i class="fas fa-check"></i> Accept Job';
            _popupSelectedMechId = null;
            _popupSelectedMechName = null;
            document.getElementById('dispatch-popup').style.display = 'flex';
            startPopupTimer(60);
            loadMechanicsForPopup();
        }

        function startPopupTimer(seconds) {
            clearInterval(_popupTimer);
            var remaining = seconds;

            function tick() {
                var badge = document.getElementById('popup-timer-badge');
                if (badge) badge.textContent = remaining + 's';
                if (remaining <= 0) {
                    clearInterval(_popupTimer);
                    closeDispatchPopup();
                }
                remaining--;
            }
            tick();
            _popupTimer = setInterval(tick, 1000);
        }

        async function acceptPopupRequest() {
            if (!_currentPopupReqId) return;
            clearInterval(_popupTimer);
            await doAcceptRequest(_currentPopupReqId, _popupSelectedMechId, _popupSelectedMechName);
        }

        function declinePopupRequest() {
            if (!_currentPopupReqId) return;
            fetch('/shop/decline/' + _currentPopupReqId, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken()
                }
            });
            closeDispatchPopup();
        }

        function closeDispatchPopup() {
            clearInterval(_popupTimer);
            document.getElementById('dispatch-popup').style.display = 'none';
            _currentPopupReqId = null;
            _currentPopupReq = null;
            setTimeout(processPopupQueue, 400);
        }

        async function loadUnclaimedRequests() {
            try {
                var r = await fetch('/shop/unclaimed-requests', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                var d = await r.json();
                if (d.requests && d.requests.length) {
                    _popupQueue = [];
                    _currentPopupReqId = null;
                    d.requests.forEach(function(req) {
                        _popupQueue.push(req);
                    });
                    processPopupQueue();
                } else showToast('No unclaimed requests at the moment.', 'info');
            } catch (e) {
                showToast('Could not load requests.', 'error');
            }
        }
        async function updateRequestStatus(id, status) {
            // Guard: must assign a mechanic before going en_route
            if (status === 'en_route') {
                var mrow = document.getElementById('mrow-' + id);
                var hasMechanic = mrow && !!mrow.querySelector('span');
                if (!hasMechanic) {
                    showToast('Assign a mechanic first before marking En Route.', 'warning', 5000);
                    _pendingEnRoute = id;
                    openMechanicPicker(id);
                    return;
                }
            }
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



        // Popup is now the entry point for new requests — prependRequestCard is no longer used

        // ── Mechanic Picker ──────────────────────────────────────
        var _pickerRequestId = null;
        var _pickerMode = null; // null | 'accept'
        var _pendingEnRoute = null; // request id waiting for mechanic before en_route

        function openMechanicPicker(requestId) {
            _pickerRequestId = requestId;
            _pickerMode = null;
            var title = document.getElementById('mechPickerTitle');
            if (title) title.textContent = 'Assign Mechanic';
            var skip = document.getElementById('mechPickerSkip');
            if (skip) {
                skip.style.display = 'none';
                skip.disabled = false;
            }
            document.getElementById('mechanicPickerModal').style.display = 'flex';
            loadMechanicsForPicker();
        }

        function openMechanicPickerForAccept(requestId, req) {
            _pickerRequestId = requestId;
            _pickerMode = 'accept';
            var title = document.getElementById('mechPickerTitle');
            if (title) title.textContent = 'Select Mechanic';
            var skip = document.getElementById('mechPickerSkip');
            if (skip) {
                skip.style.display = 'block';
                skip.disabled = false;
            }
            document.getElementById('mechanicPickerModal').style.display = 'flex';
            loadMechanicsForPicker();
        }

        function closeMechanicPicker() {
            document.getElementById('mechanicPickerModal').style.display = 'none';
            _pickerRequestId = null;
            _pickerMode = null;
            _pendingEnRoute = null;
        }

        async function loadMechanicsForPicker() {
            var list = document.getElementById('mechanicPickerList');
            list.innerHTML =
                '<div style="text-align:center;padding:20px;color:#667382;font-size:13px;"><i class="fas fa-spinner fa-spin" style="margin-right:6px;"></i>Loading mechanics…</div>';
            try {
                var r = await fetch('/shop/mechanics-list', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                var mechanics = await r.json();
                if (!mechanics.length) {
                    list.innerHTML =
                        '<div style="text-align:center;padding:20px;color:#667382;font-size:13px;">No mechanics found. <a href="/shop/mechanics" style="color:#f76707;">Add one →</a></div>';
                    return;
                }
                list.innerHTML = '';
                mechanics.forEach(function(m) {
                    var statusColor = m.status === 'available' ? '#2fb344' : (m.status === 'dispatched' ?
                        '#f76707' : '#667382');
                    var statusLabel = m.status === 'available' ? 'Available' : (m.status === 'dispatched' ?
                        'Dispatched' : 'Off Duty');
                    var isDisabled = m.status === 'off_duty' || m.status === 'dispatched';
                    var row = document.createElement('div');
                    row.style.cssText =
                        'display:flex;align-items:center;gap:10px;padding:10px 12px;border:1px solid #e6e7eb;border-radius:8px;cursor:pointer;transition:background .15s;' +
                        (isDisabled ? 'opacity:.5;pointer-events:none;' : '');
                    row.innerHTML =
                        '<div style="width:36px;height:36px;border-radius:50%;background:#f0f1f3;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-user-gear" style="color:#667382;font-size:14px;"></i></div>' +
                        '<div style="flex:1;min-width:0;"><div style="font-size:13px;font-weight:600;color:#1d273b;">' +
                        escHtml(m.name || 'Mechanic') + '</div>' +
                        (m.phone ? '<div style="font-size:11px;color:#667382;">' + escHtml(m.phone) + '</div>' :
                            '') + '</div>' +
                        '<span style="font-size:11px;font-weight:600;color:' + statusColor + ';background:' +
                        statusColor + '1a;padding:2px 8px;border-radius:10px;">' + statusLabel + '</span>';
                    if (!isDisabled) {
                        row.addEventListener('click', function() {
                            assignMechanic(m.user_id, m.name);
                        });
                    }
                    list.appendChild(row);
                });
            } catch (e) {
                list.innerHTML =
                    '<div style="text-align:center;padding:20px;color:#d63939;font-size:13px;">Failed to load mechanics.</div>';
            }
        }

        async function skipMechanicAndAccept() {
            if (_pickerMode === 'accept' && _pickerRequestId) {
                await doAcceptRequest(_pickerRequestId, null, null);
            }
        }

        async function assignMechanic(userId, name) {
            if (!_pickerRequestId) return;
            if (_pickerMode === 'accept') {
                await doAcceptRequest(_pickerRequestId, userId, name);
                return;
            }
            // Normal assign to an already-accepted job
            try {
                var r = await fetch('/shop/request/' + _pickerRequestId + '/dispatch', {
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
                    var reqId = _pickerRequestId;
                    var pending = _pendingEnRoute === reqId ? reqId : null;
                    closeMechanicPicker();
                    var mrow = document.getElementById('mrow-' + reqId);
                    if (mrow) {
                        mrow.innerHTML =
                            '<i class="fas fa-user-gear" style="color:#667382;font-size:12px;flex-shrink:0;"></i>' +
                            '<span style="font-size:12px;color:#374151;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
                            escHtml(d.mechanic_name || name) + '</span>' +
                            '<button class="btn btn-secondary btn-sm" style="padding:2px 8px;font-size:11px;flex-shrink:0;" onclick="event.stopPropagation();openMechanicPicker(' +
                            reqId + ')">Change</button>';
                    }
                    showToast('Mechanic assigned: ' + escHtml(d.mechanic_name || name), 'success');
                    // Auto-proceed with en_route if triggered by the guard
                    if (pending) {
                        updateRequestStatus(pending, 'en_route');
                    } else {
                        // Reveal En Route button if it was hidden (accepted card with no mechanic)
                        var enBtn = document.getElementById('en-route-btn-' + reqId);
                        if (enBtn && enBtn.style.display === 'none') enBtn.style.display = '';
                    }
                } else {
                    showToast(d.message || 'Assignment failed.', 'error');
                }
            } catch (e) {
                showToast('Network error.', 'error');
            }
        }

        async function doAcceptRequest(reqId, mechanicUserId, mechanicName) {
            var acceptBtn = document.getElementById('popup-accept-btn');
            if (acceptBtn) {
                acceptBtn.disabled = true;
                acceptBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Accepting…';
            }
            var mechList = document.getElementById('popup-mech-list');
            if (mechList) mechList.innerHTML =
                '<div style="text-align:center;padding:16px;color:#667382;font-size:13px;"><i class="fas fa-spinner fa-spin" style="margin-right:6px;"></i>Accepting…</div>';
            var skip = document.getElementById('popup-mech-skip');
            if (skip) skip.disabled = true;
            try {
                var r = await fetch('/shop/accept/' + reqId, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    }
                });
                var d = await r.json();
                if (!d.success) {
                    closeDispatchPopup();
                    showToast(d.taken ? 'Too slow! Another shop already accepted this one.' : (d.message ||
                        'Could not accept.'), d.taken ? 'warning' : 'error', 5000);
                    setTimeout(processPopupQueue, 500);
                    return;
                }
                // Assign mechanic if one was selected
                if (mechanicUserId) {
                    await fetch('/shop/request/' + reqId + '/dispatch', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            mechanic_id: mechanicUserId
                        })
                    });
                }
                var savedReq = _currentPopupReq;
                closeDispatchPopup();
                showToast('Job accepted!' + (mechanicName ? ' Mechanic: ' + escHtml(mechanicName) : ''), 'success',
                    6000);
                adjustCount('stat-active', 1);
                adjustCount('stat-pending', -1);
                var pb = document.getElementById('pending-badge');
                if (pb) {
                    var n = Math.max(0, (parseInt(pb.textContent) || 0) - 1);
                    pb.textContent = n + ' unclaimed';
                    if (n === 0) pb.style.display = 'none';
                }
                addActiveJobCard(reqId, savedReq, mechanicName);
                setTimeout(processPopupQueue, 500);
            } catch (e) {
                closeDispatchPopup();
                showToast('Network error. Try again.', 'error');
            }
        }

        async function loadMechanicsForPopup() {
            var list = document.getElementById('popup-mech-list');
            if (!list) return;
            list.innerHTML =
                '<div style="text-align:center;padding:16px;color:#667382;font-size:13px;"><i class="fas fa-spinner fa-spin" style="margin-right:6px;"></i>Loading mechanics…</div>';
            try {
                var r = await fetch('/shop/mechanics-list', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                var mechanics = await r.json();
                if (!mechanics.length) {
                    list.innerHTML =
                        '<div style="text-align:center;padding:16px;color:#667382;font-size:13px;">No mechanics found. <a href="/shop/mechanics" style="color:#f76707;">Add one →</a></div>';
                    return;
                }
                list.innerHTML = '';
                mechanics.forEach(function(m) {
                    var statusColor = m.status === 'available' ? '#2fb344' : (m.status === 'dispatched' ?
                        '#f76707' : '#667382');
                    var statusLabel = m.status === 'available' ? 'Available' : (m.status === 'dispatched' ?
                        'Dispatched' : 'Off Duty');
                    var isDisabled = m.status === 'off_duty' || m.status === 'dispatched';
                    var row = document.createElement('div');
                    row.id = 'popup-mech-row-' + m.user_id;
                    row.style.cssText =
                        'display:flex;align-items:center;gap:10px;padding:9px 11px;border:1.5px solid #e6e7eb;border-radius:8px;cursor:pointer;transition:all .15s;' +
                        (isDisabled ? 'opacity:.5;pointer-events:none;' : '');
                    row.innerHTML =
                        '<div style="width:34px;height:34px;border-radius:50%;background:#f0f1f3;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-user-gear" style="color:#667382;font-size:13px;"></i></div>' +
                        '<div style="flex:1;min-width:0;"><div style="font-size:13px;font-weight:600;color:#1d273b;">' +
                        escHtml(m.name || 'Mechanic') + '</div>' +
                        (m.phone ? '<div style="font-size:11px;color:#667382;">' + escHtml(m.phone) + '</div>' :
                            '') + '</div>' +
                        '<span style="font-size:11px;font-weight:600;color:' + statusColor + ';background:' +
                        statusColor + '1a;padding:2px 8px;border-radius:10px;">' + statusLabel + '</span>';
                    if (!isDisabled) {
                        row.addEventListener('click', function() {
                            selectPopupMechanic(m.user_id, m.name);
                        });
                    }
                    list.appendChild(row);
                });
            } catch (e) {
                list.innerHTML =
                    '<div style="text-align:center;padding:16px;color:#d63939;font-size:13px;">Failed to load mechanics.</div>';
            }
        }

        function selectPopupMechanic(id, name) {
            _popupSelectedMechId = id;
            _popupSelectedMechName = name;
            document.querySelectorAll('[id^="popup-mech-row-"]').forEach(function(r) {
                r.style.borderColor = '#e6e7eb';
                r.style.background = '';
            });
            if (id) {
                var sel = document.getElementById('popup-mech-row-' + id);
                if (sel) {
                    sel.style.borderColor = '#2fb344';
                    sel.style.background = '#f0fdf4';
                }
            }
            var skip = document.getElementById('popup-mech-skip');
            if (skip) skip.style.color = id ? '#667382' : '#2fb344';
        }

        function addActiveJobCard(reqId, req, mechanicName) {
            var container = document.getElementById('active-jobs-container');
            if (!container) return;
            // Remove empty-state placeholder
            var empty = container.querySelector('.t-card');
            if (empty) empty.remove();
            var motoristName = escHtml((req && (req.owner_name || req.motorist_name || req.guest_name)) ||
                'Unknown Motorist');
            var issueType = escHtml((req && req.issue_type) || 'Job');
            var mechInner = mechanicName ?
                '<i class="fas fa-user-gear" style="color:#667382;font-size:12px;flex-shrink:0;"></i>' +
                '<span style="font-size:12px;color:#374151;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
                escHtml(mechanicName) + '</span>' +
                '<button class="btn btn-secondary btn-sm" style="padding:2px 8px;font-size:11px;flex-shrink:0;" onclick="event.stopPropagation();openMechanicPicker(' +
                reqId + ')">Change</button>' :
                '<button class="btn btn-secondary btn-sm" style="width:100%;justify-content:center;" onclick="event.stopPropagation();openMechanicPicker(' +
                reqId + ')"><i class="fas fa-user-plus" style="margin-right:5px;"></i>Assign Mechanic</button>';
            var card = document.createElement('div');
            card.className = 'req-card open';
            card.id = 'req-' + reqId;
            card.innerHTML = '<div class="req-head" onclick="toggleCard(this)">' +
                '<div class="req-info">' +
                '<div class="req-title" style="font-size:13px;">' + issueType + '</div>' +
                '<div class="req-meta">' + motoristName + '</div>' +
                '</div>' +
                '<span class="sbadge badge-warning">ACCEPTED</span>' +
                '<i class="fas fa-chevron-down req-chev"></i>' +
                '</div>' +
                '<div class="req-body">' +
                '<div class="req-div"></div>' +
                '<div class="mechanic-row" id="mrow-' + reqId +
                '" style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">' + mechInner + '</div>' +
                '<button id="en-route-btn-' + reqId +
                '" class="btn btn-primary btn-sm" style="width:100%;justify-content:center;' + (mechanicName ? '' :
                    'display:none;') + '" onclick="updateRequestStatus(' +
                reqId + ', \'en_route\')">Mechanic En Route</button>' +
                '</div>';
            container.insertBefore(card, container.firstChild);
            adjustCount('active-badge', 1);
        }
        // ─────────────────────────────────────────────────────────

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
            subscribeWhenReady(0);
            // Auto-queue any pending requests that exist at page load
            @if ($pending > 0)
                loadUnclaimedRequests();
            @endif
        });

        function subscribeWhenReady(attempts) {
            if (window.Echo) {
                // Public channel — all open shop dashboards receive new dispatch requests
                window.Echo.channel('shop-requests').listen('.dispatch.new', function(req) {
                    queueDispatchPopup(req);
                    adjustCount('stat-pending', 1);
                    var pb = document.getElementById('pending-badge');
                    if (pb) {
                        var n = (parseInt(pb.textContent) || 0) + 1;
                        pb.textContent = n + ' unclaimed';
                        pb.style.display = '';
                    }
                });
            } else if (attempts < 30) {
                setTimeout(function() {
                    subscribeWhenReady(attempts + 1);
                }, 200);
            }
        }

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

@section('body_after')
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                try {
                    showToast(@json(session('success')), 'success');
                } catch (e) {
                    console.log('Toast failed:', e);
                }
            });
        </script>
    @endif

    {{-- Combined Dispatch + Mechanic Picker Popup --}}
    <div id="dispatch-popup"
        style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,.55); align-items:center; justify-content:center; padding:16px;">
        <div
            style="width:100%; max-width:420px; background:#fff; border-radius:16px; box-shadow:0 24px 64px rgba(0,0,0,.25); overflow:hidden; max-height:90vh; display:flex; flex-direction:column;">
            {{-- Header --}}
            <div style="background:#d63939; padding:16px 20px; display:flex; align-items:center; gap:12px; flex-shrink:0;">
                <div
                    style="width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas fa-motorcycle" style="color:#fff; font-size:18px;"></i>
                </div>
                <div style="flex:1; min-width:0;">
                    <div
                        style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:rgba(255,255,255,.75);">
                        New Rescue Request</div>
                    <div id="popup-issue"
                        style="font-size:17px;font-weight:700;color:#fff; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    </div>
                </div>
                <div id="popup-timer-badge"
                    style="background:rgba(0,0,0,.25);color:#fff;font-size:13px;font-weight:800;border-radius:20px;padding:5px 12px;flex-shrink:0;min-width:44px;text-align:center;">
                    60s</div>
            </div>
            {{-- Scrollable body --}}
            <div style="overflow-y:auto; flex:1; padding:16px 20px 0;">
                <div id="popup-details"
                    style="background:#f4f6fb;border-radius:10px;padding:14px;font-size:13px;line-height:1.9;">
                </div>
                {{-- Mechanic selection --}}
                <div style="margin-top:12px;">
                    <div
                        style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#667382;margin-bottom:8px;">
                        Select Mechanic</div>
                    <div id="popup-mech-list" style="display:flex;flex-direction:column;gap:6px;">
                        <div style="text-align:center;padding:16px;color:#667382;font-size:13px;">
                            <i class="fas fa-spinner fa-spin" style="margin-right:6px;"></i>Loading mechanics…
                        </div>
                    </div>
                    <button id="popup-mech-skip" onclick="selectPopupMechanic(null,null)"
                        style="margin-top:8px;width:100%;padding:9px;border:1px dashed #cdd1d9;background:none;border-radius:8px;color:#667382;font-size:12px;cursor:pointer;">
                        <i class="fas fa-forward" style="margin-right:5px;"></i>Skip — Accept without assigning a mechanic
                    </button>
                </div>
            </div>
            {{-- Footer buttons --}}
            <div style="padding:16px 20px 20px; flex-shrink:0; display:flex; gap:10px;">
                <button id="popup-accept-btn" onclick="acceptPopupRequest()"
                    style="flex:2;background:#2fb344;color:#fff;border:none;border-radius:10px;padding:14px;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:7px;">
                    <i class="fas fa-check"></i> Accept Job
                </button>
                <button onclick="declinePopupRequest()"
                    style="flex:1;background:transparent;color:#d63939;border:1.5px solid #d63939;border-radius:10px;padding:14px;font-size:14px;font-weight:700;cursor:pointer;">
                    Pass
                </button>
            </div>
        </div>
    </div>
@endsection
