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
            top: 12px;
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
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: var(--surface);
            border: none;
            box-shadow: var(--sh-float);
            color: var(--text-2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
            transition: background .15s, transform .1s;
        }

        #locateFab:active {
            background: var(--surface-2);
            transform: scale(.94);
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

        /* ── RESCUE FAB (floating circle on map) ── */
        #rescueFab {
            position: absolute;
            right: 12px;
            bottom: calc(var(--nav-h) + var(--bar-h) + 68px);
            z-index: 21;
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: var(--red);
            color: #fff;
            border: none;
            box-shadow: var(--sh-float);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            -webkit-tap-highlight-color: transparent;
            transition: background .15s, transform .1s;
        }

        #rescueFab:active {
            background: #DC2626;
            transform: scale(.94);
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
        /* ── RESCUE FORM ── */
        .rescue-form {
            padding: 12px 14px 32px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .resc-section {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .resc-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .09em;
            color: var(--text-3);
        }

        .resc-label-opt {
            font-weight: 400;
            text-transform: none;
            letter-spacing: 0;
        }

        /* identity card */
        .resc-identity-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r2);
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .resc-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--brand-bg);
            color: var(--brand);
            border: 1.5px solid rgba(247, 148, 29, .25);
            font-size: 16px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        #identityBody {
            flex: 1;
            min-width: 0;
        }

        .resc-edit-btn {
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: var(--r1);
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-2);
            font-size: 12px;
            cursor: pointer;
            flex-shrink: 0;
        }

        /* issue grid */
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
            gap: 6px;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
            transition: border-color .15s, background .15s;
        }

        .issue-tile .t-icon-wrap {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--surface-2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: var(--text-3);
            transition: background .15s, color .15s;
        }

        .issue-tile .t-label {
            font-size: 10px;
            font-weight: 600;
            color: var(--text-2);
            text-align: center;
            line-height: 1.3;
        }

        .issue-tile.sel {
            border-color: var(--brand);
            background: var(--brand-bg);
        }

        .issue-tile.sel .t-icon-wrap {
            background: var(--brand);
            color: #fff;
        }

        .issue-tile.sel .t-label {
            color: var(--brand-dk);
        }

        /* gps row */
        .resc-gps {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 9px 12px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r1);
            font-size: 11px;
            color: var(--text-2);
        }

        /* rescue submit */
        .rescue-submit-btn {
            width: 100%;
            background: var(--brand);
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            border-radius: var(--r2);
            padding: 14px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: opacity .15s;
        }

        .rescue-submit-btn:disabled {
            opacity: .35;
            cursor: not-allowed;
        }

        .rescue-submit-btn:not(:disabled):active {
            opacity: .85;
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

        /* ── PROFILE ── */
        .prof-hero {
            background: linear-gradient(145deg, var(--action) 0%, var(--action-2) 100%);
            padding: 28px 20px 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .prof-avatar {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background: var(--brand);
            color: #fff;
            font-size: 26px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid rgba(255, 255, 255, .2);
        }

        .prof-hero-name {
            font-size: 17px;
            font-weight: 700;
            color: #fff;
            text-align: center;
        }

        .prof-hero-email {
            font-size: 12px;
            color: rgba(255, 255, 255, .55);
            margin-top: 1px;
            text-align: center;
        }

        .prof-hero-badge {
            margin-top: 6px;
            padding: 3px 10px;
            border-radius: 99px;
            background: rgba(255, 255, 255, .12);
            font-size: 10px;
            font-weight: 600;
            color: rgba(255, 255, 255, .7);
            letter-spacing: .05em;
        }

        .prof-section-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .09em;
            color: var(--text-3);
            padding: 0 2px;
            margin-bottom: 6px;
        }

        .prof-group {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r2);
            overflow: hidden;
        }

        .prof-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 14px;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .prof-row+.prof-row {
            border-top: 1px solid var(--border);
        }

        .prof-row-icon {
            width: 34px;
            height: 34px;
            border-radius: var(--r1);
            flex-shrink: 0;
            background: var(--action-bg);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .prof-row-icon i {
            font-size: 13px;
            color: var(--action);
        }

        .prof-row-icon.red {
            background: rgba(239, 68, 68, .08);
        }

        .prof-row-icon.red i {
            color: var(--red);
        }

        .prof-row-body {
            flex: 1;
            min-width: 0;
        }

        .prof-row-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-1);
        }

        .prof-row-sub {
            font-size: 11px;
            color: var(--text-2);
            margin-top: 1px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .prof-row-sub.empty {
            color: var(--text-3);
            font-style: italic;
        }

        .prof-row-chevron {
            font-size: 10px;
            color: var(--text-3);
            flex-shrink: 0;
        }

        .field-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .field-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-2);
            padding-left: 2px;
        }

        .field-hint {
            font-size: 10px;
            color: var(--text-3);
            padding-left: 2px;
            line-height: 1.4;
        }

        .info-banner {
            background: var(--action-bg);
            border: 1px solid rgba(30, 41, 59, .12);
            border-radius: var(--r2);
            padding: 11px 13px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .info-banner i {
            color: var(--action);
            font-size: 13px;
            margin-top: 1px;
            flex-shrink: 0;
        }

        .info-banner span {
            font-size: 12px;
            color: var(--action);
            line-height: 1.5;
        }

        .logout-btn {
            width: 100%;
            padding: 14px;
            background: none;
            border: 1.5px solid var(--red);
            border-radius: var(--r2);
            color: var(--red);
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        /* ── TOAST ── */
        #mfToast {
            position: absolute;
            bottom: calc(var(--nav-h) + 14px);
            left: 50%;
            transform: translateX(-50%) translateY(12px);
            background: var(--action);
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            padding: 10px 18px;
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

        {{-- RESCUE FAB --}}
        <button id="rescueFab" onclick="openPanel('rescuePanel')" title="Request Rescue">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </button>

        {{-- RESCUE BAR --}}
        <div id="rescueBar">
            {{-- Idle state: shown when no active request --}}
            <div id="barIdle" style="display:flex;align-items:center;width:100%;">
                <button class="btn-shops" onclick="openPanel('shopsPanel')">
                    <div class="btn-shops-icon"><i class="fa-solid fa-wrench"></i></div>
                    <div class="btn-shops-text">
                        <div class="btn-shops-title">Find a Shop</div>
                        <div class="btn-shops-sub">
                            <i class="fa-solid fa-location-dot"></i>
                            <span id="locationLineBtm">Detecting…</span>
                            &nbsp;&middot;&nbsp;
                            <i class="fa-solid fa-circle-check" style="color:var(--green);"></i>
                            <span id="openShopsCount">…</span> open
                        </div>
                    </div>
                    <i class="fa-chevron-right fa-solid" style="font-size:11px;color:var(--text-3);"></i>
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
                    <i class="fa-arrow-left fa-solid"></i>
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
                    <input id="shopSearchInput" type="text" placeholder="Search shop name…"
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
                    <i class="fa-arrow-left fa-solid"></i>
                </button>
                <div>
                    <div class="ph-title">Request Rescue</div>
                    <div class="ph-subtitle">Nearest available shop will be dispatched</div>
                </div>
            </div>

            <div class="rescue-form">

                {{-- Identity --}}
                <div class="resc-section">
                    <div class="resc-label">Rescuing</div>
                    <div class="resc-identity-card">
                        <div class="resc-avatar" id="rescueAvatar">?</div>
                        <div id="identityBody" style="flex:1;min-width:0;"></div>
                        <button onclick="showTab('profile')" class="resc-edit-btn" title="Edit profile">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                    </div>
                </div>

                {{-- Issue type --}}
                <div class="resc-section">
                    <div class="resc-label">What's the problem?</div>
                    <div class="issue-grid">
                        <button class="issue-tile" onclick="selectIssue(this,'Flat Tire')">
                            <span class="t-icon-wrap"><i class="fa-solid fa-wrench"></i></span>
                            <span class="t-label">Flat Tire</span>
                        </button>
                        <button class="issue-tile" onclick="selectIssue(this,'Engine Stall')">
                            <span class="t-icon-wrap"><i class="fa-solid fa-gear"></i></span>
                            <span class="t-label">Engine Stall</span>
                        </button>
                        <button class="issue-tile" onclick="selectIssue(this,'Battery')">
                            <span class="t-icon-wrap"><i class="fa-solid fa-battery-half"></i></span>
                            <span class="t-label">Battery</span>
                        </button>
                        <button class="issue-tile" onclick="selectIssue(this,'Brake Problem')">
                            <span class="t-icon-wrap"><i class="fa-solid fa-circle-stop"></i></span>
                            <span class="t-label">Brake</span>
                        </button>
                        <button class="issue-tile" onclick="selectIssue(this,'Chain Problem')">
                            <span class="t-icon-wrap"><i class="fa-solid fa-link"></i></span>
                            <span class="t-label">Chain</span>
                        </button>
                        <button class="issue-tile" onclick="selectIssue(this,'Other')">
                            <span class="t-icon-wrap"><i class="fa-solid fa-circle-question"></i></span>
                            <span class="t-label">Other</span>
                        </button>
                    </div>
                </div>

                {{-- Description --}}
                <div class="resc-section">
                    <div class="resc-label">Notes <span class="resc-label-opt">— optional</span></div>
                    <textarea id="dispatchDesc" class="mf-input" rows="3" placeholder="Describe your situation…"
                        style="resize:none;"></textarea>
                </div>

                {{-- GPS --}}
                <div class="resc-gps">
                    <i class="fa-solid fa-location-dot" style="color:var(--brand);"></i>
                    GPS location shared with the dispatched shop
                </div>

                {{-- Submit --}}
                <button id="rescueBtn" onclick="submitDispatch()" class="rescue-submit-btn" disabled>
                    <i class="fa-solid fa-paper-plane"></i> Send Rescue Request
                </button>

                <p id="noShopWarning"
                    style="display:none;text-align:center;font-size:11px;color:var(--red);margin-top:-8px;">
                    <i class="fa-solid fa-circle-exclamation"></i> No open shops found nearby. Your request has been saved.
                </p>

            </div>
        </div>

        {{-- ══ PROFILE PANEL (overview) ══ --}}
        <div id="profilePanel" class="panel">

            {{-- Hero --}}
            <div class="prof-hero">
                <div class="prof-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <div>
                    <div class="prof-hero-name">{{ Auth::user()->name }}</div>
                    <div class="prof-hero-email">{{ Auth::user()->email }}</div>
                </div>
                <div class="prof-hero-badge"><i class="fa-solid fa-shield-halved" style="margin-right:4px;"></i>Motorist
                    Account</div>
            </div>

            <div style="padding:20px 14px 48px;display:flex;flex-direction:column;gap:22px;overflow-y:auto;">

                {{-- Ride info --}}
                <div>
                    <p class="prof-section-label">Ride Information</p>
                    <div class="prof-group">
                        <button class="prof-row" onclick="openSubPanel('editMotoPanel')">
                            <div class="prof-row-icon"><i class="fa-solid fa-motorcycle"></i></div>
                            <div class="prof-row-body">
                                <div class="prof-row-title">My Motorcycle</div>
                                <div id="profMotoSub" class="prof-row-sub empty">Tap to add — make, model, plate</div>
                            </div>
                            <i class="fa-chevron-right fa-solid prof-row-chevron"></i>
                        </button>
                        <button class="prof-row" onclick="openSubPanel('editContactPanel')">
                            <div class="prof-row-icon"><i class="fa-solid fa-phone"></i></div>
                            <div class="prof-row-body">
                                <div class="prof-row-title">Dispatch Contact</div>
                                <div id="profContactSub" class="prof-row-sub empty">Tap to add — name &amp; phone number
                                </div>
                            </div>
                            <i class="fa-chevron-right fa-solid prof-row-chevron"></i>
                        </button>
                    </div>
                    <p style="font-size:10px;color:var(--text-3);margin-top:6px;padding:0 4px;line-height:1.5;">
                        <i class="fa-solid fa-circle-info" style="margin-right:3px;"></i>
                        This info is shared with the mechanic when you request rescue.
                    </p>
                </div>

                {{-- Account --}}
                <div>
                    <p class="prof-section-label">Account &amp; Security</p>
                    <div class="prof-group">
                        <button class="prof-row" onclick="openSubPanel('changePasswordPanel')">
                            <div class="prof-row-icon"><i class="fa-solid fa-lock"></i></div>
                            <div class="prof-row-body">
                                <div class="prof-row-title">Change Password</div>
                                <div class="prof-row-sub">Update your login password</div>
                            </div>
                            <i class="fa-chevron-right fa-solid prof-row-chevron"></i>
                        </button>
                    </div>
                </div>

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="fa-right-from-bracket fa-solid"></i> Log Out
                    </button>
                </form>

            </div>
        </div>

        {{-- ══ EDIT MOTORCYCLE SUB-PANEL ══ --}}
        <div id="editMotoPanel" class="panel" style="display:none;">
            <div class="ph">
                <button class="ph-back" onclick="closeSubPanel('editMotoPanel')">
                    <i class="fa-arrow-left fa-solid"></i>
                </button>
                <div style="flex:1;">
                    <div class="ph-title">My Motorcycle</div>
                </div>
                <button onclick="saveMoto()"
                    style="background:var(--action);color:#fff;font-size:13px;font-weight:700;border-radius:var(--r1);padding:8px 18px;border:none;cursor:pointer;">Save</button>
            </div>
            <div style="padding:16px 14px 40px;display:flex;flex-direction:column;gap:16px;">

                <div class="info-banner">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>This info is shared with the mechanic when you request a rescue. Keep it accurate so they can
                        identify your bike.</span>
                </div>

                <div class="field-group">
                    <label class="field-label" for="pMakeModel">Make &amp; Model <span
                            style="color:var(--red);">*</span></label>
                    <input id="pMakeModel" class="mf-input" placeholder="e.g. Honda Wave 110, Yamaha Mio">
                    <span class="field-hint">The brand and model of your motorcycle</span>
                </div>

                <div class="field-group">
                    <label class="field-label" for="pColor">Color &amp; Variant</label>
                    <input id="pColor" class="mf-input" placeholder="e.g. Black Alpha, Red Sports">
                    <span class="field-hint">Color and edition — helps the mechanic spot your bike</span>
                </div>

                <div class="field-group">
                    <label class="field-label" for="pPlate">Plate / Conduction Number</label>
                    <input id="pPlate" class="mf-input" placeholder="e.g. ABC 1234 or conduction sticker">
                    <span class="field-hint">Official plate or temporary conduction number</span>
                </div>

            </div>
        </div>

        {{-- ══ EDIT CONTACT SUB-PANEL ══ --}}
        <div id="editContactPanel" class="panel" style="display:none;">
            <div class="ph">
                <button class="ph-back" onclick="closeSubPanel('editContactPanel')">
                    <i class="fa-arrow-left fa-solid"></i>
                </button>
                <div style="flex:1;">
                    <div class="ph-title">Dispatch Contact</div>
                </div>
                <button onclick="saveContact()"
                    style="background:var(--action);color:#fff;font-size:13px;font-weight:700;border-radius:var(--r1);padding:8px 18px;border:none;cursor:pointer;">Save</button>
            </div>
            <div style="padding:16px 14px 40px;display:flex;flex-direction:column;gap:16px;">

                <div class="info-banner">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>The mechanic uses this to contact you during a rescue. Make sure the number can receive calls and
                        SMS.</span>
                </div>

                <div class="field-group">
                    <label class="field-label" for="pName">Your Name <span style="color:var(--red);">*</span></label>
                    <input id="pName" class="mf-input" placeholder="e.g. Juan Dela Cruz">
                    <span class="field-hint">Your name as it appears to the mechanic</span>
                </div>

                <div class="field-group">
                    <label class="field-label" for="pContact">Mobile Number <span
                            style="color:var(--red);">*</span></label>
                    <input id="pContact" class="mf-input" type="tel" placeholder="e.g. 09171234567">
                    <span class="field-hint">Philippine mobile number — must be reachable for rescue coordination</span>
                </div>

            </div>
        </div>

        {{-- ══ CHANGE PASSWORD SUB-PANEL ══ --}}
        <div id="changePasswordPanel" class="panel" style="display:none;">
            <div class="ph">
                <button class="ph-back" onclick="closeSubPanel('changePasswordPanel')">
                    <i class="fa-arrow-left fa-solid"></i>
                </button>
                <div>
                    <div class="ph-title">Change Password</div>
                </div>
            </div>
            <div style="padding:16px 14px 40px;display:flex;flex-direction:column;gap:16px;">

                <div class="info-banner">
                    <i class="fa-solid fa-lock"></i>
                    <span>For your security, enter your current password first. Your new password must be at least 6
                        characters.</span>
                </div>

                @if ($errors->has('current_password'))
                    <div
                        style="background:rgba(239,68,68,.1);border:1px solid var(--red);border-radius:var(--r2);padding:11px 13px;font-size:12px;color:var(--red);display:flex;gap:8px;align-items:flex-start;">
                        <i class="fa-solid fa-circle-exclamation" style="margin-top:1px;flex-shrink:0;"></i>
                        <span>{{ $errors->first('current_password') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('motorist.profile.password') }}"
                    style="display:flex;flex-direction:column;gap:16px;">
                    @csrf

                    <div class="field-group">
                        <label class="field-label" for="cur_pw">Current Password</label>
                        <input id="cur_pw" name="current_password" class="mf-input" type="password"
                            placeholder="Your existing password" required autocomplete="current-password">
                        <span class="field-hint">Enter the password you currently use to log in</span>
                    </div>

                    <div class="field-group">
                        <label class="field-label" for="new_pw">New Password</label>
                        <input id="new_pw" name="password" class="mf-input" type="password"
                            placeholder="At least 6 characters" required autocomplete="new-password">
                        <span class="field-hint">Choose a strong password you haven't used before</span>
                    </div>

                    <div class="field-group">
                        <label class="field-label" for="new_pw_conf">Confirm New Password</label>
                        <input id="new_pw_conf" name="password_confirmation" class="mf-input" type="password"
                            placeholder="Re-enter your new password" required autocomplete="new-password">
                        <span class="field-hint">Must match the new password above</span>
                    </div>

                    <button type="submit" class="btn-primary" style="margin-top:4px;">
                        <i class="fa-solid fa-lock"></i> Update Password
                    </button>
                </form>
            </div>
        </div>

        {{-- ══ REQUESTS PANEL ══ --}}
        <div id="requestsPanel" class="panel">

            <div class="ph">
                <button class="ph-back" onclick="showTab('map')">
                    <i class="fa-arrow-left fa-solid"></i>
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

        {{-- TOAST --}}
        <div id="mfToast"></div>

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
        let shopStatusClient = null;
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
            initMap();
            locateUser();
            subscribeToShopStatus();
            if (currentRequestId) resumeActiveRequest(currentRequestId);
            @if (session('pw_success'))
                showToast('{{ session('pw_success') }}', 'success');
            @endif
            @if ($errors->hasAny())
                showTab('profile');
                openSubPanel('changePasswordPanel');
            @endif
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
            const lineBtm = document.getElementById('locationLineBtm');

            function setLocation(text) {
                lineBtm.textContent = text;
            }
            if (!navigator.geolocation) {
                setLocation('GPS not supported');
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
                        setLocation(p.slice(0, 2).join(',').trim() || 'Location detected');
                    })
                    .catch(() => {
                        setLocation('Location detected');
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
                startShopPolling();
            }, () => {
                setLocation('Using Olongapo default');
                loadShops();
                startShopPolling();
            }, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 30000
            });
        }

        /* ── SHOPS (pins only) ── */
        let _shopPollTimer = null;

        async function loadShops() {
            try {
                const shops = await fetch(`/motorist/shops?lat=${userLat}&lng=${userLng}&_t=${Date.now()}`, {
                    cache: 'no-store'
                }).then(r => r.json());
                allShops = shops;
                renderShopPins(shops);
            } catch {
                /* non-critical */
            }
        }

        function startShopPolling() {
            if (_shopPollTimer) return; // already running
            _shopPollTimer = setInterval(loadShops, 30000); // refresh every 30 s
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
            if (id === 'shopsPanel') {
                renderShopList(allShops, '');
                loadShops(); // always fetch fresh status when panel opens
            }
            document.getElementById(id).classList.add('open');
        }

        function closePanel(id) {
            document.getElementById(id).classList.remove('open');
        }

        let _toastTimer;

        function showToast(msg, type = '') {
            const t = document.getElementById('mfToast');
            t.textContent = msg;
            t.className = 'show' + (type ? ' ' + type : '');
            clearTimeout(_toastTimer);
            _toastTimer = setTimeout(() => t.classList.remove('show'), 3200);
        }

        function refreshIdentityCard() {
            const id = mfIdentity();
            const body = document.getElementById('identityBody');
            const btn = document.getElementById('rescueBtn');
            const avatar = document.getElementById('rescueAvatar');

            if (!id.owner_name) {
                if (avatar) avatar.textContent = '?';
                body.innerHTML =
                    '<span style="color:var(--red);font-size:12px;line-height:1.2;">Complete your profile first.</span>';
                btn.disabled = true;
                return;
            }
            if (avatar) avatar.textContent = id.owner_name[0].toUpperCase();
            let html = `<div style="font-size:13px;font-weight:700;line-height:1.2;">${id.owner_name}</div>`;
            if (id.contact_number) html +=
                `<div style="font-size:11px;color:var(--text-2);line-height:1.2;margin-top:2px;">${id.contact_number}</div>`;
            if (id.vehicle_make_model) html +=
                `<div style="font-size:11px;color:var(--text-2);line-height:1.2;margin-top:1px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${id.vehicle_make_model}${id.vehicle_variant_color?' · '+id.vehicle_variant_color:''}</div>`;
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

        /* ── REAL-TIME SHOP STATUS ── */
        function subscribeToShopStatus() {
            if (!window.pusherKey) return;
            shopStatusClient = new Pusher(window.pusherKey, {
                cluster: window.pusherCluster,
                forceTLS: true
            });
            shopStatusClient.subscribe('shops-status').bind('shop.status', () => {
                loadShops(); // re-fetch all shops whenever any shop toggles
            });
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
                renderProfileSummary();
                openPanel('profilePanel');
            }
        }

        /* ── PROFILE ── */
        function renderProfileSummary() {
            const id = mfIdentity();
            // Motorcycle
            const motoSub = document.getElementById('profMotoSub');
            if (id.vehicle_make_model) {
                const parts = [id.vehicle_make_model, id.vehicle_variant_color, id.plate_temp_number].filter(Boolean);
                motoSub.textContent = parts.join(' · ');
                motoSub.classList.remove('empty');
            } else {
                motoSub.textContent = 'Tap to add — make, model, plate';
                motoSub.classList.add('empty');
            }
            // Contact
            const contactSub = document.getElementById('profContactSub');
            if (id.owner_name || id.contact_number) {
                const parts = [id.owner_name, id.contact_number].filter(Boolean);
                contactSub.textContent = parts.join(' · ');
                contactSub.classList.remove('empty');
            } else {
                contactSub.textContent = 'Tap to add — name & phone number';
                contactSub.classList.add('empty');
            }
        }

        function openSubPanel(id) {
            // Load current values into sub-panel inputs before showing
            const identity = mfIdentity();
            if (id === 'editMotoPanel') {
                document.getElementById('pMakeModel').value = identity.vehicle_make_model;
                document.getElementById('pColor').value = identity.vehicle_variant_color;
                document.getElementById('pPlate').value = identity.plate_temp_number;
            } else if (id === 'editContactPanel') {
                document.getElementById('pName').value = identity.owner_name;
                document.getElementById('pContact').value = identity.contact_number;
            }
            document.getElementById(id).style.display = 'block';
            requestAnimationFrame(() => document.getElementById(id).classList.add('open'));
        }

        function closeSubPanel(id) {
            const el = document.getElementById(id);
            el.classList.remove('open');
            setTimeout(() => {
                el.style.display = 'none';
            }, 320);
            renderProfileSummary();
        }

        function saveMoto() {
            LS.set('mf_vehicle_make_model', document.getElementById('pMakeModel').value.trim());
            LS.set('mf_vehicle_variant_color', document.getElementById('pColor').value.trim());
            LS.set('mf_plate_temp_number', document.getElementById('pPlate').value.trim());
            closeSubPanel('editMotoPanel');
        }

        function saveContact() {
            LS.set('mf_owner_name', document.getElementById('pName').value.trim());
            LS.set('mf_contact_number', document.getElementById('pContact').value.trim());
            closeSubPanel('editContactPanel');
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
