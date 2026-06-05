@extends('layouts.mechanic-mobile')

@section('content')

<style>
    :root {
        --nav-h: 60px;
        --brand: #F7941D;
        --brand-dk: #C87010;
        --surface: #FFFFFF;
        --surface-2: #F5F6F8;
        --border: #E4E7EC;
        --text-1: #111827;
        --text-2: #6B7280;
        --text-3: #9CA3AF;
        --action: #1E293B;
        --action-2: #2D3F55;
        --action-bg: rgba(30, 41, 59, .06);
        --r1: 6px;
        --r2: 10px;
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

    .map-container {
        flex: 1;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        padding: 14px 14px 90px;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .prof-hero {
        background: linear-gradient(145deg, var(--action) 0%, var(--action-2) 100%);
        padding: 24px 18px 22px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        border-radius: 22px;
        box-shadow: var(--sh-card);
        color: #fff;
    }

    .prof-avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: var(--brand);
        color: #fff;
        font-size: 24px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid rgba(255, 255, 255, .22);
    }

    .prof-hero-name {
        font-size: 17px;
        font-weight: 700;
        color: #fff;
        text-align: center;
    }

    .prof-hero-email {
        font-size: 12px;
        color: rgba(255, 255, 255, .7);
        margin-top: 1px;
        text-align: center;
    }

    .prof-hero-badge {
        margin-top: 6px;
        padding: 4px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .16);
        font-size: 11px;
        font-weight: 700;
        color: rgba(255, 255, 255, .88);
        letter-spacing: .04em;
    }

    .search-card,
    .map-panel,
    .map-info,
    .map-list {
        background: var(--surface);
        border-radius: 22px;
        border: 1px solid var(--border);
        box-shadow: var(--sh-card);
    }

    .search-card {
        padding: 14px;
    }

    .search-input-group {
        display: flex;
        gap: 10px;
    }

    .search-input {
        flex: 1;
        min-width: 0;
        border: 1px solid #D1D5DB;
        border-radius: 16px;
        padding: 12px 14px;
        font-size: 14px;
        color: var(--text-1);
        outline: none;
        background: #F8FAFC;
    }

    .search-input::placeholder {
        color: var(--text-3);
    }

    .refresh-btn {
        background: var(--brand);
        border: none;
        color: #fff;
        border-radius: 16px;
        padding: 12px 16px;
        font-size: 14px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: background .2s ease, transform .2s ease, box-shadow .2s ease;
    }

    .refresh-btn:hover {
        background: #d97706;
    }

    .refresh-btn:active {
        transform: translateY(1px);
    }

    .map-panel {
        position: relative;
        overflow: hidden;
    }

    .map-panel #map {
        min-height: 320px;
        width: 100%;
        display: block;
    }

    .map-legend {
        position: absolute;
        bottom: 16px;
        right: 16px;
        background: rgba(255, 255, 255, .92);
        border-radius: 18px;
        border: 1px solid rgba(226, 232, 240, .9);
        padding: 10px 12px;
        display: grid;
        gap: 8px;
        width: auto;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11px;
        color: var(--text-2);
        font-weight: 600;
    }

    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 999px;
        display: inline-block;
    }

    .legend-dot.bg-blue { background: #3B82F6; }
    .legend-dot.bg-orange { background: #F59E0B; }
    .legend-dot.bg-green { background: #10B981; }

    .map-info {
        padding: 16px;
    }

    .map-info-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .map-info h4 {
        margin: 0 0 6px;
        font-size: 15px;
        font-weight: 700;
        color: var(--text-1);
    }

    .map-info p {
        margin: 0;
        font-size: 13px;
        color: var(--text-2);
        line-height: 1.6;
    }

    .map-info-row .badge {
        display: inline-flex;
        padding: 8px 12px;
        border-radius: 999px;
        background: rgba(59, 130, 246, .12);
        color: #1D4ED8;
        font-size: 11px;
        font-weight: 700;
    }

    .map-list {
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .list-title {
        font-size: 13px;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
        color: var(--text-3);
    }

    .request-list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: grid;
        gap: 12px;
    }

    .request-item {
        background: #F8FAFC;
        border: 1px solid #E5E7EB;
        border-radius: 18px;
        padding: 14px 16px;
        color: var(--text-1);
        font-size: 13px;
        line-height: 1.5;
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
        box-shadow: 0 -1px 3px rgba(0, 0, 0, .05);
    }

    .nav-btn { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 3px; cursor: pointer; color: var(--text-3); background: none; border: none; transition: color .15s; -webkit-tap-highlight-color: transparent; position: relative; text-decoration: none; padding: 0; }
    .nav-btn.active { color: var(--brand); }
    .n-icon { font-size: 20px; }
    .n-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; }
</style>

<div id="mfApp">
    <div class="map-container">
        <div class="prof-hero">
            <div class="prof-avatar">{{ strtoupper(substr($mechanic->name, 0, 1)) }}</div>
            <div class="prof-hero-name">{{ $mechanic->name }}</div>
            <div class="prof-hero-email">{{ $mechanic->email }}</div>
            <div class="prof-hero-badge">Mechanic Account</div>
        </div>

        <div class="search-card">
            <div class="search-input-group">
                <input type="text" class="search-input" placeholder="Search area or request..." aria-label="Search requests" />
                <button type="button" class="refresh-btn">
                    <i class="fas fa-rotate-right"></i>
                    Refresh
                </button>
            </div>
        </div>

        <div class="map-panel">
            <div id="map"></div>
            <div class="map-legend">
                <div class="legend-item"><span class="legend-dot bg-blue"></span> You</div>
                <div class="legend-item"><span class="legend-dot bg-orange"></span> Request</div>
                <div class="legend-item"><span class="legend-dot bg-green"></span> Shop</div>
            </div>
        </div>

        <div class="map-info">
            <div class="map-info-row">
                <div>
                    <h4>Live location</h4>
                    <p>Visible to motorists in need of assistance.</p>
                </div>
                <span class="badge">ONLINE</span>
            </div>
        </div>

        <div class="map-list">
            <div class="list-title">Nearby requests</div>
            <ul class="request-list">
                <li class="request-item">No active requests nearby. Open the map to see requests once they arrive.</li>
            </ul>
        </div>
    </div>

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

@endsection