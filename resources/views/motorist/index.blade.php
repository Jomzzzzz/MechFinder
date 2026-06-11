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
            height: 100svh;
            height: 100dvh;
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

        /* ── STEP TRACKER (Grab-style) ── */
        #rescueBar.bar-active {
            height: auto;
            min-height: 96px;
            align-items: flex-start;
            padding: 10px 14px 8px;
        }

        .step-track {
            display: flex;
            align-items: center;
            width: 100%;
            margin: 6px 0 0;
        }

        .step-dot {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            background: var(--surface-2);
            border: 2px solid var(--border);
            color: var(--text-3);
            flex-shrink: 0;
            transition: background .25s, border-color .25s, color .25s;
        }

        .step-dot.done {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
        }

        .step-dot.active {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
            animation: stepPulse 1.6s ease-in-out infinite;
        }

        @keyframes stepPulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(247, 148, 29, .35);
            }

            50% {
                box-shadow: 0 0 0 6px rgba(247, 148, 29, 0);
            }
        }

        .step-line {
            flex: 1;
            height: 2px;
            background: var(--border);
            transition: background .3s;
        }

        .step-line.done {
            background: var(--brand);
        }

        /* ── LOCATE FAB ── */
        #locateFab {
            position: absolute;
            right: 12px;
            top: 12px;
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
            left: 12px;
            right: 12px;
            bottom: calc(var(--nav-h) + 10px);
            z-index: 20;
            background: #fff;
            border: 1px solid rgba(226, 232, 240, .95);
            border-radius: 22px;
            box-shadow: 0 24px 48px rgba(15, 23, 42, .12);
            padding: 16px 18px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: transform .2s, box-shadow .2s;
        }

        #rescueBar:hover {
            transform: translateY(-1px);
            box-shadow: 0 28px 52px rgba(15, 23, 42, .14);
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

        .btn-shops:hover {
            background: rgba(247, 148, 29, .06);
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

        .panel.chat-panel {
            display: none;
            position: fixed;
            left: 12px;
            right: 12px;
            bottom: calc(var(--nav-h) + 10px);
            width: auto;
            margin: 16px 0 0;
            z-index: 45;
            transform: none;
            transition: none;
            max-height: min(calc(100vh - var(--nav-h) - 40px), 92vh);
            overflow: hidden;
            border-radius: 18px;
            background: var(--surface);
            border: 1px solid var(--border);
        }

        .panel.chat-panel.open {
            display: flex;
        }

        #requestChatPanel.open.fullscreen {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            max-height: 100vh;
            margin: 0;
            border-radius: 0;
            box-shadow: none;
            z-index: 60;
        }

        #mfApp.chat-open #bottomNav {
            display: none;
        }

        .chat-panel {
            max-height: 100%;
        }

        /* When chat panel is placed inside the requests panel, make it behave like a full tab panel. */
        #requestsPanel .chat-panel {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            margin: 0;
            border-radius: 0;
            transform: translateY(100%);
            transition: transform .33s cubic-bezier(.4, 0, .2, 1);
            overflow: hidden;
            background: var(--surface);
            border: none;
        }

        #requestsPanel .chat-panel.open {
            transform: translateY(0);
        }

        .chat-panel {
            display: flex;
            flex-direction: column;
            min-height: 0;
            background: var(--surface);
            border: 1px solid rgba(226, 232, 240, .9);
            border-radius: 20px;
            box-shadow: 0 18px 32px rgba(15, 23, 42, .08);
        }

        .chat-panel .chat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 14px 14px 10px;
            position: sticky;
            top: 0;
            background: transparent;
            z-index: 2;
            border-bottom: none;
            flex-shrink: 0;
        }

        .chat-panel .chat-header h2 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
            color: var(--text-1);
        }

        .chat-panel .chat-tab-bar {
            display: flex;
            gap: 8px;
            padding: 0 14px;
            margin-top: 8px;
            margin-bottom: 10px;
            flex-shrink: 0;
            z-index: 5;
        }

        .chat-panel .chat-tab {
            flex: 1;
            padding: 10px 12px;
            border-radius: 999px;
            border: 1px solid rgba(15, 23, 42, .08);
            background: rgba(15, 23, 42, .04);
            color: var(--text-2);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            transition: background .2s, border-color .2s, color .2s;
        }

        .chat-panel .chat-tab:hover {
            background: rgba(15, 23, 42, .08);
        }

        .chat-panel .chat-tab.active {
            background: rgba(247, 148, 29, .15);
            color: var(--brand-dk);
            border-color: rgba(247, 148, 29, .22);
        }

        .chat-panel .chat-body {
            display: flex;
            flex-direction: column;
            flex: 1 1 0;
            min-height: 0;
            overflow-y: auto;
            padding: 0 14px 14px 14px;
            gap: 12px;
            background: transparent;
            max-height: calc(100vh - 230px);
        }

        .chat-panel .chat-bubble {
            max-width: 90%;
            word-break: break-word;
        }

        .chat-panel .chat-footer {
            position: sticky;
            bottom: 0;
            z-index: 10;
            flex-shrink: 0;
            background: transparent;
            padding: 12px 14px 14px;
            border-top: none;
        }

        .chat-panel .chat-input {
            width: 100%;
            border-radius: 16px;
            border: 1px solid rgba(15, 23, 42, .12);
            background: var(--surface);
            color: var(--text-1);
            padding: 14px 16px;
            font-size: 14px;
            outline: none;
            box-shadow: inset 0 0 0 1px rgba(15, 23, 42, .03);
        }

        .chat-panel .chat-send {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: var(--brand);
            border: none;
            color: #111;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 16px;
            transition: transform .15s, box-shadow .15s;
        }

        .chat-panel .chat-send:hover {
            box-shadow: 0 12px 24px rgba(247, 148, 29, .16);
            transform: translateY(-1px);
        }

        .chat-panel .chat-empty {
            text-align: center;
            color: var(--text-3);
            padding: 40px 14px;
            font-size: 13px;
        }

        .chat-panel .chat-closed {
            color: var(--text-3);
            font-size: 13px;
            padding: 10px 14px;
            text-align: center;
        }

        .chat-panel .bubble-own {
            align-self: flex-end;
            display: flex;
            flex-direction: column;
            text-align: right;
        }

        .chat-panel .bubble-other {
            align-self: flex-start;
            display: flex;
            flex-direction: column;
            text-align: left;
        }

        .chat-panel .bubble-content {
            border-radius: 20px;
            padding: 12px 16px;
            line-height: 1.55;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, .06);
        }

        .chat-panel .bubble-own .bubble-content {
            background: rgba(247, 148, 29, .14);
            color: var(--text-1);
            border: 1px solid rgba(247, 148, 29, .18);
        }

        .chat-panel .bubble-other .bubble-content {
            background: rgba(15, 23, 42, .05);
            color: var(--text-1);
            border: 1px solid rgba(15, 23, 42, .08);
        }

        .chat-panel .bubble-meta {
            margin-top: 6px;
            font-size: 11px;
            color: var(--text-3);
        }

        .chat-panel .chat-empty,
        .chat-panel .chat-closed {
            color: var(--text-3);
            font-size: 13px;
            text-align: center;
            padding: 24px 14px;
        }

        @media (max-width: 640px) {
            .panel.chat-panel {
                left: 8px;
                right: 8px;
                bottom: calc(var(--nav-h) + 8px);
                margin: 12px 0 0;
                max-height: min(calc(100vh - var(--nav-h) - 28px), 90vh);
            }

            .chat-panel .chat-body {
                max-height: calc(100vh - 220px);
            }

            .chat-panel .chat-footer {
                padding: 10px 12px 12px;
            }

            .chat-panel .chat-header,
            .chat-panel .chat-tab-bar {
                padding-left: 12px;
                padding-right: 12px;
            }
        }

        .ph {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 13px 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            position: sticky;
            top: 0;
            z-index: 1;
            backdrop-filter: blur(10px);
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

        .save-panel-btn {
            background: linear-gradient(135deg, #F7941D 0%, #FF9500 100%);
            color: #111;
            border: none;
            border-radius: var(--r2);
            padding: 12px 18px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: transform .15s, box-shadow .15s, opacity .15s;
        }

        .save-panel-btn:active {
            transform: translateY(1px);
            opacity: .95;
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
            padding: 16px 14px;
            cursor: pointer;
            border: none;
            background: var(--surface);
            width: 100%;
            text-align: left;
            transition: background .15s, box-shadow .15s;
        }

        .prof-row:hover {
            background: var(--surface-2);
            box-shadow: inset 0 0 0 1px rgba(247, 148, 29, .08);
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
            background: rgba(255, 255, 255, .94);
            backdrop-filter: blur(12px);
            border-top: 1px solid rgba(226, 232, 240, .95);
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
            transition: color .15s, transform .15s;
            -webkit-tap-highlight-color: transparent;
            position: relative;
            padding: 4px 0;
        }

        .nav-btn.active {
            color: var(--brand);
            transform: translateY(-1px);
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
            background: #fff;
            border-radius: 18px;
            border: 1px solid rgba(226, 232, 240, .95);
            padding: 16px;
            margin-bottom: 14px;
            box-shadow: 0 18px 32px rgba(15, 23, 42, .06);
            transition: transform .2s, box-shadow .2s;
        }

        .req-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 22px 36px rgba(15, 23, 42, .08);
        }

        .req-steps {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 12px;
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

        .you-pin-animate {
            animation: youPulse 2s ease-in-out infinite;
        }

        @keyframes youPulse {

            0%,
            100% {
                box-shadow: 0 0 0 4px #3B82F6, 0 4px 14px rgba(59, 130, 246, .45);
            }

            50% {
                box-shadow: 0 0 0 9px rgba(59, 130, 246, .25), 0 4px 14px rgba(59, 130, 246, .45);
            }
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

        /* ── COMPLETION MODAL ── */
        #completionModal {
            position: absolute;
            inset: 0;
            z-index: 65;
            background: rgba(0, 0, 0, .45);
            display: flex;
            align-items: flex-end;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s ease;
        }

        #completionModal.show {
            opacity: 1;
            pointer-events: all;
        }

        #completionModal .cm-sheet {
            background: var(--surface);
            border-radius: var(--r3) var(--r3) 0 0;
            width: 100%;
            padding: 28px 20px 36px;
            transform: translateY(100%);
            transition: transform .3s cubic-bezier(.4, 0, .2, 1);
        }

        #completionModal.show .cm-sheet {
            transform: translateY(0);
        }

        .completion-icon {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background: rgba(16, 185, 129, .12);
            color: var(--green);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            margin: 0 auto 16px;
        }
        /* ── RESPONSIVE TWEAKS FOR MOBILE ── */
        @media (max-width: 360px) {
            :root { --nav-h: 56px; }
            .panel.chat-panel {
                left: 8px;
                right: 8px;
                border-radius: 12px;
                max-height: calc(100vh - var(--nav-h) - 32px);
            }
            .chat-panel .chat-body { max-height: calc(100vh - var(--nav-h) - 120px); }
        }

        @media (min-width: 361px) and (max-width: 420px) {
            :root { --nav-h: 58px; }
            .panel.chat-panel {
                left: 10px;
                right: 10px;
                border-radius: 14px;
                max-height: calc(100vh - var(--nav-h) - 40px);
            }
            .chat-panel .chat-body { max-height: calc(100vh - var(--nav-h) - 120px); }
        }

        /* Portrait short screens: reduce header/footer impact */
        @media (max-height: 720px) {
            .panel.chat-panel { max-height: calc(100vh - var(--nav-h) - 28px); }
            .chat-panel .chat-body { max-height: calc(100vh - var(--nav-h) - 120px); }
        }

        /* Landscape phones: use wider panel and less height reserved for header/footer */
        @media (orientation: landscape) and (max-width: 900px) {
            .panel.chat-panel {
                left: 12px;
                right: 12px;
                bottom: calc(var(--nav-h) + 10px);
                max-height: calc(100vh - var(--nav-h) - 20px);
            }
            .chat-panel .chat-body { max-height: calc(100vh - var(--nav-h) - 72px); }
        }

        /* Ensure the chat footer visually sits above nav with consistent 10px gap */
        .panel.chat-panel { box-sizing: border-box; }
        /* Smooth swap between request list and chat panel */
        #requestsList {
            transition: opacity .28s ease, transform .28s ease;
            will-change: opacity, transform;
        }

        #requestsList.hidden-for-chat {
            opacity: 0;
            transform: translateY(-6px);
            pointer-events: none;
        }
    </style>

    <div id="mfApp">

        {{-- MAP --}}
        <div id="map"></div>

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
            {{-- Active state: Grab-style step tracker --}}
            <div id="barActive" style="display:none;flex-direction:column;width:100%;" onclick="showTab('requests')">
                {{-- Row 1: title + cancel --}}
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;width:100%;">
                    <div style="flex:1;min-width:0;">
                        <div
                            style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-3);">
                            Active Rescue</div>
                        <div id="barActiveText"
                            style="font-size:15px;font-weight:700;color:var(--text-1);margin-top:1px;line-height:1.25;">
                        </div>
                    </div>
                    <button id="cancelBtn" onclick="event.stopPropagation();cancelDispatch()"
                        style="display:none;flex-shrink:0;width:30px;height:30px;background:transparent;border:2px solid var(--red);color:var(--red);border-radius:50%;cursor:pointer;align-items:center;justify-content:center;font-size:13px;padding:0;margin-top:2px;">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                {{-- Row 2: step dots --}}
                <div class="step-track">
                    <div class="step-dot" id="track-step-0"><i class="fa-solid fa-store"></i></div>
                    <div class="step-line" id="track-line-0"></div>
                    <div class="step-dot" id="track-step-1"><i class="fa-solid fa-motorcycle"></i></div>
                    <div class="step-line" id="track-line-1"></div>
                    <div class="step-dot" id="track-step-2"><i class="fa-solid fa-circle-check"></i></div>
                </div>
                {{-- Distance (prominent) --}}
                <div id="barDistance"
                    style="display:none;align-items:center;justify-content:center;gap:8px;margin-top:8px;padding:9px 14px;background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.22);border-radius:12px;">
                    <i class="fa-solid fa-route" style="font-size:14px;color:#3B82F6;"></i>
                    <div style="display:flex;align-items:baseline;gap:3px;">
                        <span id="barDistanceVal"
                            style="font-size:22px;font-weight:800;color:#3B82F6;letter-spacing:-.5px;line-height:1;"></span>
                        <span style="font-size:11px;font-weight:600;color:var(--text-3);">away</span>
                    </div>
                </div>
                {{-- Mechanic chip (visible when en_route / arrived) --}}
                <div id="barMechInfo"
                    style="display:none;align-items:center;gap:10px;margin-top:8px;padding:10px 12px;background:var(--surface-2);border-radius:12px;width:100%;">
                    <div
                        style="width:36px;height:36px;border-radius:50%;background:var(--brand-bg);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fa-solid fa-user-gear" style="font-size:14px;color:var(--brand);"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div id="barMechName"
                            style="font-size:13px;font-weight:700;color:var(--text-1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;margin-top:3px;flex-wrap:wrap;">
                            <span style="display:flex;align-items:center;gap:3px;">
                                <i class="fa-solid fa-phone" style="font-size:8px;color:var(--text-3);"></i>
                                <span id="barMechPhone" style="font-size:10px;color:var(--text-3);"></span>
                            </span>
                            <span id="barMechPlate" style="display:none;align-items:center;gap:3px;">
                                <i class="fa-solid fa-motorcycle" style="font-size:8px;color:var(--text-3);"></i>
                                <span id="barMechPlateVal" style="font-size:10px;color:var(--text-3);"></span>
                            </span>
                        </div>
                    </div>
                </div>
                {{-- Sub message --}}
                <div id="barSubMsg" style="font-size:11px;color:var(--text-3);margin-top:5px;"></div>
            </div>
        </div>

        {{-- ══ REQUEST DETAILS MODAL ══ --}}
        <div id="detailsModal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.18);backdrop-filter:blur(10px);z-index:1000;overflow-y:auto;padding:20px;">
            <div style="max-width:560px;width:100%;margin:40px auto;border-radius:24px;overflow:hidden;background:#FFFFFF;border:1px solid rgba(15,23,42,0.08);box-shadow:0 24px 60px rgba(15,23,42,0.12);">
                <div style="display:flex;justify-content:space-between;align-items:center;gap:14px;padding:22px 22px 16px;background:#FFFFFF;">
                    <div>
                        
                        <h2 style="margin:0;font-size:22px;font-weight:700;color:#111827;letter-spacing:-0.02em;">Request details</h2>
                    </div>
                    <button onclick="closeDetailsModal()" style="width:36px;height:36px;background:#F8FAFC;border:1px solid #E5E7EB;color:#111827;font-size:16px;cursor:pointer;border-radius:12px;display:grid;place-items:center;transition:background 0.2s;" onmouseover="this.style.background='#EFF6FF'" onmouseout="this.style.background='#F8FAFC'">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div style="display:grid;gap:16px;padding:20px 22px 22px;">
                    <div style="display:grid;gap:8px;">
                        <div style="font-size:11px;color:#6B7280;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;">Issue</div>
                        <div id="detailIssueType" style="font-size:20px;font-weight:700;color:#111827;line-height:1.2;"></div>
                    </div>
                    <div style="display:grid;gap:8px;">
                        <div style="font-size:11px;color:#6B7280;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;">Location</div>
                        <div id="detailLocation" style="font-size:15px;color:#475569;line-height:1.6;"></div>
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;">
                        <div style="border:1px solid #E5E7EB;border-radius:18px;padding:16px;background:#F8FAFC;">
                            <div style="font-size:11px;color:#6B7280;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;margin-bottom:6px;">Status</div>
                            <div id="detailStatus" style="font-size:14px;font-weight:700;color:#047857;line-height:1.4;"></div>
                        </div>
                        <div style="border:1px solid #E5E7EB;border-radius:18px;padding:16px;background:#F8FAFC;">
                            <div style="font-size:11px;color:#6B7280;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;margin-bottom:6px;">Time</div>
                            <div id="detailTime" style="font-size:14px;font-weight:700;color:#475569;line-height:1.4;"></div>
                        </div>
                    </div>
                    <div style="display:grid;gap:12px;">
                        <div style="padding:18px;border:1px solid #E5E7EB;border-radius:20px;background:#F8FAFC;display:flex;justify-content:space-between;align-items:center;gap:12px;">
                            <span style="font-size:11px;color:#6B7280;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;">Shop</span>
                            <span id="detailShop" style="font-size:15px;color:#111827;font-weight:700;"></span>
                        </div>
                        <div style="padding:18px;border:1px solid #E5E7EB;border-radius:20px;background:#F8FAFC;display:flex;justify-content:space-between;align-items:center;gap:12px;">
                            <span style="font-size:11px;color:#6B7280;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;">Mechanic</span>
                            <span id="detailMechanic" style="font-size:15px;color:#111827;font-weight:700;"></span>
                        </div>
                    </div>
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:12px;padding:18px 22px 22px;background:#FFFFFF;border-top:1px solid #E5E7EB;">
                    <button onclick="closeDetailsModal()" style="flex:1;min-width:140px;padding:14px 18px;background:#FFFFFF;border:1px solid #D1D5DB;border-radius:16px;color:#111827;font-size:14px;font-weight:700;cursor:pointer;transition:background 0.2s;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#FFFFFF'">
                        Close
                    </button>
                    <button onclick="goToMessage()" style="flex:1;min-width:140px;padding:14px 18px;background:#2563EB;border:none;border-radius:16px;color:#FFFFFF;font-size:14px;font-weight:700;cursor:pointer;transition:transform 0.18s,box-shadow 0.18s;box-shadow:0 12px 30px rgba(37,99,235,0.18);" onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 18px 40px rgba(37,99,235,0.24)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 12px 30px rgba(37,99,235,0.18)'">
                        Message
                    </button>
                </div>
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

        {{-- TRANSACTION COMPLETE MODAL --}}
        {{-- ═══ COMPLETION & REVIEW MODAL ═══ --}}
        <div id="reviewNotificationOverlay" style="position: fixed; inset: 0; background: rgba(0,0,0,0.85); display: none; z-index: 99999; padding: 20px;">
        </div>
        <div id="reviewModal" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 450px; background: #0a0a0a; border: 1px solid rgba(255,255,255,0.1); border-radius: 16px; padding: 36px 28px; z-index: 100000; display: none; box-shadow: 0 25px 80px rgba(0,0,0,0.9); max-height: 90vh; overflow-y: auto;">
            <!-- Success checkmark -->
            <div style="text-align: center; margin-bottom: 28px;">
                <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #2FB344 0%, #1fa02a 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; font-size: 32px; color: white; box-shadow: 0 8px 20px rgba(47, 179, 68, 0.3);">
                    <i class="fa-solid fa-check"></i>
                </div>
            </div>

            <!-- Title and description -->
            <h2 style="font-size: 24px; font-weight: 800; color: #fff; text-align: center; margin: 0 0 10px; letter-spacing: -0.5px;">Rescue Complete</h2>
            <p style="font-size: 13px; color: #999; text-align: center; margin: 0 0 36px; line-height: 1.6;">Please rate the mechanic and shop to help us maintain quality service</p>

            <!-- Rating stars -->
            <div style="margin-bottom: 32px;">
                <label style="display: block; font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 14px; font-weight: 600;">Rate Experience</label>
                <div style="display: flex; justify-content: center; gap: 14px; margin-bottom: 12px;">
                    <button onclick="window.setReviewRating(1)" class="rating-star" data-rating="1" style="background: none; border: none; font-size: 36px; color: #444; cursor: pointer; transition: all 0.2s ease; padding: 4px;">★</button>
                    <button onclick="window.setReviewRating(2)" class="rating-star" data-rating="2" style="background: none; border: none; font-size: 36px; color: #444; cursor: pointer; transition: all 0.2s ease; padding: 4px;">★</button>
                    <button onclick="window.setReviewRating(3)" class="rating-star" data-rating="3" style="background: none; border: none; font-size: 36px; color: #444; cursor: pointer; transition: all 0.2s ease; padding: 4px;">★</button>
                    <button onclick="window.setReviewRating(4)" class="rating-star" data-rating="4" style="background: none; border: none; font-size: 36px; color: #444; cursor: pointer; transition: all 0.2s ease; padding: 4px;">★</button>
                    <button onclick="window.setReviewRating(5)" class="rating-star" data-rating="5" style="background: none; border: none; font-size: 36px; color: #444; cursor: pointer; transition: all 0.2s ease; padding: 4px;">★</button>
                </div>
                <p id="ratingLabel" style="font-size: 12px; color: #777; text-align: center; margin: 0; font-weight: 500;">Select rating</p>
            </div>

            <!-- Comments -->
            <div style="margin-bottom: 28px;">
                <label style="display: block; font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; font-weight: 600;">Comments (optional)</label>
                <textarea id="reviewCommentField" placeholder="Tell us about your experience..." maxlength="500" style="width: 100%; height: 90px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #ddd; padding: 12px; border-radius: 8px; font-family: inherit; font-size: 13px; resize: none; outline: none; box-sizing: border-box;"></textarea>
                <div style="font-size: 11px; color: #666; margin-top: 6px; text-align: right;"><span id="charCountDisplay">0</span>/500</div>
            </div>

            <!-- Service tags -->
            <div style="margin-bottom: 28px;">
                <label style="display: block; font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; font-weight: 600;">What went well? (optional)</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                    <button onclick="window.toggleTag('Professional')" class="tag-btn" data-tag="Professional" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1); color: #999; padding: 10px; border-radius: 6px; font-size: 12px; cursor: pointer; transition: all 0.2s ease; display: flex; align-items: center; justify-content: center; gap: 6px;"><i class="fa-solid fa-check" style="opacity: 0; font-size: 11px;"></i><span>Professional</span></button>
                    <button onclick="window.toggleTag('Timely')" class="tag-btn" data-tag="Timely" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1); color: #999; padding: 10px; border-radius: 6px; font-size: 12px; cursor: pointer; transition: all 0.2s ease; display: flex; align-items: center; justify-content: center; gap: 6px;"><i class="fa-solid fa-check" style="opacity: 0; font-size: 11px;"></i><span>Timely</span></button>
                    <button onclick="window.toggleTag('Friendly')" class="tag-btn" data-tag="Friendly" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1); color: #999; padding: 10px; border-radius: 6px; font-size: 12px; cursor: pointer; transition: all 0.2s ease; display: flex; align-items: center; justify-content: center; gap: 6px;"><i class="fa-solid fa-check" style="opacity: 0; font-size: 11px;"></i><span>Friendly</span></button>
                    <button onclick="window.toggleTag('Fair Price')" class="tag-btn" data-tag="Fair Price" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1); color: #999; padding: 10px; border-radius: 6px; font-size: 12px; cursor: pointer; transition: all 0.2s ease; display: flex; align-items: center; justify-content: center; gap: 6px;"><i class="fa-solid fa-check" style="opacity: 0; font-size: 11px;"></i><span>Fair Price</span></button>
                </div>
            </div>

            <!-- Buttons -->
            <div style="display: flex; gap: 10px;">
                <button onclick="window.submitReview()" id="submitBtn" style="flex: 1; background: linear-gradient(135deg, #F7941D 0%, #ff8c00 100%); color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(247, 148, 29, 0.3);">Submit Review</button>
                <button onclick="window.skipReview()" style="flex: 1; background: transparent; color: #999; border: 1px solid rgba(255,255,255,0.1); padding: 12px; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer; transition: all 0.2s ease;">Skip</button>
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
                <div class="prof-avatar">{{ strtoupper(substr(optional(Auth::user())->name ?? 'U', 0, 1)) }}</div>
                <div>
                    <div class="prof-hero-name">{{ optional(Auth::user())->name ?? 'Motorist' }}</div>
                    <div class="prof-hero-email">{{ optional(Auth::user())->email ?? '' }}</div>
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
                <button onclick="saveMoto()" class="save-panel-btn">Save</button>
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
                <button onclick="saveContact()" class="save-panel-btn">Save</button>
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

            <div id="requestChatPanel" class="panel chat-panel">
                <div class="chat-header">
                    <button class="ph-back" onclick="closeRequestChat()">
                        <i class="fa-arrow-left fa-solid"></i>
                    </button>
                    <div>
                        <h2 id="chatPanelTitle">Chat</h2>
                        <div id="chatPanelSubtitle" style="font-size:12px;color:var(--text-3);margin-top:4px;">Messages with your rescue team</div>
                    </div>
                    <div style="width:32px;height:60px;"></div>
                </div>
                <div class="chat-tab-bar" style="padding:0 14px 0;">
                    <button id="chatTabMechanic" class="chat-tab active" type="button" onclick="switchRequestChatTab('mechanic')">Mechanic</button>
                    <button id="chatTabShop" class="chat-tab" type="button" onclick="switchRequestChatTab('shop')">Shop</button>
                </div>
                <div id="requestChatBody" class="chat-body"></div>
                <div id="requestChatClosed" class="chat-closed" style="display:none;">
                    Chat is closed after rescue completion.
                </div>
                <div class="chat-footer">
                    <form id="requestChatForm" class="flex items-center gap-3">
                        <input id="requestChatInput" type="text" placeholder="Type a message..." class="chat-input" autocomplete="off" required>
                        <button type="submit" class="chat-send"><i class="fa-solid fa-paper-plane"></i></button>
                    </form>
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

        /* Plain headline text for the active bar */
        const STATUS_TITLE = {
            requested: 'Finding nearest mechanic…',
            accepted: 'Shop accepted your request',
            en_route: 'Mechanic is on the way',
            arrived: 'Mechanic has arrived!',
            completed: 'All done — job complete!',
        };
        /* Sub-message below the step dots */
        const SUB_MSG = {
            requested: 'Looking for an available mechanic…',
            accepted: 'Your mechanic is getting ready to head out',
            en_route: 'Sit tight — your mechanic is on the way to you',
            arrived: 'Your mechanic is on-site and working on your vehicle',
        };

        const STATUS_LABEL = {
            requested: '<i class="fa-solid fa-hourglass-half"></i> Finding nearest shop…',
            accepted: '<i class="fa-solid fa-circle-check"></i> Shop accepted your request',
            en_route: '<i class="fa-solid fa-motorcycle"></i> Mechanic is on the way',
            arrived: '<i class="fa-solid fa-location-dot"></i> Mechanic has arrived — job complete!',
            completed: '<i class="fa-solid fa-circle-check"></i> All done!',
            declined: '<i class="fa-solid fa-circle-xmark"></i> No shops available',
            cancelled: '<i class="fa-solid fa-ban"></i> Request cancelled',
        };
        const STATUS_TEXT = {
            requested: 'Finding nearest shop',
            accepted: 'Shop accepted your request',
            en_route: 'Mechanic is on the way',
            arrived: 'Mechanic has arrived — job complete',
            completed: 'All done',
            declined: 'No shops available',
            cancelled: 'Request cancelled',
        };
        const STEP_ORDER = ['requested', 'accepted', 'en_route', 'arrived', 'completed'];
        const STEP_PROGRESS = {
            requested: 10,
            accepted: 30,
            en_route: 55,
            arrived: 80,
            completed: 100
        };

        const LS = {
            get: (k) => {
                try {
                    return localStorage.getItem(k);
                } catch (e) {
                    console.warn('Storage access denied:', e.message);
                    return null;
                }
            },
            set: (k, v) => {
                try {
                    localStorage.setItem(k, v);
                } catch (e) {
                    console.warn('Storage access denied:', e.message);
                }
            },
            del: (k) => {
                try {
                    localStorage.removeItem(k);
                } catch (e) {
                    console.warn('Storage access denied:', e.message);
                }
            }
        };

        /* ── STATE ── */
        let map, userMarker, shopMarkers = [],
            routeLayer = null,
            mechanicMarker = null,
            hiddenShopMarker = null;
        let _activeShopLat = null,
            _activeShopLng = null;
        let _dispatchShopLat = null,
            _dispatchShopLng = null;
        let userLat = 14.8386,
            userLng = 120.2842;
        let selectedIssue = null;
        const currentUserId = {{ auth()->id() ?? 'null' }};
        const reviewEndpoint = "{{ route('motorist.review.store') }}";
        let currentRequestId = null;
        let currentRequestGuestToken = null;
        let currentDispatchShopId = null;
        let currentMotoristId = null;
        let reviewContext = {
            requestId: null,
            shopId: null,
            motoristId: null,
        };
        
        /* ── REVIEW STATE ── */
        let currentReviewRating = 0;
        let selectedServiceTags = new Set();
        const currentRequestStored = LS.get('mf_current_request_id');
        if (currentRequestStored) {
            try {
                const parsed = JSON.parse(currentRequestStored);
                currentRequestId = parsed?.id ?? currentRequestStored;
                currentRequestGuestToken = parsed?.guest_token ?? null;
            } catch {
                currentRequestId = currentRequestStored;
                currentRequestGuestToken = null;
            }
        }
        let pusherClient = null;
        let shopStatusClient = null;
        let _statusPollTimer = null;
        let _lastKnownStatus = null;
        let allShops = [];

        /* ── BOOT ── */
        document.addEventListener('DOMContentLoaded', () => {
            initMap();
            locateUser();
            subscribeToShopStatus();
            if (currentRequestId) resumeActiveRequest(currentRequestId);
            const validTabs = ['map', 'requests', 'profile'];
            const validPanels = ['rescuePanel','shopsPanel','requestsPanel','profilePanel','requestChatPanel'];
            const savedTab = LS.get('mf_motorist_active_tab');
            const hashState = getMotoristHashState();

            if (validTabs.includes(hashState.hashValue)) {
                showTab(hashState.hashValue);
            } else if (validPanels.includes(hashState.hashValue)) {
                if (hashState.hashValue === 'rescuePanel' || hashState.hashValue === 'shopsPanel') {
                    showTab('map');
                    openPanel(hashState.hashValue);
                } else if (hashState.hashValue === 'requestsPanel') {
                    showTab('requests');
                    openPanel('requestsPanel');
                } else if (hashState.hashValue === 'profilePanel') {
                    showTab('profile');
                    openPanel('profilePanel');
                } else if (hashState.hashValue === 'requestChatPanel' && hashState.requestId) {
                    openRequestChat(hashState.requestId);
                } else {
                    showTab('requests');
                    openPanel('requestChatPanel');
                }
            } else if (savedTab && validTabs.includes(savedTab)) {
                showTab(savedTab);
            }
            window.addEventListener('hashchange', () => {
                const state = getMotoristHashState();
                if (validTabs.includes(state.hashValue)) {
                    showTab(state.hashValue);
                } else if (validPanels.includes(state.hashValue)) {
                    if (state.hashValue === 'rescuePanel' || state.hashValue === 'shopsPanel') {
                        showTab('map');
                        openPanel(state.hashValue);
                    } else if (state.hashValue === 'requestsPanel') {
                        showTab('requests');
                        openPanel('requestsPanel');
                    } else if (state.hashValue === 'profilePanel') {
                        showTab('profile');
                        openPanel('profilePanel');
                    } else if (state.hashValue === 'requestChatPanel' && state.requestId) {
                        openRequestChat(state.requestId);
                    }
                }
            });
            // Refresh profile UI once DB profile has loaded
            window.addEventListener('mfProfileLoaded', () => {
                refreshIdentityCard();
                renderProfileSummary();
            });
            // If already loaded before DOMContentLoaded, refresh immediately
            if (window._mfProfile) {
                refreshIdentityCard();
                renderProfileSummary();
            }
            // Keep --bar-h in sync with the bar's actual rendered height
            const _bar = document.getElementById('rescueBar');
            if (_bar && window.ResizeObserver) {
                new ResizeObserver(() => {
                    document.documentElement.style.setProperty('--bar-h', Math.ceil(_bar
                        .getBoundingClientRect().height) + 'px');
                    if (map) map.invalidateSize();
                }).observe(_bar);
            }
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
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                maxZoom: 19,
                subdomains: 'abcd',
                crossOrigin: true,
                updateWhenIdle: true,
                updateWhenZooming: false,
                keepBuffer: 3
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
                        html: '<div style="position:relative;width:44px;height:44px;"><div style="width:44px;height:44px;border-radius:50%;background:#3B82F6;color:#fff;border:3px solid #fff;display:flex;align-items:center;justify-content:center;font-size:20px;box-shadow:0 2px 10px rgba(59,130,246,.45);"><i class="fa-solid fa-user"></i></div><div style="position:absolute;bottom:-20px;left:50%;transform:translateX(-50%);background:#3B82F6;color:#fff;font-size:9px;font-weight:700;border-radius:3px;padding:2px 6px;letter-spacing:.5px;white-space:nowrap;">YOU</div></div>',
                        iconSize: [44, 44],
                        iconAnchor: [22, 22]
                    }),
                    zIndexOffset: 1000,
                }).addTo(map).bindPopup('<div style="font-weight:700">You are here</div>');

                map.setView([userLat, userLng], 15);
                loadShops();
                startShopPolling();
            }, () => {
                setLocation('Using Olongapo default');
                if (userMarker) map.removeLayer(userMarker);
                userMarker = L.marker([userLat, userLng], {
                    icon: L.divIcon({
                        className: '',
                        html: '<div style="position:relative;width:44px;height:44px;"><div style="width:44px;height:44px;border-radius:50%;background:#3B82F6;color:#fff;border:3px solid #fff;display:flex;align-items:center;justify-content:center;font-size:20px;box-shadow:0 2px 10px rgba(59,130,246,.45);"><i class="fa-solid fa-user"></i></div><div style="position:absolute;bottom:-20px;left:50%;transform:translateX(-50%);background:#3B82F6;color:#fff;font-size:9px;font-weight:700;border-radius:3px;padding:2px 6px;letter-spacing:.5px;white-space:nowrap;">YOU</div></div>',
                        iconSize: [44, 44],
                        iconAnchor: [22, 22]
                    }),
                    zIndexOffset: 1000,
                }).addTo(map).bindPopup('<div style="font-weight:700">You are here (default)</div>');
                map.setView([userLat, userLng], 15);
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

            const OVERLAP_TOL = 0.0002;
            shops.forEach(shop => {
                if (!shop.latitude || !shop.longitude) return;
                const open = shop.status === 'open';
                const name = shop.shop_name || shop.name || 'Shop';
                const addr = shop.address || '';
                const stars = Number(shop.rating || 0).toFixed(1);

                const isAssignedShop = _dispatchShopLat !== null &&
                    Math.abs(shop.latitude - _dispatchShopLat) < OVERLAP_TOL &&
                    Math.abs(shop.longitude - _dispatchShopLng) < OVERLAP_TOL;
                // Hide all other shops while a dispatch is active
                const isOtherShop = _dispatchShopLat !== null && !isAssignedShop;
                // Hide assigned shop pin when mechanic marker is overlapping it
                const isActiveShop = _activeShopLat !== null && isAssignedShop;

                const m = L.marker([shop.latitude, shop.longitude], {
                    icon: L.divIcon({
                        className: '',
                        html: `<div class="mf-pin ${open?'mf-pin-open':'mf-pin-closed'}"><i class="fa-solid fa-wrench"></i></div>`,
                        iconSize: [34, 34],
                        iconAnchor: [17, 17],
                    })
                });
                if (!isOtherShop && !isActiveShop) m.addTo(map);
                else if (isActiveShop) hiddenShopMarker = m;
                m.bindPopup(`
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

        /* ── ROUTE PLOTTING (OSRM) ── */
        async function plotRouteToShop(shopLat, shopLng, mechInfo = {}) {
            if (!shopLat || !shopLng) return;
            clearRoute();
            _activeShopLat = parseFloat(shopLat);
            _activeShopLng = parseFloat(shopLng);
            try {
                const url =
                    `https://router.project-osrm.org/route/v1/driving/${userLng},${userLat};${shopLng},${shopLat}?overview=full&geometries=geojson`;
                const res = await fetch(url);
                const data = await res.json();
                if (data.code !== 'Ok' || !data.routes.length) return;
                const coords = data.routes[0].geometry;
                // Show distance in rescue bar
                const distM = data.routes[0].distance;
                const distKm = (distM / 1000).toFixed(1);
                const distEl = document.getElementById('barDistance');
                const distVal = document.getElementById('barDistanceVal');
                if (distEl && distVal) {
                    distVal.textContent = distKm + ' km';
                    distEl.style.display = 'flex';
                }
                routeLayer = L.geoJSON(coords, {
                    style: {
                        color: '#3B82F6',
                        weight: 5,
                        opacity: 0.9,
                        lineJoin: 'round',
                        lineCap: 'round'
                    }
                }).addTo(map);
                // Hide any existing shop pin at the same location to prevent overlap
                const TOLERANCE = 0.0002;
                hiddenShopMarker = shopMarkers.find(m => {
                    const ll = m.getLatLng();
                    return Math.abs(ll.lat - shopLat) < TOLERANCE && Math.abs(ll.lng - shopLng) < TOLERANCE;
                }) || null;
                if (hiddenShopMarker) map.removeLayer(hiddenShopMarker);
                // Build mechanic popup
                const popupHtml = `<div style="font-family:Inter,sans-serif;min-width:160px;padding:2px 0;">
                    <div style="font-size:12px;font-weight:700;color:#111827;margin-bottom:5px;"><i class="fa-solid fa-motorcycle" style="margin-right:5px;color:#3B82F6;"></i>Mechanic on the way</div>
                    ${mechInfo.name ? `<div style="font-size:12px;color:#374151;margin-bottom:3px;"><b>${mechInfo.name}</b></div>` : ''}
                    ${mechInfo.phone ? `<div style="font-size:11px;color:#6B7280;margin-bottom:2px;"><i class="fa-solid fa-phone" style="font-size:9px;margin-right:4px;"></i>${mechInfo.phone}</div>` : ''}
                    ${mechInfo.plate ? `<div style="font-size:11px;color:#6B7280;"><i class="fa-solid fa-motorcycle" style="font-size:9px;margin-right:4px;"></i>Plate: ${mechInfo.plate}</div>` : ''}
                </div>`;
                // Add motorcycle marker at shop (mechanic) location
                if (mechanicMarker) map.removeLayer(mechanicMarker);
                mechanicMarker = L.marker([shopLat, shopLng], {
                    icon: L.divIcon({
                        className: '',
                        html: '<div class="mf-pin" style="background:#3B82F6;color:#fff;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:16px;box-shadow:0 2px 8px rgba(0,0,0,.25);"><i class="fa-solid fa-motorcycle"></i></div>',
                        iconSize: [36, 36],
                        iconAnchor: [18, 18]
                    }),
                    zIndexOffset: 900
                }).addTo(map).bindPopup(popupHtml);
                // Fit map to show full route — defer so bar has finished expanding first
                const bounds = routeLayer.getBounds().pad(0.2);
                setTimeout(() => {
                    map.invalidateSize();
                    map.fitBounds(bounds);
                }, 200);
            } catch {
                /* non-critical */
            }
        }

        function clearRoute() {
            if (routeLayer) {
                map.removeLayer(routeLayer);
                routeLayer = null;
            }
            if (mechanicMarker) {
                map.removeLayer(mechanicMarker);
                mechanicMarker = null;
            }
            if (hiddenShopMarker) {
                hiddenShopMarker.addTo(map);
                hiddenShopMarker = null;
            }
            _activeShopLat = null;
            _activeShopLng = null;
            const distEl = document.getElementById('barDistance');
            if (distEl) distEl.style.display = 'none';
        }

        /* ── LIVE MECHANIC MARKER ── */
        function moveMechanicMarker(lat, lng) {
            if (!map) return;
            const icon = L.divIcon({
                className: '',
                html: '<div class="mf-pin" style="background:#3B82F6;color:#fff;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:16px;box-shadow:0 2px 8px rgba(0,0,0,.25);border:2.5px solid #fff;"><i class="fa-solid fa-motorcycle"></i></div>',
                iconSize: [36, 36],
                iconAnchor: [18, 18]
            });
            if (mechanicMarker) {
                mechanicMarker.setLatLng([lat, lng]);
            } else {
                mechanicMarker = L.marker([lat, lng], {
                    icon,
                    zIndexOffset: 900
                }).addTo(map);
            }
            // Re-draw route from mechanic's real position to motorist
            if (_activeShopLat) {
                // reuse existing route layer — just shift the start; re-fetch route
                const osrmUrl =
                    `https://router.project-osrm.org/route/v1/driving/${lng},${lat};${_activeShopLng},${_activeShopLat}?overview=full&geometries=geojson`;
                fetch(osrmUrl)
                    .then(r => r.json())
                    .then(data => {
                        if (data.code !== 'Ok' || !data.routes.length) return;
                        if (routeLayer) {
                            map.removeLayer(routeLayer);
                            routeLayer = null;
                        }
                        routeLayer = L.geoJSON(data.routes[0].geometry, {
                            style: {
                                color: '#3B82F6',
                                weight: 5,
                                opacity: .9,
                                lineJoin: 'round',
                                lineCap: 'round'
                            }
                        }).addTo(map);
                        // Update distance
                        const distM = data.routes[0].distance;
                        const distVal = document.getElementById('barDistanceVal');
                        if (distVal) distVal.textContent = (distM / 1000).toFixed(1) + ' km';
                    })
                    .catch(() => {});
            }
        }

        /* ── RESCUE FORM ── */
        function openPanel(id) {
            if (id === 'rescuePanel') refreshIdentityCard();
            if (id === 'shopsPanel') {
                renderShopList(allShops, '');
                loadShops(); // always fetch fresh status when panel opens
            }
            document.getElementById(id).classList.add('open');
            if (id === 'requestChatPanel') {
                updateMotoristHash(null, id, requestChatId);
            } else {
                updateMotoristHash(null, id);
            }
        }

        function closePanel(id) {
            document.getElementById(id).classList.remove('open');
            if (id === 'rescuePanel' || id === 'shopsPanel') {
                updateMotoristHash('map');
            } else if (id === 'requestsPanel' || id === 'requestChatPanel') {
                updateMotoristHash('requests');
            } else if (id === 'profilePanel') {
                updateMotoristHash('profile');
            }
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
                    currentRequestGuestToken = id.guest_token || null;
                    LS.set('mf_current_request_id', JSON.stringify({
                        id: data.request_id,
                        guest_token: currentRequestGuestToken
                    }));
                    saveRequestHistory({
                        id: data.request_id,
                        issueType: selectedIssue
                    });

                    closePanel('rescuePanel');
                    document.getElementById('rescueFab').style.display = 'none';
                    // reset form
                    document.querySelectorAll('.issue-tile').forEach(t => t.classList.remove('sel'));
                    document.getElementById('dispatchDesc').value = '';
                    selectedIssue = null;
                    btn.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Send Rescue Request';

                    showStatusStrip('requested');
                    _lastKnownStatus = 'requested';
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

        /* ── STATUS POLLING FALLBACK ── */
        function startStatusPolling(requestId) {
            if (_statusPollTimer) return;
            _statusPollTimer = setInterval(async () => {
                if (!requestId) {
                    stopStatusPolling();
                    return;
                }
                try {
                    const r = await fetch(`/motorist/request/${requestId}`);
                    if (!r.ok) {
                        stopStatusPolling();
                        return;
                    }
                    const d = await r.json();
                    // always refresh mechanic chip (assignment may happen after status update)
                    updateMechanicInfo(d.status, d.mechanic_name, d.mechanic_phone, d.mechanic_plate);
                    // Track assigned shop and hide others
                    if (['accepted', 'en_route', 'arrived'].includes(d.status) && d.shop_lat && d.shop_lng) {
                        const newLat = parseFloat(d.shop_lat),
                            newLng = parseFloat(d.shop_lng);
                        if (newLat !== _dispatchShopLat || newLng !== _dispatchShopLng) {
                            _dispatchShopLat = newLat;
                            _dispatchShopLng = newLng;
                            renderShopPins(allShops);
                        }
                    }
                    // Plot/clear route based on status
                    if (['en_route', 'arrived'].includes(d.status) && d.shop_lat && d.shop_lng && !routeLayer) {
                        plotRouteToShop(d.shop_lat, d.shop_lng, {
                            name: d.mechanic_name,
                            phone: d.mechanic_phone,
                            plate: d.mechanic_plate
                        });
                    } else if (!['en_route', 'arrived'].includes(d.status)) {
                        clearRoute();
                    }
                    if (d.status !== _lastKnownStatus) {
                        _lastKnownStatus = d.status;
                        if (d.status === 'completed') {
                            currentDispatchShopId = d?.shop_id || currentDispatchShopId;
                            currentMotoristId = d?.motorist_id || currentUserId || currentMotoristId;
                            reviewContext = {
                                requestId: requestId,
                                shopId: currentDispatchShopId,
                                motoristId: currentMotoristId,
                            };
                        }
                        showStatusStrip(d.status, d.mechanic_name, d.mechanic_phone, d.mechanic_plate);
                        if (['completed', 'declined', 'cancelled'].includes(d.status)) {
                            stopStatusPolling();
                            setTimeout(() => {
                                if (!document.getElementById('reviewModal') || document.getElementById('reviewModal').style.display === 'none') {
                                    LS.del('mf_current_request_id');
                                    currentRequestId = null;
                                    currentDispatchShopId = null;
                                    currentMotoristId = null;
                                    reviewContext = { requestId: null, shopId: null, motoristId: null };
                                    if (pusherClient) pusherClient.disconnect();
                                    document.getElementById('reqBadge').classList.remove('show');
                                }
                            }, d.status === 'completed' ? 12000 : 5000);
                        }
                    }
                } catch {}
            }, 5000);
        }

        function stopStatusPolling() {
            if (_statusPollTimer) {
                clearInterval(_statusPollTimer);
                _statusPollTimer = null;
            }
        }

        /* ── REAL-TIME STATUS ── */
        function subscribeToDispatch(requestId) {
            if (!requestId) return;
            // Pusher real-time (fast path)
            if (window.pusherKey) {
                if (pusherClient) pusherClient.disconnect();
                pusherClient = new Pusher(window.pusherKey, {
                    cluster: window.pusherCluster,
                    forceTLS: true
                });
                pusherClient.connection.bind('connected', () => {
                    console.log('[Pusher] connected — subscribing to dispatch-status.' + requestId);
                });
                pusherClient.connection.bind('error', (err) => {
                    console.error('[Pusher] connection error', err);
                });
                pusherClient.subscribe('dispatch-status.' + requestId).bind('dispatch.status', ({
                    status
                }) => {
                    console.log('[Pusher dispatch.status] received status:', status);
                    _lastKnownStatus = status;
                    
                    // For completed status, fetch request data first to set shop_id and motorist_id
                    if (['completed', 'declined', 'cancelled'].includes(status)) {
                        console.log('[Pusher dispatch.status] final status detected, fetching request data for requestId:', requestId);
                        fetch(`/motorist/request/${requestId}`)
                            .then(r => {
                                console.log('[Pusher dispatch.status] fetch response status:', r.status);
                                return r.json();
                            })
                            .then(d => {
                                console.log('[Pusher dispatch.status] request data received:', d);
                                console.log('[Pusher dispatch.status] shop_id from response:', d?.shop_id);
                                console.log('[Pusher dispatch.status] motorist_id from response:', d?.motorist_id);
                                
                                if (status === 'completed') {
                                    currentDispatchShopId = d?.shop_id || null;
                                    currentMotoristId = d?.motorist_id || currentUserId || null;
                                    reviewContext = {
                                        requestId: requestId,
                                        shopId: currentDispatchShopId,
                                        motoristId: currentMotoristId,
                                    };
                                    console.log('[Pusher dispatch.status] set currentDispatchShopId:', currentDispatchShopId);
                                    console.log('[Pusher dispatch.status] set currentMotoristId:', currentMotoristId);
                                }
                                showStatusStrip(status);
                            })
                            .catch(err => {
                                console.error('[Pusher dispatch.status] fetch error:', err);
                                showStatusStrip(status);
                            });
                    } else {
                        showStatusStrip(status);
                    }
                    
                    if (['accepted', 'en_route', 'arrived'].includes(status) && !_dispatchShopLat) {
                        fetch(`/motorist/request/${requestId}`)
                            .then(r => r.json())
                            .then(d => {
                                if (d.shop_lat && d.shop_lng) {
                                    _dispatchShopLat = parseFloat(d.shop_lat);
                                    _dispatchShopLng = parseFloat(d.shop_lng);
                                    renderShopPins(allShops);
                                }
                                if (['en_route', 'arrived'].includes(status) && !routeLayer && d.shop_lat && d
                                    .shop_lng) {
                                    plotRouteToShop(d.shop_lat, d.shop_lng, {
                                        name: d.mechanic_name,
                                        phone: d.mechanic_phone,
                                        plate: d.mechanic_plate
                                    });
                                }
                            })
                            .catch(() => {});
                    } else if (['en_route', 'arrived'].includes(status) && !routeLayer) {
                        fetch(`/motorist/request/${requestId}`)
                            .then(r => r.json())
                            .then(d => {
                                if (d.shop_lat && d.shop_lng) plotRouteToShop(d.shop_lat, d.shop_lng, {
                                    name: d.mechanic_name,
                                    phone: d.mechanic_phone,
                                    plate: d.mechanic_plate
                                });
                            })
                            .catch(() => {});
                    } else if (!['accepted', 'en_route', 'arrived'].includes(status)) {
                        clearRoute();
                    }
                    if (['completed', 'declined', 'cancelled'].includes(status)) {
                        stopStatusPolling();
                        setTimeout(() => {
                            if (!document.getElementById('reviewModal') || document.getElementById('reviewModal').style.display === 'none') {
                                LS.del('mf_current_request_id');
                                currentRequestId = null;
                                currentDispatchShopId = null;
                                currentMotoristId = null;
                                reviewContext = { requestId: null, shopId: null, motoristId: null };
                                if (pusherClient) pusherClient.disconnect();
                                document.getElementById('reqBadge').classList.remove('show');
                            }
                        }, status === 'completed' ? 12000 : 5000);
                    }
                });

                // Live mechanic GPS updates
                pusherClient.subscribe('dispatch-status.' + requestId).bind('mechanic.location', ({
                    lat,
                    lng
                }) => {
                    moveMechanicMarker(parseFloat(lat), parseFloat(lng));
                });
            }
            // Polling fallback — always runs regardless of Pusher
            startStatusPolling(requestId);
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
                    _lastKnownStatus = d.status;
                    if (d.guest_token) {
                        currentRequestGuestToken = d.guest_token;
                        saveRequestToken(requestId, d.guest_token);
                    }
                    document.getElementById('rescueFab').style.display = 'none';
                    showStatusStrip(d.status, d.mechanic_name, d.mechanic_phone, d
                        .mechanic_plate); // also calls updateRescueBar
                    if (['accepted', 'en_route', 'arrived'].includes(d.status) && d.shop_lat && d.shop_lng) {
                        _dispatchShopLat = parseFloat(d.shop_lat);
                        _dispatchShopLng = parseFloat(d.shop_lng);
                        renderShopPins(allShops);
                    }
                    if (['en_route', 'arrived'].includes(d.status) && d.shop_lat && d.shop_lng) {
                        plotRouteToShop(d.shop_lat, d.shop_lng, {
                            name: d.mechanic_name,
                            phone: d.mechanic_phone,
                            plate: d.mechanic_plate
                        });
                    }
                    // Seed mechanic marker from last known GPS (if available)
                    if (['en_route', 'arrived'].includes(d.status) && d.mechanic_lat && d.mechanic_lng) {
                        moveMechanicMarker(parseFloat(d.mechanic_lat), parseFloat(d.mechanic_lng));
                    }
                    subscribeToDispatch(requestId); // also starts polling fallback
                    document.getElementById('reqBadge').classList.add('show');
                } else if (d.status === 'completed') {
                    currentDispatchShopId = d?.shop_id || null;
                    currentMotoristId = d?.motorist_id || currentUserId || null;
                    reviewContext = {
                        requestId: requestId,
                        shopId: currentDispatchShopId,
                        motoristId: currentMotoristId,
                    };
                    showStatusStrip('completed', d.mechanic_name, d.mechanic_phone, d.mechanic_plate);
                    subscribeToDispatch(requestId);
                    document.getElementById('reqBadge').classList.add('show');
                } else {
                    LS.del('mf_current_request_id');
                    currentRequestId = null;
                }
            } catch {}
        }

        function showStatusStrip(status, mechName, mechPhone, mechPlate = null) {
            updateRescueBar(status, mechName, mechPhone, mechPlate);
        }

        function updateMechanicInfo(status, name, phone, plate = null) {
            const info = document.getElementById('barMechInfo');
            const nameEl = document.getElementById('barMechName');
            const phoneEl = document.getElementById('barMechPhone');
            const plateWrap = document.getElementById('barMechPlate');
            const plateVal = document.getElementById('barMechPlateVal');
            const show = ['en_route', 'arrived'].includes(status) && name;
            if (!info) return;
            info.style.display = show ? 'flex' : 'none';
            if (show) {
                if (nameEl) nameEl.textContent = name;
                if (phoneEl) phoneEl.textContent = phone || 'Assigned mechanic';
                if (plateWrap && plateVal) {
                    if (plate) {
                        plateVal.textContent = plate;
                        plateWrap.style.display = 'flex';
                    } else {
                        plateWrap.style.display = 'none';
                    }
                }
                document.documentElement.style.setProperty('--bar-h', '130px');
            }
        }

        /* Step tracker state machine */
        function updateStepTrack(status) {
            const configs = {
                requested: {
                    dots: ['active', '', ''],
                    lines: [false, false]
                },
                accepted: {
                    dots: ['done', '', ''],
                    lines: [false, false]
                },
                en_route: {
                    dots: ['done', 'active', ''],
                    lines: [true, false]
                },
                arrived: {
                    dots: ['done', 'active', ''],
                    lines: [true, false]
                },
                completed: {
                    dots: ['done', 'done', 'done'],
                    lines: [true, true]
                },
            };
            const cfg = configs[status];
            if (!cfg) return;
            cfg.dots.forEach((cls, i) => {
                const el = document.getElementById('track-step-' + i);
                if (!el) return;
                el.className = 'step-dot';
                if (cls) el.classList.add(cls);
            });
            cfg.lines.forEach((done, i) => {
                const el = document.getElementById('track-line-' + i);
                if (el) el.className = 'step-line' + (done ? ' done' : '');
            });
            const subMsg = document.getElementById('barSubMsg');
            if (subMsg) subMsg.textContent = SUB_MSG[status] ?? '';
        }

        function updateRescueBar(status, mechName, mechPhone, mechPlate = null) {
            console.log('[updateRescueBar] status=', status, 'mechName=', mechName);
            const idle = document.getElementById('barIdle');
            const active = document.getElementById('barActive');
            const activeText = document.getElementById('barActiveText');
            const cancelBtn = document.getElementById('cancelBtn');
            const bar = document.getElementById('rescueBar');
            if (!status || ['completed', 'declined', 'cancelled'].includes(status)) {
                console.log('[updateRescueBar] entering final status block, status=', status);
                idle.style.display = 'flex';
                active.style.display = 'none';
                bar.classList.remove('bar-active');
                document.documentElement.style.setProperty('--bar-h', '78px');
                document.getElementById('rescueFab').style.display = 'flex';
                // hide mechanic chip on reset
                const info = document.getElementById('barMechInfo');
                if (info) info.style.display = 'none';
                clearRoute();
                _dispatchShopLat = null;
                _dispatchShopLng = null;
                if (allShops.length) renderShopPins(allShops);
                if (status === 'completed') {
                    console.log('[updateRescueBar] calling showReviewModal()');
                    const reviewRequestId = currentRequestId || reviewContext.requestId;
                    if (!currentDispatchShopId && reviewRequestId) {
                        console.log('[updateRescueBar] fetching completed request data before showing review modal');
                        fetch(`/motorist/request/${reviewRequestId}`)
                            .then(r => r.ok ? r.json() : Promise.reject('Request failed'))
                            .then(d => {
                                currentDispatchShopId = d?.shop_id || currentDispatchShopId;
                                currentMotoristId = d?.motorist_id || currentUserId || currentMotoristId;
                                reviewContext = {
                                    requestId: reviewRequestId,
                                    shopId: currentDispatchShopId,
                                    motoristId: currentMotoristId,
                                };
                                window.showReviewModal();
                            })
                            .catch(err => {
                                console.error('[updateRescueBar] failed to fetch completed request before modal', err);
                                window.showReviewModal();
                            });
                    } else {
                        window.showReviewModal();
                    }
                }
                return;
            }
            idle.style.display = 'none';
            active.style.display = 'flex';
            bar.classList.add('bar-active');
            document.documentElement.style.setProperty('--bar-h', '96px');
            activeText.textContent = STATUS_TITLE[status] ?? status;
            cancelBtn.style.display = status === 'requested' ? 'flex' : 'none';
            updateStepTrack(status);
            updateMechanicInfo(status, mechName, mechPhone, mechPlate);
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
                    stopStatusPolling();
                    LS.del('mf_current_request_id');
                    currentRequestId = null;
                    if (pusherClient) pusherClient.disconnect();
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
        // Global transition timer and configurable duration (ms)
        let tabTransitionTimer = null;
        window.MF_TAB_TRANS_MS = window.MF_TAB_TRANS_MS || 340;

        function getMotoristHashState() {
            const raw = window.location.hash.replace('#', '');
            const [hashValue, hashQuery = ''] = raw.split('?');
            const params = new URLSearchParams(hashQuery);
            return {
                hashValue: hashValue || '',
                requestId: params.get('request')
            };
        }

        function updateMotoristHash(tab, panel, requestId) {
            let hash = '';
            if (panel) {
                hash = panel + (requestId ? '?request=' + encodeURIComponent(requestId) : '');
            } else if (tab) {
                hash = tab;
            }
            const url = window.location.pathname + window.location.search + (hash ? '#' + hash : '');
            if (window.location.href !== url) {
                history.replaceState(null, '', url);
            }
        }

        function setTabTransition(ms) {
            window.MF_TAB_TRANS_MS = Number(ms) || 340;
        }

        function showTab(tab) {
            updateMotoristHash(tab);

            ['map', 'requests', 'profile'].forEach(t => {
                document.getElementById('nav' + t[0].toUpperCase() + t.slice(1))
                    .classList.toggle('active', t === tab);
            });
            LS.set('mf_motorist_active_tab', tab);
            // panels that use slide animation
            const panels = ['requestsPanel','profilePanel','rescuePanel','shopsPanel','requestChatPanel'];
            const currentlyOpen = panels.find(id => document.getElementById(id) && document.getElementById(id).classList.contains('open'));

            function openTarget() {
                if (tab === 'map') {
                    // nothing to open; keep map visible
                    setTimeout(() => { try { map.invalidateSize(); } catch {} }, 50);
                    return;
                }
                if (tab === 'requests') {
                    document.getElementById('requestsList').style.display = 'block';
                    openPanel('requestsPanel');
                    loadRequests();
                    return;
                }
                if (tab === 'profile') {
                    renderProfileSummary();
                    openPanel('profilePanel');
                    return;
                }
            }

            // If nothing is open, just open the target (fast path)
            if (!currentlyOpen) {
                // cancel any pending timer
                if (tabTransitionTimer) { clearTimeout(tabTransitionTimer); tabTransitionTimer = null; }
                openTarget();
                if (tab === 'map') setTimeout(() => map.invalidateSize(), window.MF_TAB_TRANS_MS + 50);
                return;
            }

            // If the target panel is already open, do nothing
            if ((tab === 'requests' && currentlyOpen === 'requestsPanel') || (tab === 'profile' && currentlyOpen === 'profilePanel')) {
                return;
            }

            // Close currently open panel(s), cancel any pending open, and schedule opening of new target.
            panels.forEach(id => closePanel(id));
            if (tabTransitionTimer) clearTimeout(tabTransitionTimer);
            tabTransitionTimer = setTimeout(() => {
                openTarget();
                tabTransitionTimer = null;
                if (tab === 'map') setTimeout(() => map.invalidateSize(), 50);
            }, window.MF_TAB_TRANS_MS);
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
            const patch = {
                vehicle_make_model: document.getElementById('pMakeModel').value.trim(),
                vehicle_variant_color: document.getElementById('pColor').value.trim(),
                plate_temp_number: document.getElementById('pPlate').value.trim(),
            };
            mfSaveProfile(patch);
            closeSubPanel('editMotoPanel');
        }

        function saveContact() {
            const patch = {
                owner_name: document.getElementById('pName').value.trim(),
                contact_number: document.getElementById('pContact').value.trim(),
            };
            mfSaveProfile(patch);
            closeSubPanel('editContactPanel');
        }

        /* ── REQUEST HISTORY ── */
        function getRequestTokenEntry(requestId) {
            const tokens = JSON.parse(LS.get('mf_request_tokens') ?? '{}');
            return tokens?.[requestId] ?? null;
        }

        function saveRequestToken(requestId, guestToken) {
            if (!guestToken) return;
            const tokens = JSON.parse(LS.get('mf_request_tokens') ?? '{}');
            if (tokens[requestId] !== guestToken) {
                tokens[requestId] = guestToken;
                LS.set('mf_request_tokens', JSON.stringify(tokens));
            }
        }

        function saveRequestHistory(entry) {
            const identity = typeof mfIdentity === 'function' ? mfIdentity() : { guest_token: null };
            const token = identity.guest_token || currentRequestGuestToken || getRequestTokenEntry(entry.id) || null;
            if (token) {
                saveRequestToken(entry.id, token);
            }
            const h = JSON.parse(LS.get('mf_request_history') ?? '[]');
            const next = [
                { ...entry, guest_token: token, time: new Date().toISOString() },
                ...h.filter(item => `${item.id}` !== `${entry.id}`)
            ];
            LS.set('mf_request_history', JSON.stringify(next.slice(0, 10)));
        }

        function updateRequestHistoryGuestToken(requestId, guestToken) {
            if (!guestToken) return;
            saveRequestToken(requestId, guestToken);
            const h = JSON.parse(LS.get('mf_request_history') ?? '[]');
            let updated = false;
            const next = h.map(entry => {
                if (`${entry.id}` === `${requestId}` && entry.guest_token !== guestToken) {
                    updated = true;
                    return { ...entry, guest_token: guestToken };
                }
                return entry;
            });
            if (updated) {
                LS.set('mf_request_history', JSON.stringify(next));
            }
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
    <div style="display:flex;gap:8px;margin-top:12px;">
      <button onclick="loadRequestDetailsForCard('${id}')" style="flex:1;padding:8px 12px;background-color:rgba(255,255,255,0.1);border:1px solid black;border-radius:8px;text-align:center;font-size:12px;color:inherit;cursor:pointer;transition:background-color 0.2s;" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.15)'" onmouseout="this.style.backgroundColor='rgba(255,255,255,0.1)'">View</button>
      <button onclick="openRequestChat('${id}')" style="flex:1;padding:8px 12px;background-color:#F7941D;color:black;border:none;border-radius:8px;text-align:center;font-size:12px;font-weight:600;transition:background-color 0.2s;cursor:pointer;" onmouseover="this.style.backgroundColor='#ff9e2a'" onmouseout="this.style.backgroundColor='#F7941D'">Message</button>
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

        let requestChatId = null;
        let requestChatConversation = 'motorist';
        let requestChatMessageIds = new Set();
        let requestChatStatus = null;
        let requestChatTimer = null;
        let requestChatGuestToken = null;

        function getRequestHistoryEntry(requestId) {
            const hist = JSON.parse(LS.get('mf_request_history') ?? '[]');
            return hist.find(entry => `${entry.id}` === `${requestId}`) || null;
        }

        async function ensureRequestChatGuestToken(requestId, forceServer = false) {
            if (!forceServer && requestChatGuestToken) {
                return requestChatGuestToken;
            }

            const identity = typeof mfIdentity === 'function' ? mfIdentity() : { guest_token: null };
            const historyEntry = getRequestHistoryEntry(requestId);

            const tokenEntry = getRequestTokenEntry(requestId);
            if (!forceServer && tokenEntry) {
                requestChatGuestToken = tokenEntry;
                return requestChatGuestToken;
            }

            if (!forceServer && historyEntry?.guest_token) {
                requestChatGuestToken = historyEntry.guest_token;
                return requestChatGuestToken;
            }

            if (!forceServer && requestId === currentRequestId && currentRequestGuestToken) {
                requestChatGuestToken = currentRequestGuestToken;
                return requestChatGuestToken;
            }

            if (!forceServer && requestId === currentRequestId && identity.guest_token) {
                requestChatGuestToken = identity.guest_token;
                return requestChatGuestToken;
            }

            try {
                const requestStatusUrl = `/motorist/request/${requestId}${forceServer ? '?_=' + Date.now() : ''}`;
                const res = await fetch(requestStatusUrl);
                if (!res.ok) return requestChatGuestToken;
                const data = await res.json();
                if (data && data.guest_token) {
                    requestChatGuestToken = data.guest_token;
                    saveRequestToken(requestId, requestChatGuestToken);
                    updateRequestHistoryGuestToken(requestId, requestChatGuestToken);
                    if (requestId === currentRequestId) {
                        currentRequestGuestToken = requestChatGuestToken;
                        LS.set('mf_current_request_id', JSON.stringify({
                            id: currentRequestId,
                            guest_token: currentRequestGuestToken
                        }));
                    }
                }
            } catch (e) {
                console.warn('Unable to resolve guest token from request status', e);
            }
            return requestChatGuestToken;
        }

        function switchRequestChatTab(tab) {
            requestChatConversation = tab === 'shop' ? 'motorist' : 'mechanic';
            document.getElementById('chatTabMechanic').classList.toggle('active', requestChatConversation === 'mechanic');
            document.getElementById('chatTabShop').classList.toggle('active', requestChatConversation === 'motorist');
            loadRequestChatMessages();
        }

        async function openRequestChat(requestId) {
            // ensure requests panel is visible first (triggers slide if needed)
            showTab('requests');
            // cancel any pending scheduled chat open
            if (requestChatTimer) { clearTimeout(requestChatTimer); requestChatTimer = null; }

            const historyEntry = getRequestHistoryEntry(requestId);
            const identity = typeof mfIdentity === 'function' ? mfIdentity() : { guest_token: null };
            requestChatGuestToken = getRequestTokenEntry(requestId) || historyEntry?.guest_token || (requestId === currentRequestId ? currentRequestGuestToken : null) || identity.guest_token || null;
            if (!requestChatGuestToken) {
                await ensureRequestChatGuestToken(requestId, true);
            }

            const doOpenChat = () => {
                // animate hiding the list then open chat
                const list = document.getElementById('requestsList');
                if (list) {
                    list.classList.add('hidden-for-chat');
                }
                // small overlap so fade starts before chat slides in
                const OPEN_DELAY = 80;
                setTimeout(() => {
                    if (list) list.style.display = 'none';
                        requestChatId = requestId;
                    requestChatMessageIds.clear();
                    document.getElementById('chatPanelTitle').textContent = 'Chat Request #' + requestId;
                    document.getElementById('chatPanelSubtitle').textContent = 'Messages with your shop and mechanic';
                    document.getElementById('requestChatBody').innerHTML = '<div class="chat-empty">Loading messages…</div>';
                    document.getElementById('requestChatClosed').style.display = 'none';
                    document.getElementById('requestChatInput').disabled = false;
                    document.getElementById('requestChatForm').style.display = 'flex';
                    document.getElementById('chatTabMechanic').classList.toggle('active', true);
                    document.getElementById('chatTabShop').classList.toggle('active', false);
                    requestChatConversation = 'mechanic';
                    if (!requestChatGuestToken) {
                        const historyEntry = getRequestHistoryEntry(requestId);
                        requestChatGuestToken = historyEntry?.guest_token || currentRequestGuestToken || null;
                    }
                    openPanel('requestChatPanel');
                    const chatPanel = document.getElementById('requestChatPanel');
                    if (chatPanel) {
                        chatPanel.classList.add('fullscreen');
                        document.getElementById('mfApp')?.classList.add('chat-open');
                    }
                    loadRequestChatDetails();
                    loadRequestChatMessages();
                    updateMotoristHash(null, 'requestChatPanel', requestId);
                }, OPEN_DELAY);
            };

            // If requests panel is already open, open chat immediately; otherwise wait for tab transition
            const requestsPanel = document.getElementById('requestsPanel');
            if (requestsPanel && requestsPanel.classList.contains('open')) {
                doOpenChat();
            } else {
                requestChatTimer = setTimeout(() => { doOpenChat(); requestChatTimer = null; }, window.MF_TAB_TRANS_MS + 20);
            }
        }

        function closeRequestChat() {
            // close chat panel with slide; then restore request list after transition
            if (requestChatTimer) { clearTimeout(requestChatTimer); requestChatTimer = null; }
            // slide chat out
            const chatPanel = document.getElementById('requestChatPanel');
            if (chatPanel) {
                chatPanel.classList.remove('fullscreen');
            }
            document.getElementById('mfApp')?.classList.remove('chat-open');
            closePanel('requestChatPanel');
            // after chat slide finishes, show list and animate it back in
            setTimeout(() => {
                const list = document.getElementById('requestsList');
                if (list) {
                    list.style.display = 'block';
                    // allow a frame then remove hidden class to trigger fade-in
                    requestAnimationFrame(() => {
                        requestAnimationFrame(() => list.classList.remove('hidden-for-chat'));
                    });
                }
                requestChatId = null;
                requestChatGuestToken = null;
            }, window.MF_TAB_TRANS_MS);
            // ensure requests tab is active
            showTab('requests');
            updateMotoristHash('requests');
        }

        async function loadRequestChatDetails() {
            if (!requestChatId) return;
            try {
                const res = await fetch(`/motorist/request/${requestChatId}`);
                if (!res.ok) return;
                const data = await res.json();
                requestChatStatus = data.status;
                const title = data.shop_name ? (data.shop_name + ' Chat') : 'Request Chat';
                document.getElementById('chatPanelTitle').textContent = title;
                if (data.mechanic_name) {
                    document.getElementById('chatPanelSubtitle').textContent = `Mechanic: ${data.mechanic_name}`;
                } else {
                    document.getElementById('chatPanelSubtitle').textContent = 'Messages with your shop and mechanic';
                }
                if (requestChatStatus === 'completed' || requestChatStatus === 'cancelled') {
                    document.getElementById('requestChatClosed').style.display = 'block';
                    document.getElementById('requestChatInput').disabled = true;
                    document.getElementById('requestChatForm').style.display = 'none';
                }
            } catch (e) {
                console.error('Unable to load request chat details', e);
            }
        }

        async function loadRequestChatMessages() {
            if (!requestChatId) return;
            const body = document.getElementById('requestChatBody');
            body.innerHTML = '<div class="chat-empty">Loading messages…</div>';
            try {
                await ensureRequestChatGuestToken(requestChatId);
                let token = requestChatGuestToken || null;
                let guestQuery = token ? `&guest_token=${encodeURIComponent(token)}` : '';
                let res = await fetch(`/api/chat/${requestChatId}?conversation_type=${requestChatConversation}${guestQuery}`);

                if (res.status === 403) {
                    requestChatGuestToken = null;
                    await ensureRequestChatGuestToken(requestChatId, true);
                    token = requestChatGuestToken || null;
                    guestQuery = token ? `&guest_token=${encodeURIComponent(token)}` : '';
                    res = await fetch(`/api/chat/${requestChatId}?conversation_type=${requestChatConversation}${guestQuery}`);
                }

                if (!res.ok) {
                    if (res.status === 403) {
                        body.innerHTML = '<div class="chat-empty">You are not allowed to view this chat.</div>';
                    } else {
                        body.innerHTML = '<div class="chat-empty">Unable to load messages.</div>';
                    }
                    return;
                }
                const data = await res.json();
                if (!data.success || !Array.isArray(data.messages)) {
                    body.innerHTML = '<div class="chat-empty">No messages yet.</div>';
                    return;
                }
                if (!data.messages.length) {
                    body.innerHTML = '<div class="chat-empty">No messages yet. Say hello to your shop or mechanic.</div>';
                    return;
                }
                body.innerHTML = '';
                requestChatMessageIds.clear();
                data.messages.forEach(msg => appendRequestChatMessage(msg));
                body.scrollTop = body.scrollHeight;
            } catch (e) {
                body.innerHTML = '<div class="chat-empty">Unable to load messages.</div>';
                console.error(e);
            }
        }

        function appendRequestChatMessage(msg) {
            if (requestChatMessageIds.has(msg.id)) return;
            requestChatMessageIds.add(msg.id);
            const wrapper = document.createElement('div');
            const mine = msg.sender_type === 'motorist';
            wrapper.className = mine ? 'bubble-own' : 'bubble-other';
            wrapper.innerHTML = `
                <div class="chat-bubble">
                    <div class="bubble-content">${escapeHtml(msg.message)}</div>
                    <div class="bubble-meta">${escapeHtml(msg.sender_type === 'motorist' ? 'You' : msg.sender_name || msg.sender_type)} • ${escapeHtml(formatTime(msg.created_at))}</div>
                </div>
            `;
            const body = document.getElementById('requestChatBody');
            body.appendChild(wrapper);
        }

        document.getElementById('requestChatForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            if (!requestChatId) return;
            const input = document.getElementById('requestChatInput');
            const message = input.value.trim();
            if (!message) return;
            input.disabled = true;
            try {
                await ensureRequestChatGuestToken(requestChatId);
                let token = requestChatGuestToken || null;
                let res = await fetch('/api/messages', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.csrfToken
                    },
                    body: JSON.stringify({
                        dispatch_id: requestChatId,
                        message,
                        sender_type: 'motorist',
                        motorist_id: currentUserId || null,
                        guest_token: token,
                        conversation_type: requestChatConversation,
                    })
                });

                if (res.status === 403) {
                    requestChatGuestToken = null;
                    await ensureRequestChatGuestToken(requestChatId, true);
                    token = requestChatGuestToken || null;
                    res = await fetch('/api/messages', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': window.csrfToken
                        },
                        body: JSON.stringify({
                            dispatch_id: requestChatId,
                            message,
                            sender_type: 'motorist',
                            motorist_id: currentUserId || null,
                            guest_token: token,
                            conversation_type: requestChatConversation,
                        })
                    });
                }

                if (!res.ok) {
                    if (res.status === 403) {
                        alert('You are not allowed to send this message.');
                    } else {
                        alert('Failed to send message.');
                    }
                    return;
                }

                const data = await res.json();
                if (data.success) {
                    input.value = '';
                    loadRequestChatMessages();
                } else {
                    alert(data.message || 'Failed to send message.');
                }
            } catch (err) {
                console.error(err);
                alert('Failed to send message.');
            } finally {
                input.disabled = false;
            }
        });

        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
        }

        function parseTimestamp(value) {
            if (!value) return null;
            let date = new Date(value);
            if (isNaN(date.getTime()) && typeof value === 'string') {
                date = new Date(value.replace(' ', 'T'));
            }
            return isNaN(date.getTime()) ? null : date;
        }

        function formatTime(value) {
            const date = parseTimestamp(value);
            return date ? date.toLocaleString([], { hour: 'numeric', minute: '2-digit' }) : value || '';
        }

        /* ── REQUEST DETAILS MODAL ── */
        function closeDetailsModal() {
            const modal = document.getElementById('detailsModal');
            if (modal) modal.style.display = 'none';
        }

        async function loadRequestDetailsForCard(requestId) {
            const modal = document.getElementById('detailsModal');
            if (!modal) return;

            try {
                const res = await fetch(`/motorist/request/${requestId}`);
                if (!res.ok) return;
                const data = await res.json();

                document.getElementById('detailIssueType').textContent = data.issue_type || 'No issue';
                document.getElementById('detailLocation').textContent = data.location || 'No location';
                document.getElementById('detailStatus').textContent = (STATUS_TEXT[data.status] || data.status || 'Unknown');
                const detailTime = parseTimestamp(data.created_at);
                document.getElementById('detailTime').textContent = detailTime ? detailTime.toLocaleString() : 'Unknown';
                document.getElementById('detailShop').textContent = data.shop_name || 'Finding shop...';
                document.getElementById('detailMechanic').textContent = data.mechanic_name || 'Not assigned yet';

                modal.style.display = 'block';
            } catch (e) {
                console.error('Failed to load request details:', e);
            }
        }

        function goToMessage() {
            if (!currentRequestId) {
                alert('No active request');
                return;
            }
            openRequestChat(currentRequestId);
        }

        async function showDetailsModal() {
            if (!currentRequestId) return;
            const modal = document.getElementById('detailsModal');
            if (!modal) return;

            try {
                const res = await fetch(`/motorist/request/${currentRequestId}`);
                if (!res.ok) return;
                const data = await res.json();

                document.getElementById('detailIssueType').textContent = data.issue_type || 'No issue';
                document.getElementById('detailLocation').textContent = data.location || 'No location';
                document.getElementById('detailStatus').textContent = (STATUS_TEXT[data.status] || data.status || 'Unknown');
                const detailTime = parseTimestamp(data.created_at);
                document.getElementById('detailTime').textContent = detailTime ? detailTime.toLocaleString() : 'Unknown';
                document.getElementById('detailShop').textContent = data.shop_name || 'Finding shop...';
                document.getElementById('detailMechanic').textContent = data.mechanic_name || 'Not assigned yet';

                modal.style.display = 'block';
            } catch (e) {
                console.error('Failed to load request details:', e);
            }
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('detailsModal');
            if (modal && e.target === modal) {
                closeDetailsModal();
            }
        });

        /* ══════════════════════════════════════════════
           REVIEW SYSTEM - COMPLETELY REWRITTEN
           ══════════════════════════════════════════════ */
        
        window.reviewData = {
            rating: 0,
            tags: new Set(),
            comment: ''
        };

        window.showReviewModal = function() {
            console.log('[showReviewModal] OPENING REVIEW MODAL');
            const modal = document.getElementById('reviewModal');
            const overlay = document.getElementById('reviewNotificationOverlay');
            const ratingLabel = document.getElementById('ratingLabel');
            const commentField = document.getElementById('reviewCommentField');
            const charCountDisplay = document.getElementById('charCountDisplay');

            if (!modal || !overlay) {
                console.error('Modal elements not found!');
                return;
            }

            window.reviewData = { rating: 0, tags: new Set(), comment: '' };
            if (ratingLabel) {
                ratingLabel.textContent = 'Select rating';
                ratingLabel.style.color = '#777';
            }
            if (commentField) {
                commentField.value = '';
            }
            if (charCountDisplay) {
                charCountDisplay.textContent = '0';
            }

            document.querySelectorAll('.rating-star').forEach(btn => {
                btn.style.color = '#444';
                btn.style.fontSize = '36px';
            });

            document.querySelectorAll('.tag-btn').forEach(btn => {
                btn.style.background = 'rgba(255,255,255,0.04)';
                btn.style.borderColor = 'rgba(255,255,255,0.1)';
                btn.style.color = '#999';
                const icon = btn.querySelector('i');
                if (icon) icon.style.opacity = '0';
            });

            overlay.style.display = 'block';
            overlay.style.pointerEvents = 'auto';
            overlay.style.opacity = '1';
            overlay.style.zIndex = '99999';
            modal.style.display = 'block';
            modal.style.zIndex = '100000';
            document.body.style.overflow = 'hidden';

            overlay.addEventListener('click', window.hideReviewModal, { once: true });
            console.log('[showReviewModal] MODAL DISPLAYED');
        };

        window.hideReviewModal = function() {
            const modal = document.getElementById('reviewModal');
            const overlay = document.getElementById('reviewNotificationOverlay');
            if (modal) modal.style.display = 'none';
            if (overlay) overlay.style.display = 'none';
            document.body.style.overflow = '';
        };

        window.setReviewRating = function(rating) {
            console.log('[setReviewRating] rating:', rating);
            window.reviewData.rating = rating;
            
            const labels = ['', 'Poor', 'Fair', 'Good', 'Great', 'Excellent'];
            document.getElementById('ratingLabel').textContent = labels[rating] || 'Select rating';
            document.getElementById('ratingLabel').style.color = rating > 0 ? '#F7941D' : '#888';
            
            // Update stars
            document.querySelectorAll('.rating-star').forEach((btn, idx) => {
                if (idx + 1 <= rating) {
                    btn.style.color = '#F7941D';
                    btn.style.fontSize = '40px';
                } else {
                    btn.style.color = '#444';
                    btn.style.fontSize = '36px';
                }
            });
        };

        window.toggleTag = function(tag) {
            console.log('[toggleTag] tag:', tag);
            const btn = document.querySelector(`[data-tag="${tag}"]`);
            if (!btn) return;
            
            if (window.reviewData.tags.has(tag)) {
                window.reviewData.tags.delete(tag);
                btn.style.background = 'rgba(255,255,255,0.04)';
                btn.style.borderColor = 'rgba(255,255,255,0.1)';
                btn.style.color = '#999';
                const icon = btn.querySelector('i');
                if (icon) icon.style.opacity = '0';
            } else {
                window.reviewData.tags.add(tag);
                btn.style.background = 'rgba(247, 148, 29, 0.15)';
                btn.style.borderColor = '#F7941D';
                btn.style.color = '#F7941D';
                const icon = btn.querySelector('i');
                if (icon) icon.style.opacity = '1';
            }
        };

        window.submitReview = async function() {
            console.log('[submitReview] starting submission');
            console.log('[submitReview] currentDispatchShopId:', currentDispatchShopId);
            console.log('[submitReview] currentRequestId:', currentRequestId);
            console.log('[submitReview] currentMotoristId:', currentMotoristId);
            console.log('[submitReview] currentUserId:', currentUserId);
            
            if (window.reviewData.rating === 0) {
                showToast('Please select a rating', 'warning');
                return;
            }
            
            // Try to get shop_id, it might not be set during modal opening
            let shopId = currentDispatchShopId || reviewContext.shopId;
            let requestId = currentRequestId || reviewContext.requestId;
            let motoristId = currentMotoristId || reviewContext.motoristId || currentUserId || null;

            if (!shopId && requestId) {
                console.log('[submitReview] shop_id not set, trying to fetch...');
                try {
                    const res = await fetch(`/motorist/request/${requestId}`);
                    const data = await res.json();
                    shopId = data?.shop_id;
                    currentDispatchShopId = shopId;
                    reviewContext.shopId = shopId;
                    reviewContext.requestId = requestId;
                    reviewContext.motoristId = motoristId;
                    console.log('[submitReview] fetched shop_id:', shopId);
                } catch (e) {
                    console.error('[submitReview] failed to fetch shop_id:', e);
                }
            }
            
            if (!shopId) {
                console.error('Shop ID still not set:', shopId);
                showToast('Error: Shop information missing. Please try again.', 'error');
                return;
            }
            
            const payload = {
                dispatch_id: requestId || null,
                shop_id: shopId,
                motorist_id: motoristId,
                rating: window.reviewData.rating,
                comment: document.getElementById('reviewCommentField').value || '',
                services: Array.from(window.reviewData.tags).join(', ')
            };
            
            console.log('[submitReview] payload:', payload);
            
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.textContent = 'Submitting...';
            
            try {
                const response = await fetch(reviewEndpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify(payload)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    console.log('[submitReview] success');
                    showToast('Thank you! Your review has been submitted.', 'success');
                    window.hideReviewModal();
                } else {
                    console.error('[submitReview] failed:', data.message);
                    showToast(data.message || 'Failed to submit review', 'error');
                    btn.disabled = false;
                    btn.textContent = 'Submit Review';
                }
            } catch (error) {
                console.error('[submitReview] error:', error);
                showToast('Error submitting review', 'error');
                btn.disabled = false;
                btn.textContent = 'Submit Review';
            }
        };

        window.skipReview = function() {
            console.log('[skipReview] skipping review');
            window.hideReviewModal();
        };

        // Character counter
        document.addEventListener('DOMContentLoaded', function() {
            const field = document.getElementById('reviewCommentField');
            if (field) {
                field.addEventListener('input', function() {
                    document.getElementById('charCountDisplay').textContent = this.value.length;
                });
            }
        });

    </script>
@endsection
