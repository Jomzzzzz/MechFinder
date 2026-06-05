@extends('layouts.mechanic-mobile')

@section('main-class', '')

@section('content')
    <style>
        :root {
            --nav-h: 60px;
            --brand: #F7941D;
            --surface: #FFFFFF;
            --text-1: #111827;
            --text-2: #6B7280;
            --text-3: #9CA3AF;
            --border: #E4E7EC;
            --sh-float: 0 4px 18px rgba(0, 0, 0, .12);
            --sh-card: 0 1px 4px rgba(0, 0, 0, .08);
        }

        #mfApp {
            height: 100svh;
            height: 100dvh;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .mechanic-content {
            flex: 1;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            padding: 12px 14px calc(var(--nav-h) + 12px);
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .job-card {
            background: white;
            border-radius: 12px;
            padding: 12px;
            border: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            gap: 10px;
            box-shadow: var(--sh-card);
            transition: box-shadow .15s ease, border-color .15s ease;
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
            font-size: 16px;
            font-weight: 700;
            color: var(--text-1);
            margin: 0;
        }

        .job-meta {
            font-size: 12px;
            color: var(--text-2);
            margin: 4px 0 0;
        }

        .status-indicator {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .status-requested {
            background: rgba(247, 148, 29, .15);
            color: #C87010;
        }

        .status-accepted {
            background: rgba(59, 130, 246, .15);
            color: #1E40AF;
        }

        .status-enroute {
            background: rgba(202, 138, 4, .15);
            color: #854D0E;
        }

        .status-arrived {
            background: rgba(34, 197, 94, .15);
            color: #15803D;
        }

        .status-completed {
            background: rgba(16, 185, 129, .15);
            color: #047857;
        }

        .timeline {
            display: flex;
            align-items: center;
            gap: 0;
            padding: 8px 0;
            font-size: 11px;
            color: var(--text-2);
            transition: all .15s ease;
        }

        .timeline-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        .timeline-item:not(:last-child)::after {
            content: '';
            position: absolute;
            right: -50%;
            top: 10px;
            width: 100%;
            height: 2px;
            background: #E5E7EB;
            transition: background .3s ease;
        }

        .timeline-item.active:not(:last-child)::after {
            background: var(--brand);
        }

        .timeline-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: white;
            border: 2px solid #D1D5DB;
            margin-bottom: 4px;
            position: relative;
            z-index: 1;
            transition: background .2s ease, border-color .2s ease;
        }

        .timeline-item.active .timeline-dot {
            background: var(--brand);
            border-color: var(--brand);
            box-shadow: 0 0 0 2px rgba(247, 148, 29, .1);
        }

        .timeline-label {
            text-align: center;
            width: 100%;
            line-height: 1.2;
        }

        .status-message {
            background: rgba(247, 148, 29, .08);
            border-left: 3px solid var(--brand);
            padding: 8px 10px;
            border-radius: 6px;
            font-size: 12px;
            color: var(--text-1);
            font-weight: 500;
            line-height: 1.4;
            transition: background .15s ease;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 8px 12px;
            border-radius: 8px;
            border: none;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            transition: all .15s ease;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            -webkit-tap-highlight-color: transparent;
        }

        .action-btn-primary {
            background: var(--brand);
            color: white;
        }

        .action-btn-primary:active {
            background: #C87010;
            transform: scale(.96);
        }

        .action-btn-secondary {
            background: var(--border);
            color: var(--text-1);
        }

        .action-btn-secondary:active {
            background: #D1D5DB;
            transform: scale(.96);
        }

        .no-jobs {
            display: block;
            padding: 48px 0 18px;
            text-align: center;
            color: var(--text-2);
        }

        .no-jobs-icon {
            font-size: 48px;
            margin-bottom: 12px;
            opacity: .5;
        }

        .no-jobs-text {
            font-size: 14px;
            color: var(--text-2);
            margin: 0;
            line-height: 1.5;
        }

        .top-header {
            background: white;
            border-bottom: 1px solid var(--border);
            padding: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: var(--sh-card);
        }

        .top-header-title {
            flex: 1;
        }

        .top-header-title h1 {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-1);
            margin: 0;
            line-height: 1.2;
        }

        .top-header-subtitle {
            font-size: 11px;
            color: var(--text-3);
            margin: 2px 0 0;
            letter-spacing: 0.3px;
        }

        .header-icon-btn {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--brand);
            color: white;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            cursor: pointer;
            flex-shrink: 0;
            transition: all .15s;
        }

        .header-icon-btn:active {
            background: #C87010;
            transform: scale(.94);
        }

        /* ─── BOTTOM NAVIGATION ─── */
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
            box-shadow: 0 -1px 3px rgba(0, 0, 0, .05);
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
            position: relative;
            text-decoration: none;
            padding: 0;
            -webkit-user-select: none;
            user-select: none;
        }

        .nav-btn.active {
            color: var(--brand);
        }

        .n-icon {
            font-size: 20px;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .n-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            line-height: 1.1;
        }

        .nav-badge {
            position: absolute;
            top: 10px;
            right: calc(50% - 15px);
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--brand);
            border: 2px solid var(--surface);
            display: none;
            z-index: 1;
            animation: badgePulse .6s ease-in-out infinite;
        }

        .nav-badge.show {
            display: block;
        }

        @keyframes badgePulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.2); opacity: .8; }
        }

        @media (max-height: 680px) {
            .job-card {
                padding: 10px;
            }
            .timeline {
                padding: 6px 0;
            }
        }
    </style>

    <div id="mfApp">
        <div class="top-header">
            <div class="top-header-title">
                <h1>My Jobs</h1>
                <p class="top-header-subtitle">{{ $mechanic->name }}</p>
            </div>
        </div>

        <div class="mechanic-content">
        @if ($jobs->isEmpty())
            <div class="no-jobs">
                <div class="no-jobs-icon">🛠️</div>
                <p class="no-jobs-text">No jobs assigned yet. Wait for your shop manager to dispatch you.</p>
            </div>
        @else
            @foreach ($jobs as $job)
                @php
                    $req = $job->dispatchRequest;
                    $status = $req->status;
                    $motoristName = $req->motorist->name ?? $req->guest_name ?? 'Unknown';
                    
                    // Status timeline stages
                    $stages = ['requested', 'accepted', 'en_route', 'arrived', 'completed'];
                    $currentIndex = array_search($status, $stages);
                    
                    // Status message
                    $statusMessages = [
                        'requested' => 'Repair request sent',
                        'accepted' => 'You accepted the job',
                        'en_route' => 'You\'re on the way',
                        'arrived' => 'You have arrived',
                        'completed' => 'Job completed'
                    ];
                    $statusMsg = $statusMessages[$status] ?? ucfirst(str_replace('_', ' ', $status));
                    
                    // Get timestamp
                    $createdAt = $req->created_at;
                    $timeAgo = $createdAt->diffForHumans();
                    
                    $statusClass = 'status-' . str_replace('_', '', $status);
                @endphp

                <div class="job-card" data-dispatch-id="{{ $req->id }}">
                    <div class="job-header">
                        <div class="job-title">
                            <h3>{{ $req->issue_type ?? 'Motorcycle Repair' }}</h3>
                            <p class="job-meta">{{ $timeAgo }} • {{ $req->shop->shop_name ?? 'Shop' }}</p>
                        </div>
                        <span class="status-indicator {{ $statusClass }}">
                            {{ strtoupper(str_replace('_', ' ', $status)) }}
                        </span>
                    </div>

                    <div class="timeline">
                        @foreach ($stages as $stage)
                            @php
                                $stageIndex = array_search($stage, $stages);
                                $isActive = $stageIndex <= $currentIndex;
                            @endphp
                            <div class="timeline-item @if ($isActive) active @endif">
                                <div class="timeline-dot"></div>
                                <div class="timeline-label">
                                    {{ ucfirst(str_replace('_', ' ', $stage)) }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="status-message">
                        {{ $statusMsg }}
                    </div>

                    <div class="job-actions" style="display: flex; gap: 6px; flex-wrap: wrap;">
                        @if ($req->latitude && $req->longitude)
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $req->latitude }},{{ $req->longitude }}"
                                target="_blank" class="action-btn action-btn-secondary">
                                <i class="fas fa-map"></i> Maps
                            </a>
                        @endif

                        @if ($status === 'accepted' || $status === 'requested')
                            <button onclick="updateStatus({{ $req->id }}, 'en_route')"
                                class="action-btn action-btn-primary">
                                <i class="fas fa-car-side"></i> En Route
                            </button>
                        @elseif ($status === 'en_route')
                            <button onclick="updateStatus({{ $req->id }}, 'arrived')"
                                class="action-btn action-btn-primary">
                                <i class="fas fa-map-pin"></i> Arrived
                            </button>
                        @elseif ($status === 'arrived')
                            <button onclick="updateStatus({{ $req->id }}, 'completed')"
                                class="action-btn action-btn-primary">
                                <i class="fas fa-check-circle"></i> Complete
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif

        <nav id="bottomNav">
            <a href="{{ route('mechanic.dashboard') }}" class="nav-btn {{ request()->routeIs('mechanic.dashboard') ? 'active' : '' }}" title="Jobs">
                <span class="n-icon"><i class="fas fa-briefcase"></i></span>
                <span class="n-label">Jobs</span>
            </a>
            <a href="{{ route('mechanic.profile') }}" class="nav-btn {{ request()->routeIs('mechanic.profile') ? 'active' : '' }}" title="Profile">
                <span class="n-icon"><i class="fas fa-user"></i></span>
                <span class="n-label">Profile</span>
            </a>
        </nav>
    </div>

    <script>
        async function updateStatus(requestId, status) {
            if (!confirm(`Update status to ${status.replace('_', ' ').toUpperCase()}?`)) {
                return;
            }

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            try {
                const response = await fetch(`/mechanic/request/${requestId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status })
                });

                const data = await response.json();
                if (response.ok && data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Unable to update status');
                }
            } catch (error) {
                alert('Network error. Please try again.');
            }
        }
    </script>
@endsection