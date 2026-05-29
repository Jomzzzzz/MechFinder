@extends('layouts.motorist')

@section('main-class', '')

@section('content')
    <style>
        /* ══════════════════════════════════════════════
                               MECHFINDER — PROFESSIONAL LIGHT THEME
                               ══════════════════════════════════════════════ */
        :root {
            --nav-h: 60px;
            --bar-h: 78px;
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
            --blue: #3B82F6;
            /* action color — buttons (not orange) */
            --action: #1E293B;
            --action-2: #2D3F55;
            --action-bg: rgba(30, 41, 59, .06);
            /* radius scale */
            --r1: 6px;
            --r2: 10px;
            --r3: 14px;
            /* shadows */
            --sh-float: 0 4px 18px rgba(0, 0, 0, .12);
            --sh-card: 0 1px 4px rgba(0, 0, 0, .08);
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        #mfApp {
            height: 100dvh;
            height: 100vh;
            position: relative;
            overflow: hidden;
            background: #E8ECF0;
            font-family: Inter, system-ui, -apple-system, sans-serif;
            color: var(--text-1);
            -webkit-font-smoothing: antialiased;
        }

        /* ── MAP ── */
        #map {
            position: absolute;
            inset: 0;
            bottom: calc(var(--nav-h) + var(--bar-h));
            z-index: 1;
        }

        /* ── TOP BAR ── */
        #topBar {
            position: absolute;
            top: 12px;
            left: 12px;
            right: 12px;
            z-index: 20;
        }

        .top-card {
            background: var(--surface);
            border-radius: var(--r3);
            box-shadow: var(--sh-float);
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .top-logo {
            width: 34px;
            height: 34px;
            border-radius: var(--r1);
            background: var(--brand);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .top-info {
            flex: 1;
            min-width: 0;
        }

        .app-name {
            font-size: 13px;
            font-weight: 700;
            line-height: 1.2;
        }

        .location-line {
            font-size: 11px;
            color: var(--text-2);
            margin-top: 1px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .icon-btn {
            width: 34px;
            height: 34px;
            border-radius: var(--r1);
            background: var(--surface-2);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: var(--text-2);
            cursor: pointer;
            flex-shrink: 0;
        }

        /* ── STATUS STRIP ── */
        #statusStrip {
            position: absolute;
            left: 12px;
            right: 12px;
            top: 74px;
            z-index: 22;
            border-radius: var(--r3);
            overflow: hidden;
            box-shadow: var(--sh-float);
            cursor: pointer;
            transform: translateY(-140%);
            opacity: 0;
            pointer-events: none;
            transition: transform .3s ease, opacity .3s ease;
        }

        #statusStrip.show {
            transform: translateY(0);
            opacity: 1;
            pointer-events: all;
        }

        .strip-body {
            background: var(--brand);
            padding: 10px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .strip-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: rgba(0, 0, 0, .45);
        }

        #stripText {
            font-size: 12px;
            font-weight: 700;
            color: #111;
            margin-top: 2px;
        }

        .strip-cta {
            font-size: 10px;
            font-weight: 700;
            color: rgba(0, 0, 0, .45);
        }

        .strip-track {
            height: 3px;
            background: rgba(0, 0, 0, .12);
        }

        #stripBar {
            height: 100%;
            background: rgba(255, 255, 255, .6);
            transition: width .5s ease;
            width: 0%;
        }

        /* ── LOCATE FAB ── */
        #locateFab {
            position: absolute;
            right: 12px;
            bottom: calc(var(--nav-h) + var(--bar-h) + 12px);
            z-index: 21;
            width: 42px;
            height: 42px;
            border-radius: var(--r2);
            background: var(--surface);
            border: 1px solid var(--border);
            box-shadow: var(--sh-float);
            color: var(--text-2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            cursor: pointer;
        }

        /* ── RESCUE BAR ── */
        #rescueBar {
            position: absolute;
            left: 0;
            right: 0;
            bottom: var(--nav-h);
            height: var(--bar-h);
            z-index: 20;
            background: var(--surface);
            border-top: 1px solid var(--border);
            box-shadow: 0 -2px 12px rgba(0, 0, 0, .07);
            padding: 10px 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Find-a-shop trigger button */
        .btn-shops {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: var(--r2);
            padding: 10px 12px;
            cursor: pointer;
            min-width: 0;
            text-align: left;
            -webkit-tap-highlight-color: transparent;
            transition: background .15s;
        }

        .btn-shops:active {
            background: var(--border);
        }

        .btn-shops-icon {
            width: 34px;
            height: 34px;
            border-radius: var(--r1);
            background: var(--brand-bg);
            color: var(--brand);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .btn-shops-text {
            flex: 1;
            min-width: 0;
        }

        .btn-shops-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-1);
            line-height: 1.2;
        }

        .btn-shops-sub {
            font-size: 10px;
            color: var(--text-3);
            margin-top: 2px;
        }

        .btn-rescue {
            flex-shrink: 0;
            background: var(--action);
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            border-radius: var(--r2);
            padding: 12px 14px;
            border: none;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            line-height: 1;
            transition: background .15s;
            -webkit-tap-highlight-color: transparent;
        }

        .btn-rescue .br-icon {
            font-size: 18px;
        }

        .btn-rescue .br-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            opacity: .75;
        }

        .btn-rescue:active {
            background: var(--action-2);
        }

        /* ── SHOP LIST (inside shops panel) ── */
        .panel-search-wrap {
            position: sticky;
            top: 0;
            z-index: 2;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 10px 14px;
        }

        .panel-search {
            display: flex;
            align-items: center;
            gap: 9px;
            background: var(--surface-2);
            border: 1px solid var(--border-2);
            border-radius: var(--r2);
            padding: 9px 12px;
        }

        .panel-search input {
            flex: 1;
            border: none;
            background: transparent;
            font-size: 14px;
            color: var(--text-1);
            font-family: inherit;
            outline: none;
        }

        .panel-search input::placeholder {
            color: var(--text-3);
        }

        .shop-item {
            background: var(--surface);
            border-radius: var(--r2);
            border: 1px solid var(--border);
            padding: 12px 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
        }

        .shop-item-icon {
            width: 42px;
            height: 42px;
            border-radius: var(--r1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            flex-shrink: 0;
        }

        .shop-item-icon.open {
            background: var(--brand-bg);
            color: var(--brand);
        }

        .shop-item-icon.closed {
            background: var(--surface-2);
            color: var(--text-3);
        }

        .shop-item-body {
            flex: 1;
            min-width: 0;
        }

        .shop-item-name {
            font-size: 14px;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .shop-item-meta {
            font-size: 11px;
            color: var(--text-2);
            margin-top: 3px;
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .shop-status-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 1px 7px;
            border-radius: 4px;
        }

        .shop-status-badge.open {
            background: var(--brand-bg);
            color: var(--brand-dk);
        }

        .shop-status-badge.closed {
            background: var(--surface-2);
            color: var(--text-3);
        }

        .dir-btn {
            flex-shrink: 0;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--action-bg);
            color: var(--action);
            border: 1px solid rgba(30, 41, 59, .12);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
        }

        .dir-btn:active {
            background: rgba(30, 41, 59, .14);
        }

        /* ── PANELS ── */
        .panel {
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            bottom: var(--nav-h);
            z-index: 30;
            background: var(--surface-2);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            transform: translateY(100%);
            transition: transform .33s cubic-bezier(.4, 0, .2, 1);
        }

        .panel.open {
            transform: translateY(0);
        }

        .ph {
            /* panel header */
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 13px 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .ph-back {
            width: 32px;
            height: 32px;
            border-radius: var(--r1);
            background: var(--surface-2);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            color: var(--text-2);
            cursor: pointer;
            flex-shrink: 0;
        }

        .ph-title {
            font-size: 17px;
            font-weight: 700;
            line-height: 1.2;
        }

        .ph-subtitle {
            font-size: 11px;
            color: var(--text-2);
            margin-top: 1px;
        }

        /* ── ISSUE TILES ── */
        .issue-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }

        .issue-tile {
            background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: var(--r2);
            padding: 12px 4px 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
            transition: border-color .15s, background .15s;
        }

        .issue-tile .t-icon {
            font-size: 19px;
            color: var(--text-3);
            line-height: 1;
        }

        .issue-tile .t-label {
            font-size: 10px;
            font-weight: 600;
            color: var(--text-2);
            text-align: center;
            line-height: 1.3;
        }

        .issue-tile.sel {
            border-color: var(--action);
            background: var(--action-bg);
        }

        .issue-tile.sel .t-icon,
        .issue-tile.sel .t-label {
            color: var(--action);
        }

        /* ── INPUTS ── */
        .mf-input {
            width: 100%;
            background: var(--surface);
            border: 1px solid var(--border-2);
            border-radius: var(--r1);
            padding: 11px 12px;
            font-size: 14px;
            color: var(--text-1);
            font-family: inherit;
            outline: none;
            transition: border-color .2s;
        }

        .mf-input:focus {
            border-color: var(--action);
        }

        .mf-input::placeholder {
            color: var(--text-3);
        }

        /* ── BUTTONS ── */
        .btn-primary {
            width: 100%;
            background: var(--action);
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            border-radius: var(--r2);
            padding: 14px;
            border: none;
            cursor: pointer;
            transition: opacity .15s;
        }

        .btn-primary:disabled {
            opacity: .35;
            cursor: not-allowed;
        }

        .btn-primary:not(:disabled):active {
            opacity: .85;
        }

        /* ── SEARCH OVERLAY ── */
        #searchOverlay {
            position: absolute;
            inset: 0;
            z-index: 50;
            background: rgba(249, 250, 251, .97);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 24px;
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s ease;
        }

        #searchOverlay.show {
            opacity: 1;
            pointer-events: all;
        }

        .radar-wrap {
            position: relative;
            width: 110px;
            height: 110px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .radar-ring {
            position: absolute;
            width: 110px;
            height: 110px;
            border-radius: 50%;
            border: 2px solid var(--action);
            opacity: 0;
            animation: radarPulse 2.1s ease-out infinite;
        }

        .radar-ring:nth-child(2) {
            animation-delay: .7s;
        }

        .radar-ring:nth-child(3) {
            animation-delay: 1.4s;
        }

        @keyframes radarPulse {
            0% {
                transform: scale(.25);
                opacity: .6;
            }

            100% {
                transform: scale(1.8);
                opacity: 0;
            }
        }

        .radar-center {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: var(--action);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            position: relative;
            z-index: 1;
            box-shadow: 0 4px 20px rgba(30, 41, 59, .3);
        }

        /* Pulse the status strip when waiting for shop to accept */
        #statusStrip.searching .strip-body {
            animation: stripPulse 1.8s ease-in-out infinite;
        }

        @keyframes stripPulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .7;
            }
        }

        /* ── BOTTOM NAV ── */
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
        }

        .nav-badge.show {
            display: block;
        }

        /* ── REQUEST CARD ── */
        .req-card {
            background: var(--surface);
            border-radius: var(--r2);
            border: 1px solid var(--border);
            padding: 14px;
            margin-bottom: 8px;
            box-shadow: var(--sh-card);
        }

        .req-steps {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .rs-dot {
            width: 8px;
            height: 8px;
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

        /* ── MAP PINS ── */
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

        .mf-pin-open {
            background: var(--brand);
            color: #fff;
            border: 2.5px solid #fff;
        }

        .mf-pin-closed {
            background: #6B7280;
            color: #fff;
            border: 2.5px solid #fff;
        }

        .mf-pin-you {
            background: #fff;
            color: var(--blue);
            border: 3px solid var(--blue);
            box-shadow: 0 0 0 5px rgba(59, 130, 246, .18);
        }

        /* Leaflet overrides */
        .leaflet-popup-content-wrapper {
            background: var(--surface);
            color: var(--text-1);
            border-radius: var(--r2);
            box-shadow: var(--sh-float);
            border: 1px solid var(--border);
        }

        .leaflet-popup-tip {
            background: var(--surface);
        }

        .leaflet-popup-content {
            margin: 10px 12px;
            font-family: Inter, sans-serif;
        }

        /* ── CONFIRM MODAL ── */
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
            padding: 20px 20px 32px;
            transform: translateY(100%);
            transition: transform .25s cubic-bezier(.4, 0, .2, 1);
        }

        #confirmModal.show .cm-sheet {
            transform: translateY(0);
        }

        .cm-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: rgba(239, 68, 68, .1);
            color: var(--red);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin: 0 auto 14px;
        }

        .cm-title {
            font-size: 17px;
            font-weight: 700;
            text-align: center;
            color: var(--text-1);
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

        .cm-btn-cancel {
            width: 100%;
            background: var(--red);
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            border-radius: var(--r2);
            padding: 14px;
            border: none;
            cursor: pointer;
        }

        .cm-btn-back {
            width: 100%;
            background: var(--surface-2);
            color: var(--text-1);
            font-size: 15px;
            font-weight: 600;
            border-radius: var(--r2);
            padding: 14px;
            border: 1px solid var(--border);
            cursor: pointer;
        }
    </style>

    <div id="mfApp">

        {{-- MAP --}}
        <div id="map"></div>

        {{-- TOP BAR --}}
        <header id="topBar">
            <div class="top-card">
                <div class="top-logo"><i class="fa-solid fa-wrench"></i></div>
                <div class="top-info">
                    <div class="app-name">MechFinder</div>
                    <div class="location-line" id="locationLine">Detecting location…</div>
                </div>
                <button class="icon-btn" onclick="showTab('profile')">
                    <i class="fa-solid fa-user"></i>
                </button>
            </div>
        </header>

        {{-- ACTIVE REQUEST STRIP --}}
        <div id="statusStrip" onclick="showTab('requests')">
            <div class="strip-body">
                <div>
                    <div class="strip-label">Active Rescue</div>
                    <div id="stripText">Finding nearest shop…</div>
                </div>
                <span class="strip-cta">Details →</span>
            </div>
            <div class="strip-track">
                <div id="stripBar"></div>
            </div>
        </div>

        {{-- LOCATE FAB --}}
        <button id="locateFab" onclick="locateUser()" title="Find my location">
            <i class="fa-solid fa-crosshairs"></i>
        </button>

        {{-- RESCUE BAR --}}
        <div id="rescueBar">
            {{-- Idle state: shown when no active request --}}
            <div id="barIdle" style="display:flex;align-items:center;gap:10px;width:100%;">
                <button class="btn-shops" onclick="openPanel('shopsPanel')">
                    <div class="btn-shops-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                    <div class="btn-shops-text">
                        <div class="btn-shops-title">Find a Shop</div>
                        <div class="btn-shops-sub"><span id="openShopsCount">…</span> open nearby</div>
                    </div>
                    <i class="fa-solid fa-chevron-right" style="font-size:11px;color:var(--text-3);"></i>
                </button>
                <button class="btn-rescue" onclick="openPanel('rescuePanel')">
                    <span class="br-icon"><i class="fa-solid fa-triangle-exclamation"></i></span>
                    <span class="br-label">Rescue</span>
                </button>
            </div>
            {{-- Active state: shown while a request is in progress --}}
            <div id="barActive" style="display:none;align-items:center;gap:10px;width:100%;">
                <div style="flex:1;min-width:0;">
                    <div
                        style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-3);">
                        Active Rescue</div>
                    <div id="barActiveText" style="font-size:13px;font-weight:600;color:var(--text-1);margin-top:2px;">
                    </div>
                </div>
                <button id="cancelBtn" onclick="cancelDispatch()"
                    style="display:none;flex-shrink:0;background:transparent;border:1.5px solid var(--red);color:var(--red);font-size:12px;font-weight:700;border-radius:var(--r1);padding:8px 14px;cursor:pointer;">
                    <i class="fa-solid fa-xmark"></i> Cancel
                </button>
            </div>
        </div>

        {{-- ══ SHOPS PANEL ══ --}}
        <div id="shopsPanel" class="panel">
            <div class="ph">
                <button class="ph-back" onclick="closePanel('shopsPanel')">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
                <div>
                    <div class="ph-title">Nearby Shops</div>
                    <div class="ph-subtitle">Tap <i class="fa-solid fa-location-arrow" style="font-size:9px;"></i> for
                        directions</div>
                </div>
            </div>
            <div class="panel-search-wrap">
                <div class="panel-search">
                    <i class="fa-solid fa-magnifying-glass" style="color:var(--text-3);font-size:13px;"></i>
                    <input id="shopSearchInput" type="search" placeholder="Search shop name…"
                        oninput="filterShopList(this.value)">
                    <button id="shopSearchClear" onclick="clearShopSearch()"
                        style="display:none;background:none;border:none;color:var(--text-3);cursor:pointer;font-size:13px;padding:0;">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
            <div id="shopList" style="padding:12px 14px 32px;"></div>
        </div>

        {{-- CANCEL CONFIRMATION MODAL --}}
        <div id="confirmModal" onclick="_cmBgClick(event)">
            <div class="cm-sheet">
                <div class="cm-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div class="cm-title">Cancel Rescue Request?</div>
                <div class="cm-body">Your request will be removed and no mechanic will be dispatched. You can submit a new
                    one anytime.</div>
                <div class="cm-actions">
                    <button class="cm-btn-cancel" onclick="_cmConfirm()"><i class="fa-solid fa-xmark"></i> Yes, Cancel
                        Request</button>
                    <button class="cm-btn-back" onclick="_cmClose()">Keep Waiting</button>
                </div>
            </div>
        </div>

        {{-- SEARCH OVERLAY --}}
        <div id="searchOverlay">
            <div class="radar-wrap">
                <div class="radar-ring"></div>
                <div class="radar-ring"></div>
                <div class="radar-ring"></div>
                <div class="radar-center"><i class="fa-solid fa-magnifying-glass"></i></div>
            </div>
            <div style="text-align:center;padding:0 32px;">
                <div style="font-size:18px;font-weight:700;color:var(--text-1);margin-bottom:6px;">Searching for nearest
                    shop</div>
                <div style="font-size:13px;color:var(--text-2);line-height:1.6;">Finding the closest available shop in your
                    area…</div>
            </div>
        </div>

        {{-- ══ RESCUE PANEL ══ --}}
        <div id="rescuePanel" class="panel">

            <div class="ph">
                <button class="ph-back" onclick="closePanel('rescuePanel')">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
                <div>
                    <div class="ph-title">Request Rescue</div>
                    <div class="ph-subtitle">Nearest available shop will be dispatched</div>
                </div>
            </div>

            <div style="padding:12px 14px 32px; display:flex; flex-direction:column; gap:14px;">

                {{-- Identity summary --}}
                <div
                    style="background:var(--surface);border:1px solid var(--border);border-radius:var(--r2);padding:12px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                        <span
                            style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-3);">Rescuing</span>
                        <button onclick="showTab('profile')"
                            style="font-size:11px;font-weight:600;color:var(--action);background:none;border:none;cursor:pointer;">
                            Edit <i class="fa-solid fa-pen" style="font-size:9px;"></i>
                        </button>
                    </div>
                    <div id="identityBody" style="font-size:13px;line-height:1.7;color:var(--text-1);"></div>
                </div>

                {{-- Issue type --}}
                <div>
                    <p
                        style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.09em;color:var(--text-3);margin-bottom:8px;">
                        What's the problem?</p>
                    <div class="issue-grid">
                        <button class="issue-tile" onclick="selectIssue(this,'Flat Tire')">
                            <span class="t-icon"><i class="fa-solid fa-wrench"></i></span><span class="t-label">Flat
                                Tire</span>
                        </button>
                        <button class="issue-tile" onclick="selectIssue(this,'Engine Stall')">
                            <span class="t-icon"><i class="fa-solid fa-gear"></i></span><span class="t-label">Engine
                                Stall</span>
                        </button>
                        <button class="issue-tile" onclick="selectIssue(this,'Battery')">
                            <span class="t-icon"><i class="fa-solid fa-battery-half"></i></span><span
                                class="t-label">Battery</span>
                        </button>
                        <button class="issue-tile" onclick="selectIssue(this,'Brake Problem')">
                            <span class="t-icon"><i class="fa-solid fa-circle-stop"></i></span><span
                                class="t-label">Brake</span>
                        </button>
                        <button class="issue-tile" onclick="selectIssue(this,'Chain Problem')">
                            <span class="t-icon"><i class="fa-solid fa-link"></i></span><span
                                class="t-label">Chain</span>
                        </button>
                        <button class="issue-tile" onclick="selectIssue(this,'Other')">
                            <span class="t-icon"><i class="fa-solid fa-circle-question"></i></span><span
                                class="t-label">Other</span>
                        </button>
                    </div>
                </div>

                {{-- Description --}}
                <div>
                    <p
                        style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.09em;color:var(--text-3);margin-bottom:8px;">
                        Description <span
                            style="font-weight:400;text-transform:none;letter-spacing:0;color:var(--text-3);">—
                            optional</span></p>
                    <textarea id="dispatchDesc" class="mf-input" rows="3" placeholder="Describe your situation…"
                        style="resize:none;"></textarea>
                </div>

                {{-- GPS note --}}
                <div
                    style="display:flex;align-items:center;gap:8px;padding:9px 11px;background:var(--action-bg);border-radius:var(--r1);font-size:11px;color:var(--action);border:1px solid rgba(30,41,59,.12);">
                    <i class="fa-solid fa-location-dot"></i>
                    Your GPS location will be shared with the dispatched shop
                </div>

                {{-- Submit --}}
                <button id="rescueBtn" onclick="submitDispatch()" class="btn-primary" disabled>
                    <i class="fa-solid fa-triangle-exclamation"></i> Send Rescue Request
                </button>

                <p id="noShopWarning"
                    style="display:none;text-align:center;font-size:11px;color:var(--red);margin-top:-6px;">
                    <i class="fa-solid fa-circle-exclamation"></i> No open shops found nearby. Your request has been saved.
                </p>

            </div>
        </div>

        {{-- ══ PROFILE PANEL ══ --}}
        <div id="profilePanel" class="panel">

            <div class="ph">
                <button class="ph-back" onclick="closePanel('profilePanel');showTab('map')">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
                <div style="flex:1;">
                    <div class="ph-title">My Profile</div>
                    <div class="ph-subtitle">Saved locally on this device</div>
                </div>
                <button onclick="saveProfileAndClose()"
                    style="background:var(--action);color:#fff;font-size:13px;font-weight:700;border-radius:var(--r1);padding:8px 16px;border:none;cursor:pointer;">
                    Save
                </button>
            </div>

            <div style="padding:12px 14px 32px;display:flex;flex-direction:column;gap:16px;">

                <div>
                    <p
                        style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.09em;color:var(--text-3);margin-bottom:8px;">
                        Personal Info</p>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        <input id="pName" class="mf-input" placeholder="Full name">
                        <input id="pContact" class="mf-input" type="tel"
                            placeholder="Contact number (09171234567)">
                    </div>
                </div>

                <div>
                    <p
                        style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.09em;color:var(--text-3);margin-bottom:8px;">
                        Motorcycle</p>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        <input id="pMakeModel" class="mf-input" placeholder="Make &amp; model (e.g. Honda Wave 110)">
                        <input id="pColor" class="mf-input" placeholder="Color / variant (e.g. Black, Alpha)">
                        <input id="pPlate" class="mf-input" placeholder="Plate or temporary number">
                    </div>
                </div>

            </div>
        </div>

        {{-- ══ REQUESTS PANEL ══ --}}
        <div id="requestsPanel" class="panel">

            <div class="ph">
                <button class="ph-back" onclick="showTab('map')">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
                <div>
                    <div class="ph-title">My Requests</div>
                    <div class="ph-subtitle">Recent rescue history from this device</div>
                </div>
            </div>

            <div style="padding:12px 14px;" id="requestsList">
                <div style="text-align:center;padding:48px 0;color:var(--text-3);font-size:13px;line-height:1.8;">
                    No requests yet.<br>
                    <span style="font-size:11px;color:var(--text-3);">Use the Map tab to request rescue.</span>
                </div>
            </div>
        </div>

        {{-- BOTTOM NAV --}}
        <nav id="bottomNav">
            <button class="nav-btn active" id="navMap" onclick="showTab('map')">
                <span class="n-icon"><i class="fa-solid fa-map"></i></span>
                <span class="n-label">Map</span>
            </button>
            <button class="nav-btn" id="navRequests" onclick="showTab('requests')">
                <span class="n-icon"><i class="fa-solid fa-list"></i></span>
                <span class="n-label">Requests</span>
                <span class="nav-badge" id="reqBadge"></span>
            </button>
            <button class="nav-btn" id="navProfile" onclick="showTab('profile')">
                <span class="n-icon"><i class="fa-solid fa-user"></i></span>
                <span class="n-label">Profile</span>
            </button>
        </nav>

    </div>{{-- #mfApp --}}
@endsection

@section('scripts')
    <script>
        /* ══════════════════════════════════════════════
                               MECHFINDER — APP LOGIC
                               ══════════════════════════════════════════════ */

        const STATUS_LABEL = {
            requested: '<i class="fa-solid fa-hourglass-half"></i> Finding nearest shop…',
            accepted: '<i class="fa-solid fa-circle-check"></i> Shop accepted your request',
            en_route: '<i class="fa-solid fa-motorcycle"></i> Mechanic is on the way',
            arrived: '<i class="fa-solid fa-location-dot"></i> Mechanic has arrived',
            in_progress: '<i class="fa-solid fa-wrench"></i> Repair in progress',
            completed: '<i class="fa-solid fa-circle-check"></i> All done!',
            declined: '<i class="fa-solid fa-circle-xmark"></i> No shops available',
            cancelled: '<i class="fa-solid fa-ban"></i> Request cancelled',
        };
        const STEP_ORDER = ['requested', 'accepted', 'en_route', 'arrived', 'in_progress', 'completed'];
        const STEP_PROGRESS = {
            requested: 10,
            accepted: 25,
            en_route: 48,
            arrived: 65,
            in_progress: 82,
            completed: 100
        };

        const LS = {
            get: (k) => localStorage.getItem(k),
            set: (k, v) => localStorage.setItem(k, v),
            del: (k) => localStorage.removeItem(k)
        };

        /* ── STATE ── */
        let map, userMarker, shopMarkers = [];
        let userLat = 14.8386,
            userLng = 120.2842;
        let selectedIssue = null;
        let currentRequestId = LS.get('mf_current_request_id');
        let pusherClient = null;
        let allShops = [];

        /* ── IDENTITY ── */
        function genToken() {
            const t = 'mf_' + Math.random().toString(36).slice(2, 11) + Date.now().toString(36);
            LS.set('mf_guest_token', t);
            return t;
        }

        function mfIdentity() {
            return {
                guest_token: LS.get('mf_guest_token') || genToken(),
                owner_name: LS.get('mf_owner_name') || '',
                contact_number: LS.get('mf_contact_number') || '',
                vehicle_make_model: LS.get('mf_vehicle_make_model') || '',
                vehicle_variant_color: LS.get('mf_vehicle_variant_color') || '',
                plate_temp_number: LS.get('mf_plate_temp_number') || '',
            };
        }

        /* ── BOOT ── */
        document.addEventListener('DOMContentLoaded', () => {
            loadProfileInputs();
            initMap();
            locateUser();
            if (currentRequestId) resumeActiveRequest(currentRequestId);
        });

        /* ── MAP ── */
        function initMap() {
            map = L.map('map', {
                    zoomControl: false,
                    attributionControl: false
                })
                .setView([userLat, userLng], 14);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(map);
            L.control.attribution({
                prefix: '© OpenStreetMap',
                position: 'bottomleft'
            }).addTo(map);
        }

        function locateUser() {
            const line = document.getElementById('locationLine');
            if (!navigator.geolocation) {
                line.textContent = 'GPS not supported';
                loadShops();
                return;
            }

            navigator.geolocation.getCurrentPosition(pos => {
                userLat = pos.coords.latitude;
                userLng = pos.coords.longitude;

                fetch(`https://nominatim.openstreetmap.org/reverse?lat=${userLat}&lon=${userLng}&format=json`)
                    .then(r => r.json())
                    .then(d => {
                        const p = (d.display_name || '').split(',');
                        line.textContent = p.slice(0, 2).join(',').trim() || 'Location detected';
                    })
                    .catch(() => {
                        line.textContent = 'Location detected';
                    });

                if (userMarker) map.removeLayer(userMarker);
                userMarker = L.marker([userLat, userLng], {
                    icon: L.divIcon({
                        className: '',
                        html: '<div class="mf-pin mf-pin-you"><i class="fa-solid fa-location-dot"></i></div>',
                        iconSize: [34, 34],
                        iconAnchor: [17, 17]
                    }),
                    zIndexOffset: 1000,
                }).addTo(map).bindPopup('<div style="font-weight:700">You are here</div>');

                map.setView([userLat, userLng], 15);
                loadShops();
            }, () => {
                line.textContent = 'Using Olongapo default';
                loadShops();
            }, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 30000
            });
        }

        /* ── SHOPS (pins only) ── */
        async function loadShops() {
            try {
                const shops = await fetch(`/motorist/shops?lat=${userLat}&lng=${userLng}`).then(r => r.json());
                allShops = shops;
                renderShopPins(shops);
            } catch {
                /* non-critical */
            }
        }

        function renderShopPins(shops) {
            shopMarkers.forEach(m => map.removeLayer(m));
            shopMarkers = [];
            const openCount = shops.filter(s => s.status === 'open').length;
            document.getElementById('openShopsCount').textContent = openCount;

            // Refresh list if panel is open
            if (document.getElementById('shopsPanel').classList.contains('open')) {
                renderShopList(shops, document.getElementById('shopSearchInput').value);
            }

            shops.forEach(shop => {
                if (!shop.latitude || !shop.longitude) return;
                const open = shop.status === 'open';
                const name = shop.shop_name || shop.name || 'Shop';
                const addr = shop.address || '';
                const stars = Number(shop.rating || 0).toFixed(1);

                const m = L.marker([shop.latitude, shop.longitude], {
                    icon: L.divIcon({
                        className: '',
                        html: `<div class="mf-pin ${open?'mf-pin-open':'mf-pin-closed'}"><i class="fa-solid fa-wrench"></i></div>`,
                        iconSize: [34, 34],
                        iconAnchor: [17, 17],
                    })
                }).addTo(map).bindPopup(`
      <div style="font-family:Inter,sans-serif;min-width:130px;">
        <div style="font-weight:700;font-size:13px;margin-bottom:2px;">${name}</div>
        <div style="font-size:11px;color:#6B7280;margin-bottom:5px;">${addr}</div>
        <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:4px;
          ${open?'background:#FFF3E0;color:#C87010':'background:#F3F4F6;color:#6B7280'}">
          ${open?'● Open':'● Closed'}
        </span>
        ${open?`<span style="font-size:11px;color:#6B7280;margin-left:6px;"><i class="fa-solid fa-star" style="color:#F59E0B;font-size:10px;"></i> ${stars}</span>`:''}
      </div>
    `);
                shopMarkers.push(m);
            });
        }

        /* ── SHOP LIST + SEARCH ── */
        function renderShopList(shops, query) {
            const q = (query || '').toLowerCase().trim();
            const filtered = q ? shops.filter(s => (s.shop_name || s.name || '').toLowerCase().includes(q)) : shops;
            // open shops first
            const sorted = [...filtered].sort((a, b) => {
                if (a.status === 'open' && b.status !== 'open') return -1;
                if (a.status !== 'open' && b.status === 'open') return 1;
                return (a.distance_km ?? 999) - (b.distance_km ?? 999);
            });
            const list = document.getElementById('shopList');
            if (!sorted.length) {
                list.innerHTML =
                    '<div style="text-align:center;padding:48px 0;color:var(--text-3);font-size:13px;">No shops found.</div>';
                return;
            }
            list.innerHTML = sorted.map(s => {
                const open = s.status === 'open';
                const name = s.shop_name || s.name || 'Shop';
                const addr = s.address ? `<span>${s.address}</span>` : '';
                const dist = s.distance != null ?
                    `<span><i class="fa-solid fa-location-dot" style="font-size:9px;"></i> ${Number(s.distance).toFixed(1)} km</span>` :
                    '';
                const stars = s.rating ?
                    `<span><i class="fa-solid fa-star" style="color:#F59E0B;font-size:9px;"></i> ${Number(s.rating).toFixed(1)}</span>` :
                    '';
                const hasCoords = s.latitude && s.longitude;
                const iconHtml = s.logo ?
                    `<img src="/storage/${s.logo}" style="width:42px;height:42px;border-radius:var(--r1);object-fit:cover;flex-shrink:0;" alt="${name}">` :
                    `<div class="shop-item-icon ${open ? 'open' : 'closed'}"><i class="fa-solid fa-wrench"></i></div>`;
                return `<div class="shop-item">
                    ${iconHtml}
                    <div class="shop-item-body">
                        <div class="shop-item-name">${name}</div>
                        <div class="shop-item-meta">
                            <span class="shop-status-badge ${open ? 'open' : 'closed'}">${open ? 'Open' : 'Closed'}</span>
                            ${dist}${stars}${addr}
                        </div>
                    </div>
                    ${hasCoords ? `<button class="dir-btn" onclick="getDirections(${s.latitude},${s.longitude})" title="Get directions"><i class="fa-solid fa-location-arrow"></i></button>` : ''}
                </div>`;
            }).join('');
        }

        function filterShopList(query) {
            const clear = document.getElementById('shopSearchClear');
            if (clear) clear.style.display = query ? 'block' : 'none';
            renderShopList(allShops, query);
        }

        function clearShopSearch() {
            const input = document.getElementById('shopSearchInput');
            input.value = '';
            filterShopList('');
            input.focus();
        }

        function getDirections(lat, lng) {
            const url =
                `https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${lat},${lng}&travelmode=driving`;
            window.open(url, '_blank');
        }

        /* ── RESCUE FORM ── */
        function openPanel(id) {
            if (id === 'rescuePanel') refreshIdentityCard();
            if (id === 'shopsPanel') renderShopList(allShops, '');
            document.getElementById(id).classList.add('open');
        }

        function closePanel(id) {
            document.getElementById(id).classList.remove('open');
        }

        function refreshIdentityCard() {
            const id = mfIdentity();
            const body = document.getElementById('identityBody');
            const btn = document.getElementById('rescueBtn');

            if (!id.owner_name) {
                body.innerHTML =
                    '<span style="color:var(--red);font-size:12px;"><i class="fa-solid fa-circle-exclamation"></i> Profile incomplete — fill in your name and contact first.</span>';
                btn.disabled = true;
                return;
            }
            let html = `<div style="font-weight:700;margin-bottom:2px;">${id.owner_name}</div>`;
            if (id.contact_number) html +=
                `<div style="color:var(--text-2);font-size:12px;"><i class="fa-solid fa-phone" style="width:14px;font-size:10px;"></i> ${id.contact_number}</div>`;
            if (id.vehicle_make_model) html +=
                `<div style="color:var(--text-2);font-size:12px;"><i class="fa-solid fa-motorcycle" style="width:14px;font-size:10px;"></i> ${id.vehicle_make_model}${id.vehicle_variant_color?' · '+id.vehicle_variant_color:''}</div>`;
            if (id.plate_temp_number) html +=
                `<div style="color:var(--text-2);font-size:12px;"><i class="fa-solid fa-id-card" style="width:14px;font-size:10px;"></i> ${id.plate_temp_number}</div>`;
            body.innerHTML = html;
            if (selectedIssue) btn.disabled = false;
        }

        function selectIssue(tile, issue) {
            document.querySelectorAll('.issue-tile').forEach(t => t.classList.remove('sel'));
            tile.classList.add('sel');
            selectedIssue = issue;
            const btn = document.getElementById('rescueBtn');
            const id = mfIdentity();
            btn.disabled = !id.owner_name;
        }

        /* ── DISPATCH SUBMISSION ── */
        async function submitDispatch() {
            const id = mfIdentity();
            if (!id.owner_name || !id.contact_number) {
                closePanel('rescuePanel');
                showTab('profile');
                alert('Please fill in your name and contact number first.');
                return;
            }
            if (!selectedIssue) {
                alert('Please select an issue type.');
                return;
            }

            const btn = document.getElementById('rescueBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending…';
            document.getElementById('searchOverlay').classList.add('show');

            const payload = {
                guest_token: id.guest_token,
                owner_name: id.owner_name,
                contact_number: id.contact_number,
                vehicle_make_model: id.vehicle_make_model,
                vehicle_variant_color: id.vehicle_variant_color,
                plate_temp_number: id.plate_temp_number,
                issue_type: selectedIssue,
                description: document.getElementById('dispatchDesc').value.trim(),
                location: `${userLat.toFixed(5)}, ${userLng.toFixed(5)}`,
                latitude: userLat,
                longitude: userLng,
            };

            try {
                const res = await fetch('/motorist/dispatch', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.csrfToken
                    },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();

                document.getElementById('searchOverlay').classList.remove('show');
                if (data.success) {
                    currentRequestId = data.request_id;
                    LS.set('mf_current_request_id', data.request_id);
                    saveRequestHistory({
                        id: data.request_id,
                        issueType: selectedIssue
                    });

                    closePanel('rescuePanel');
                    // reset form
                    document.querySelectorAll('.issue-tile').forEach(t => t.classList.remove('sel'));
                    document.getElementById('dispatchDesc').value = '';
                    selectedIssue = null;
                    btn.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Send Rescue Request';

                    showStatusStrip('requested');
                    subscribeToDispatch(data.request_id);
                    document.getElementById('reqBadge').classList.add('show');

                    if (!data.shop_found) {
                        document.getElementById('noShopWarning').style.display = 'block';
                    }
                } else {
                    alert('Failed to send. Please try again.');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Send Rescue Request';
                }
            } catch {
                document.getElementById('searchOverlay').classList.remove('show');
                alert('Network error. Check your connection and try again.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Send Rescue Request';
            }
        }

        /* ── REAL-TIME STATUS ── */
        function subscribeToDispatch(requestId) {
            if (!window.pusherKey || !requestId) return;
            if (pusherClient) pusherClient.disconnect();
            pusherClient = new Pusher(window.pusherKey, {
                cluster: window.pusherCluster,
                forceTLS: true
            });
            pusherClient.subscribe('dispatch-status.' + requestId).bind('dispatch.status', ({
                status
            }) => {
                showStatusStrip(status);
                if (status === 'completed' || status === 'declined') {
                    setTimeout(() => {
                        LS.del('mf_current_request_id');
                        currentRequestId = null;
                        if (pusherClient) pusherClient.disconnect();
                        document.getElementById('statusStrip').classList.remove('show');
                        document.getElementById('reqBadge').classList.remove('show');
                    }, status === 'completed' ? 12000 : 5000);
                }
            });
        }

        async function resumeActiveRequest(requestId) {
            try {
                const res = await fetch(`/motorist/request/${requestId}`);
                if (!res.ok) {
                    LS.del('mf_current_request_id');
                    currentRequestId = null;
                    return;
                }
                const d = await res.json();
                if (!['completed', 'declined', 'cancelled'].includes(d.status)) {
                    showStatusStrip(d.status); // also calls updateRescueBar
                    subscribeToDispatch(requestId);
                    document.getElementById('reqBadge').classList.add('show');
                } else {
                    LS.del('mf_current_request_id');
                    currentRequestId = null;
                }
            } catch {}
        }

        function showStatusStrip(status) {
            document.getElementById('stripText').innerHTML = STATUS_LABEL[status] ?? status;
            document.getElementById('stripBar').style.width = (STEP_PROGRESS[status] ?? 0) + '%';
            const strip = document.getElementById('statusStrip');
            strip.classList.add('show');
            strip.classList.toggle('searching', status === 'requested');
            updateRescueBar(status);
        }

        function updateRescueBar(status) {
            const idle = document.getElementById('barIdle');
            const active = document.getElementById('barActive');
            const activeText = document.getElementById('barActiveText');
            const cancelBtn = document.getElementById('cancelBtn');
            if (!status || ['completed', 'declined', 'cancelled'].includes(status)) {
                idle.style.display = 'flex';
                active.style.display = 'none';
                return;
            }
            idle.style.display = 'none';
            active.style.display = 'flex';
            activeText.innerHTML = STATUS_LABEL[status] ?? status;
            cancelBtn.style.display = status === 'requested' ? 'block' : 'none';
        }

        function cancelDispatch() {
            if (!currentRequestId) return;
            document.getElementById('confirmModal').classList.add('show');
        }

        function _cmClose() {
            document.getElementById('confirmModal').classList.remove('show');
        }

        function _cmBgClick(e) {
            if (e.target === document.getElementById('confirmModal')) _cmClose();
        }

        async function _cmConfirm() {
            _cmClose();
            const btn = document.querySelector('#confirmModal .cm-btn-cancel');
            try {
                const res = await fetch(`/motorist/request/${currentRequestId}/cancel`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': window.csrfToken
                    }
                });
                const data = await res.json();
                if (data.success) {
                    LS.del('mf_current_request_id');
                    currentRequestId = null;
                    if (pusherClient) pusherClient.disconnect();
                    document.getElementById('statusStrip').classList.remove('show', 'searching');
                    document.getElementById('reqBadge').classList.remove('show');
                    updateRescueBar(null);
                } else {
                    alert(data.error ?? 'Could not cancel — request may have already been accepted.');
                }
            } catch {
                alert('Network error. Please try again.');
            }
        }

        /* ── NAVIGATION ── */
        function showTab(tab) {
            ['map', 'requests', 'profile'].forEach(t => {
                document.getElementById('nav' + t[0].toUpperCase() + t.slice(1))
                    .classList.toggle('active', t === tab);
            });
            if (tab === 'map') {
                closePanel('requestsPanel');
                closePanel('profilePanel');
                closePanel('rescuePanel');
                closePanel('shopsPanel');
                setTimeout(() => map.invalidateSize(), 350);
            } else if (tab === 'requests') {
                closePanel('profilePanel');
                closePanel('rescuePanel');
                closePanel('shopsPanel');
                openPanel('requestsPanel');
                loadRequests();
            } else if (tab === 'profile') {
                closePanel('requestsPanel');
                closePanel('rescuePanel');
                closePanel('shopsPanel');
                loadProfileInputs();
                openPanel('profilePanel');
            }
        }

        /* ── PROFILE ── */
        function loadProfileInputs() {
            const id = mfIdentity();
            document.getElementById('pName').value = id.owner_name;
            document.getElementById('pContact').value = id.contact_number;
            document.getElementById('pMakeModel').value = id.vehicle_make_model;
            document.getElementById('pColor').value = id.vehicle_variant_color;
            document.getElementById('pPlate').value = id.plate_temp_number;
        }

        function saveProfileAndClose() {
            LS.set('mf_owner_name', document.getElementById('pName').value.trim());
            LS.set('mf_contact_number', document.getElementById('pContact').value.trim());
            LS.set('mf_vehicle_make_model', document.getElementById('pMakeModel').value.trim());
            LS.set('mf_vehicle_variant_color', document.getElementById('pColor').value.trim());
            LS.set('mf_plate_temp_number', document.getElementById('pPlate').value.trim());
            closePanel('profilePanel');
            showTab('map');
        }

        /* ── REQUEST HISTORY ── */
        function saveRequestHistory(entry) {
            const h = JSON.parse(LS.get('mf_request_history') ?? '[]');
            h.unshift({
                ...entry,
                time: new Date().toISOString()
            });
            LS.set('mf_request_history', JSON.stringify(h.slice(0, 10)));
        }

        async function loadRequests() {
            const hist = JSON.parse(LS.get('mf_request_history') ?? '[]');
            const list = document.getElementById('requestsList');
            if (!hist.length) {
                list.innerHTML =
                    '<div style="text-align:center;padding:48px 0;color:var(--text-3);font-size:13px;line-height:1.8;">No requests yet.<br><span style="font-size:11px;">Use the Map tab to request rescue.</span></div>';
                return;
            }
            list.innerHTML =
                '<div style="padding:12px 0;color:var(--text-3);font-size:12px;text-align:center;">Loading…</div>';
            const cards = await Promise.all(hist.map(async entry => {
                let status = 'unknown',
                    shopName = entry.shopName ?? null,
                    issueType = entry.issueType ?? '';
                try {
                    const r = await fetch(`/motorist/request/${entry.id}`);
                    if (r.ok) {
                        const d = await r.json();
                        status = d.status ?? 'unknown';
                        shopName = d.shop_name ?? shopName;
                        issueType = d.issue_type ?? issueType;
                    }
                } catch {}
                return buildRequestCard(entry.id, shopName, issueType, status, entry.time);
            }));
            list.innerHTML = cards.join('');
        }

        function buildRequestCard(id, shopName, issueType, status, timeStr) {
            const stepIdx = STEP_ORDER.indexOf(status);
            const isActive = !['completed', 'declined', 'unknown'].includes(status);
            const timeLabel = timeStr ? formatTimeAgo(new Date(timeStr)) : '';
            const sc = isActive ? 'var(--brand)' : status === 'completed' ? 'var(--green)' : status === 'declined' ?
                'var(--red)' : 'var(--text-3)';

            const stepsHtml = STEP_ORDER.map((s, i) => {
                const d = i < stepIdx,
                    a = i === stepIdx;
                return `<div class="rs-dot ${d?'done':a?'active':''}"></div>${i<STEP_ORDER.length-1?`<div class="rs-line ${d?'done':''}"></div>`:''}`;
            }).join('');

            const shopLine = shopName ?
                `<span> · <i class="fa-solid fa-store" style="font-size:9px;"></i> ${shopName}</span>` :
                '<span style="color:var(--text-3);"> · Finding shop…</span>';

            return `<div class="req-card">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;">
      <div style="flex:1;min-width:0;margin-right:10px;">
        <div style="font-weight:700;font-size:14px;margin-bottom:2px;">${issueType||'Rescue Request'}</div>
        <div style="font-size:11px;color:var(--text-2);">${timeLabel}${shopLine}</div>
      </div>
      <span style="font-size:10px;font-weight:700;color:${sc};flex-shrink:0;text-align:right;line-height:1.6;">${STATUS_LABEL[status]??status}</span>
    </div>
    <div class="req-steps">${stepsHtml}</div>
    <div style="display:flex;justify-content:space-between;margin-top:4px;font-size:9px;color:var(--text-3);">
      <span>Sent</span><span>Accepted</span><span>En Route</span><span>Arrived</span><span>Working</span><span>Done</span>
    </div>
  </div>`;
        }

        function formatTimeAgo(date) {
            const m = Math.round((Date.now() - date.getTime()) / 60000);
            if (m < 1) return 'just now';
            if (m < 60) return m + 'm ago';
            const h = Math.round(m / 60);
            if (h < 24) return h + 'h ago';
            return Math.round(h / 24) + 'd ago';
        }
    </script>
@endsection
