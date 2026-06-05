@extends('layouts.mechanic-mobile')

@section('main-class', '')

@section('content')
    <style>
        :root {
            --nav-h: 60px;
            --brand: #F7941D;
            --brand-dk: #C87010;
            --brand-bg: rgba(247, 148, 29, .09);
            --surface: #FFFFFF;
            --surface-2: #F5F6F8;
            --border: #E4E7EC;
            --border-2: #CDD1D9;
            --text-1: #111827;
            --text-2: #6B7280;
            --text-3: #9CA3AF;
            --red: #EF4444;
            --green: #10B981;
            --action: #1E293B;
            --action-bg: rgba(30, 41, 59, .06);
            --r1: 6px;
            --r2: 10px;
            --r3: 14px;
            --sh-card: 0 1px 4px rgba(0, 0, 0, .08);
            --sh-float: 0 4px 18px rgba(0, 0, 0, .12);
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        #mfApp {
            height: 100svh;
            height: 100dvh;
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(circle at top, rgba(247, 148, 29, .12), transparent 28%),
                linear-gradient(180deg, #11141b 0%, #0b0f14 40%, #050505 100%);
            font-family: Inter, system-ui, -apple-system, sans-serif;
            -webkit-font-smoothing: antialiased;
            display: flex;
            flex-direction: column;
        }

        .top-header {
            flex-shrink: 0;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .top-header-logo {
            width: 36px;
            height: 36px;
            border-radius: var(--r1);
            background: var(--brand);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            color: #fff;
            flex-shrink: 0;
        }

        .top-header-title {
            flex: 1;
        }

        .top-header-title h1 {
            font-size: 17px;
            font-weight: 800;
            color: var(--text-1);
            margin: 0;
        }

        .top-header-sub {
            font-size: 11px;
            color: var(--text-3);
            margin: 2px 0 0;
        }

        .mechanic-content {
            flex: 1;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            padding: 12px 14px calc(var(--nav-h) + 12px);
            display: flex;
            flex-direction: column;
            gap: 10px;
            background: var(--surface-2);
        }

        .job-card {
            background: var(--surface);
            border-radius: var(--r3);
            padding: 14px;
            border: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            gap: 10px;
            box-shadow: var(--sh-card);
        }

        .job-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 8px;
        }

        .job-title {
            flex: 1;
        }

        .job-title h3 {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-1);
            margin: 0;
        }

        .job-meta {
            font-size: 11px;
            color: var(--text-2);
            margin: 3px 0 0;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 9px;
            border-radius: 99px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .status-badge.accepted {
            background: rgba(59, 130, 246, .12);
            color: #1E40AF;
        }

        .status-badge.en_route {
            background: rgba(202, 138, 4, .12);
            color: #854D0E;
        }

        .status-badge.arrived {
            background: rgba(16, 185, 129, .12);
            color: #047857;
        }

        .status-badge.completed {
            background: rgba(16, 185, 129, .18);
            color: #065F46;
        }

        .status-badge.requested {
            background: var(--brand-bg);
            color: var(--brand-dk);
        }

        .req-steps {
            display: flex;
            align-items: center;
            margin: 2px 0;
        }

        .rs-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            flex-shrink: 0;
            background: var(--border-2);
            transition: background .3s;
        }

        .rs-dot.done {
            background: var(--brand);
        }

        .rs-dot.active {
            background: var(--brand);
            box-shadow: 0 0 0 3px var(--brand-bg);
        }

        .rs-line {
            flex: 1;
            height: 2px;
            background: var(--border);
            transition: background .3s;
        }

        .rs-line.done {
            background: var(--brand);
        }

        .status-strip {
            background: var(--brand-bg);
            border-left: 3px solid var(--brand);
            border-radius: var(--r1);
            padding: 8px 11px;
            font-size: 12px;
            font-weight: 600;
            color: var(--brand-dk);
            line-height: 1.4;
        }

        .job-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .act-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 9px 14px;
            border-radius: var(--r2);
            border: none;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all .15s;
            text-decoration: none;
            letter-spacing: .02em;
            -webkit-tap-highlight-color: transparent;
        }

        .act-btn:active {
            transform: scale(.96);
        }

        .act-btn-primary {
            background: var(--brand);
            color: #fff;
            flex: 1;
        }

        .act-btn-primary:active {
            background: var(--brand-dk);
        }

        .act-btn-secondary {
            background: var(--surface-2);
            color: var(--text-1);
            border: 1px solid var(--border);
        }

        /* ── JOB MAP ── */
        .job-map-wrap {
            position: relative;
            display: block;
            border-radius: var(--r2);
            overflow: hidden;
            border: 1px solid var(--border);
            text-decoration: none;
        }

        .job-map {
            width: 100%;
            height: 160px;
            background: var(--surface-2);
            pointer-events: none;
        }

        /* dir-btn — matches motorist UI */
        .job-map-dir {
            position: absolute;
            bottom: 10px;
            right: 10px;
            z-index: 2;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--surface);
            box-shadow: 0 2px 8px rgba(0, 0, 0, .18);
            color: var(--action);
            border: 1px solid rgba(30, 41, 59, .12);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
            pointer-events: all;
        }

        .job-map-dir:active {
            background: var(--surface-2);
        }

        /* mf-pin — identical to motorist */
        .mf-pin {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .22);
        }

        .mf-pin-dest {
            background: var(--brand);
            color: #fff;
            border: 2.5px solid #fff;
        }

        .mf-pin-mech {
            background: #3B82F6;
            color: #fff;
            border: 2.5px solid #fff;
        }

        /* Leaflet overrides — matches motorist */
        .leaflet-popup-content-wrapper {
            background: var(--surface);
            color: var(--text-1);
            border-radius: var(--r2);
            box-shadow: 0 4px 18px rgba(0, 0, 0, .12);
            border: 1px solid var(--border);
        }

        .leaflet-popup-tip {
            background: var(--surface);
        }

        .leaflet-popup-content {
            margin: 10px 12px;
            font-family: Inter, sans-serif;
        }

        .no-jobs {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 56px 20px 20px;
            text-align: center;
            gap: 12px;
        }

        .no-jobs-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: var(--brand-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
        }

        .no-jobs-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-1);
        }

        .no-jobs-text {
            font-size: 13px;
            color: var(--text-2);
            line-height: 1.6;
            max-width: 280px;
        }

        #bottomNav {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: var(--nav-h);
            z-index: 40;
            background: var(--surface);
            border-top: 1px solid var(--border);
            display: flex;
            align-items: stretch;
            -webkit-tap-highlight-color: transparent;
        }

        .nav-btn {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            cursor: pointer;
            color: var(--text-3);
            background: none;
            border: none;
            transition: color .15s;
            -webkit-tap-highlight-color: transparent;
            text-decoration: none;
            padding: 0;
        }

        .nav-btn.active {
            color: var(--brand);
        }

        .n-icon {
            font-size: 20px;
            line-height: 1;
        }

        .n-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        #confirmModal {
            position: absolute;
            inset: 0;
            z-index: 60;
            background: rgba(0, 0, 0, .45);
            display: flex;
            align-items: flex-end;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s ease;
        }

        #confirmModal.show {
            opacity: 1;
            pointer-events: all;
        }

        .cm-sheet {
            background: var(--surface);
            border-radius: var(--r3) var(--r3) 0 0;
            width: 100%;
            padding: 22px 20px 36px;
            transform: translateY(100%);
            transition: transform .25s cubic-bezier(.4, 0, .2, 1);
        }

        #confirmModal.show .cm-sheet {
            transform: translateY(0);
        }

        .cm-handle {
            width: 36px;
            height: 4px;
            border-radius: 99px;
            background: var(--border);
            margin: 0 auto 18px;
        }

        .cm-title {
            font-size: 16px;
            font-weight: 800;
            color: var(--text-1);
            text-align: center;
            margin-bottom: 6px;
        }

        .cm-body {
            font-size: 13px;
            color: var(--text-2);
            text-align: center;
            line-height: 1.6;
            margin-bottom: 22px;
        }

        .cm-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .cm-ok {
            width: 100%;
            padding: 14px;
            background: var(--brand);
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            border-radius: var(--r2);
            border: none;
            cursor: pointer;
        }

        .cm-cancel {
            width: 100%;
            padding: 13px;
            background: var(--surface-2);
            color: var(--text-1);
            font-size: 15px;
            font-weight: 600;
            border-radius: var(--r2);
            border: 1px solid var(--border);
            cursor: pointer;
        }

        #mfToast {
            position: absolute;
            bottom: calc(var(--nav-h) + 14px);
            left: 50%;
            transform: translateX(-50%) translateY(12px);
            background: var(--action);
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 99px;
            white-space: nowrap;
            z-index: 999;
            opacity: 0;
            transition: opacity .25s, transform .25s;
            pointer-events: none;
        }

        #mfToast.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        #mfToast.success {
            background: var(--green);
        }

        #mfToast.error {
            background: var(--red);
        }
    </style>

    <div id="mfApp">

        <div class="top-header">
            <div class="top-header-logo"><i class="fas fa-wrench"></i></div>
            <div class="top-header-title">
                <h1>My Jobs</h1>
                <p class="top-header-sub">{{ $mechanic->name }}</p>
            </div>
        </div>

        <div class="mechanic-content">

            @if ($jobs->isEmpty())
                <div class="no-jobs">
                    <div class="no-jobs-icon">🛠️</div>
                    <p class="no-jobs-title">No jobs yet</p>
                    <p class="no-jobs-text">Wait for your shop manager to dispatch you to a job.</p>
                </div>
            @else
                @foreach ($jobs as $job)
                    @php
                        $req = $job->dispatchRequest;
                        $status = $req->status;
                        $mName = $req->motorist->name ?? ($req->guest_name ?? 'Unknown Motorist');
                        $stages = ['requested', 'accepted', 'en_route', 'arrived', 'completed'];
                        $curIdx = array_search($status, $stages);
                        $msgs = [
                            'requested' => 'Awaiting mechanic dispatch',
                            'accepted' => 'Job accepted — get ready',
                            'en_route' => "You're on the way",
                            'arrived' => 'You have arrived at location',
                            'completed' => 'Job completed ✓',
                        ];
                    @endphp

                    <div class="job-card">
                        <div class="job-header">
                            <div class="job-title">
                                <h3>{{ $req->issue_type ?? 'Motorcycle Repair' }}</h3>
                                <p class="job-meta">
                                    {{ $req->created_at->diffForHumans() }}
                                    &bull; {{ $req->shop->shop_name ?? 'Shop' }}
                                </p>
                            </div>
                            <span class="status-badge {{ $status }}">
                                {{ strtoupper(str_replace('_', ' ', $status)) }}
                            </span>
                        </div>

                        <div class="req-steps">
                            @foreach ($stages as $i => $stage)
                                <div class="rs-dot {{ $i < $curIdx ? 'done' : ($i === $curIdx ? 'active' : '') }}"></div>
                                @if (!$loop->last)
                                    <div class="rs-line {{ $i < $curIdx ? 'done' : '' }}"></div>
                                @endif
                            @endforeach
                        </div>

                        <div class="status-strip">
                            {{ $msgs[$status] ?? ucfirst(str_replace('_', ' ', $status)) }}
                        </div>

                        <div style="font-size:12px; color:var(--text-2); display:flex; align-items:center; gap:6px;">
                            <i class="fas fa-user" style="color:var(--text-3); font-size:11px;"></i>
                            {{ $mName }}
                            @if ($req->location)
                                &bull; <i class="fas fa-map-marker-alt" style="color:var(--text-3); font-size:11px;"></i>
                                {{ Str::limit($req->location, 40) }}
                            @endif
                        </div>

                        @if ($req->latitude && $req->longitude)
                            <div class="job-map-wrap">
                                <div class="job-map" id="map-{{ $req->id }}" data-lat="{{ $req->latitude }}"
                                    data-lng="{{ $req->longitude }}"></div>
                                <button class="job-map-dir"
                                    onclick="getDirections({{ $req->latitude }}, {{ $req->longitude }})"
                                    title="Get Directions">
                                    <i class="fa-solid fa-location-arrow"></i>
                                </button>
                            </div>
                        @endif

                        <div class="job-actions">
                            @if ($req->latitude && $req->longitude)
                                <button onclick="getDirections({{ $req->latitude }}, {{ $req->longitude }})"
                                    class="act-btn act-btn-secondary">
                                    <i class="fa-solid fa-location-arrow"></i> Directions
                                </button>
                            @endif

                            @if ($status === 'accepted' || $status === 'requested')
                                <button onclick="updateStatus({{ $req->id }}, 'en_route')"
                                    class="act-btn act-btn-primary">
                                    <i class="fas fa-car-side"></i> En Route
                                </button>
                            @elseif ($status === 'en_route')
                                <button onclick="updateStatus({{ $req->id }}, 'arrived')"
                                    class="act-btn act-btn-primary">
                                    <i class="fas fa-map-pin"></i> Arrived
                                </button>
                            @elseif ($status === 'arrived')
                                <button onclick="updateStatus({{ $req->id }}, 'completed')"
                                    class="act-btn act-btn-primary">
                                    <i class="fas fa-check-circle"></i> Complete Job
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif

        </div>

        <nav id="bottomNav">
            <a href="{{ route('mechanic.dashboard') }}"
                class="nav-btn {{ request()->routeIs('mechanic.dashboard') ? 'active' : '' }}">
                <span class="n-icon"><i class="fas fa-briefcase"></i></span>
                <span class="n-label">Jobs</span>
            </a>
            <a href="{{ route('mechanic.profile') }}"
                class="nav-btn {{ request()->routeIs('mechanic.profile') ? 'active' : '' }}">
                <span class="n-icon"><i class="fas fa-user"></i></span>
                <span class="n-label">Profile</span>
            </a>
        </nav>

        <div id="confirmModal">
            <div class="cm-sheet">
                <div class="cm-handle"></div>
                <p class="cm-title" id="cm-title"></p>
                <p class="cm-body" id="cm-body"></p>
                <div class="cm-actions">
                    <button class="cm-ok" id="cm-ok">Confirm</button>
                    <button class="cm-cancel" id="cm-cancel">Cancel</button>
                </div>
            </div>
        </div>

        <div id="mfToast"></div>

    </div>

    <script>
        function mfToast(msg, type) {
            const el = document.getElementById('mfToast');
            el.textContent = msg;
            el.className = type || '';
            el.classList.add('show');
            setTimeout(() => {
                el.className = '';
            }, 2600);
        }

        function mfConfirm(title, body, onOk) {
            const modal = document.getElementById('confirmModal');
            document.getElementById('cm-title').textContent = title;
            document.getElementById('cm-body').textContent = body;
            modal.classList.add('show');
            const close = () => modal.classList.remove('show');
            document.getElementById('cm-ok').onclick = () => {
                close();
                onOk();
            };
            document.getElementById('cm-cancel').onclick = close;
            modal.onclick = (e) => {
                if (e.target === modal) close();
            };
        }

        function updateStatus(id, newStatus) {
            const labels = {
                en_route: 'En Route',
                arrived: 'Arrived',
                completed: 'Complete'
            };
            const label = labels[newStatus] || newStatus;
            mfConfirm(
                'Update Job Status',
                'Mark this job as "' + label + '"?',
                async () => {
                    const token = document.querySelector('meta[name="csrf-token"]').content;
                    try {
                        const res = await fetch('/mechanic/request/' + id + '/status', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                status: newStatus
                            })
                        });
                        const data = await res.json();
                        if (res.ok && data.success) {
                            mfToast('Status updated to ' + label + '.', 'success');
                            setTimeout(() => location.reload(), 900);
                        } else {
                            mfToast(data.message || 'Unable to update status.', 'error');
                        }
                    } catch (e) {
                        mfToast('Network error. Please try again.', 'error');
                    }
                }
            );
        }

        /* ── LEAFLET ROUTE MAPS — matches motorist UI ── */
        function getDirections(destLat, destLng) {
            const url = `https://www.google.com/maps/dir/?api=1&destination=${destLat},${destLng}&travelmode=driving`;
            window.open(url, '_blank');
        }

        function initJobMaps(mechLat, mechLng) {
            document.querySelectorAll('.job-map').forEach(el => {
                const destLat = parseFloat(el.dataset.lat);
                const destLng = parseFloat(el.dataset.lng);
                if (isNaN(destLat) || isNaN(destLng)) return;

                const map = L.map(el, {
                    zoomControl: false,
                    attributionControl: false,
                    dragging: false,
                    scrollWheelZoom: false,
                    doubleClickZoom: false,
                    touchZoom: false
                });

                // Carto Voyager — same as motorist
                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                    maxZoom: 19,
                    subdomains: 'abcd',
                    crossOrigin: true
                }).addTo(map);

                L.control.attribution({
                    prefix: '© OpenStreetMap',
                    position: 'bottomleft'
                }).addTo(map);

                // Destination pin — orange mf-pin-dest (motorist's mf-pin-open style)
                const destIcon = L.divIcon({
                    className: '',
                    html: '<div class="mf-pin mf-pin-dest"><i class="fa-solid fa-motorcycle"></i></div>',
                    iconSize: [34, 34],
                    iconAnchor: [17, 17]
                });
                L.marker([destLat, destLng], {
                    icon: destIcon
                }).addTo(map);

                if (mechLat !== null && mechLng !== null) {
                    // Mechanic pin — blue mf-pin-mech (motorist's mechanic marker style)
                    const mechIcon = L.divIcon({
                        className: '',
                        html: '<div class="mf-pin mf-pin-mech"><i class="fa-solid fa-user-gear"></i></div>',
                        iconSize: [34, 34],
                        iconAnchor: [17, 17]
                    });
                    L.marker([mechLat, mechLng], {
                        icon: mechIcon
                    }).addTo(map);

                    // OSRM route — blue #3B82F6 weight 5, same as motorist plotRouteToShop
                    const osrmUrl =
                        `https://router.project-osrm.org/route/v1/driving/${mechLng},${mechLat};${destLng},${destLat}?overview=full&geometries=geojson`;
                    fetch(osrmUrl)
                        .then(r => r.json())
                        .then(data => {
                            if (data.code === 'Ok' && data.routes.length) {
                                const layer = L.geoJSON(data.routes[0].geometry, {
                                    style: {
                                        color: '#3B82F6',
                                        weight: 5,
                                        opacity: .9,
                                        lineJoin: 'round',
                                        lineCap: 'round'
                                    }
                                }).addTo(map);
                                map.fitBounds(layer.getBounds().pad(0.18));
                            } else {
                                map.fitBounds([
                                    [mechLat, mechLng],
                                    [destLat, destLng]
                                ], {
                                    padding: [22, 22]
                                });
                            }
                        })
                        .catch(() => map.fitBounds([
                            [mechLat, mechLng],
                            [destLat, destLng]
                        ], {
                            padding: [22, 22]
                        }));
                } else {
                    map.setView([destLat, destLng], 15);
                }
            });
        }

        // Get mechanic's current position then init maps
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                pos => initJobMaps(pos.coords.latitude, pos.coords.longitude),
                () => initJobMaps(null, null), {
                    timeout: 8000,
                    maximumAge: 60000
                }
            );
        } else {
            initJobMaps(null, null);
        }
    </script>

@endsection
