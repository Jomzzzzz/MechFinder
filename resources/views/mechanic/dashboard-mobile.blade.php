@extends('layouts.mechanic-mobile')

@section('main-class', '')

@section('content')
    <style>
        html,
body {
    margin: 0;
    padding: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
}

#mfApp {
    position: relative;
    width: 100%;
    height: 100vh;
    height: 100svh;
    height: 100dvh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    background: #F4F6F8;
}

        .top-header {
            flex-shrink: 0;
            background: transparent;
            padding: 14px 16px 10px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .top-header-logo {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            background: var(--brand);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: #fff;
            flex-shrink: 0;
        }

        .top-header-title {
            flex: 1;
        }

        .top-header-title h1 {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-1);
            margin: 0;
            letter-spacing: -.02em;
        }

        .top-header-sub {
            font-size: 12px;
            color: var(--text-2);
            margin: 4px 0 0;
        }

        #mechanicPanels {
            position: relative;
            flex: 1;
            min-height: 0;
            overflow: hidden;
        }

        .panel {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            transform: translateY(100%);
            transition: transform .33s cubic-bezier(.4, 0, .2, 1);
            background: transparent;
            overflow: hidden;
        }

        .panel.open {
            transform: translateY(0);
        }

        .chat-tab {
            min-width: 0;
            flex: 1;
            padding: 11px 14px;
            border: none;
            border-bottom: 2px solid transparent;
            background: transparent;
            color: #475569;
            font-size: 13px;
            font-weight: 700;
            transition: color .2s ease, border-color .2s ease;
        }

        .chat-tab.active {
            color: #111827;
            border-bottom-color: #f7941d;
        }

        .chat-item {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 14px;
            transition: background .2s ease, border-color .2s ease, box-shadow .2s ease;
        }

        .chat-link {
            display: block;
            text-decoration: none;
        }

        .chat-link:hover .chat-item {
            border-color: transparent;
            background: #f8fafc;
            box-shadow: 0 6px 18px rgba(15, 23, 42, .06);
        }

        .profile-box {
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            background: #fff;
            padding: 18px;
        }

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

        .prof-row.no-action {
            cursor: default;
        }

        .prof-row.no-action:hover {
            background: var(--surface);
            box-shadow: none;
        }

        .sub-panel {
            position: absolute;
            inset: 0;
            background: #F4F6F8;
            z-index: 50;
            display: none;
            transform: translateY(100%);
            transition: transform .3s ease;
            overflow: hidden;
        }

        .sub-panel.open {
            display: block;
            transform: translateY(0);
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

        .ph-note {
            padding: 12px 14px;
            font-size: 12px;
            line-height: 1.55;
            color: var(--text-2);
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

        .profile-label {
            display: block;
            margin-bottom: .55rem;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-2);
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .profile-field {
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

        .profile-field:focus {
            border-color: var(--action);
        }

        .profile-field::placeholder {
            color: var(--text-3);
        }

        .button-primary,
        .button-secondary {
            width: 100%;
            border-radius: var(--r2);
            padding: 14px;
            font-size: 15px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: opacity .15s;
        }

        .button-primary {
            background: var(--action);
            color: #fff;
        }

        .button-primary:disabled {
            opacity: .35;
            cursor: not-allowed;
        }

        .button-primary:not(:disabled):active {
            opacity: .85;
        }

        .button-secondary {
            background: #fff;
            color: var(--text-1);
            border: 1.5px solid var(--border);
        }

        .button-secondary:hover {
            background: var(--surface-2);
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
            transition: opacity .15s;
        }

        .logout-btn:active {
            opacity: .85;
        }

        .alert {
            border-radius: 16px;
            border: 1px solid #d1fae5;
            background: #ecfdf5;
            color: #065f46;
            padding: 14px 16px;
            font-size: 13px;
        }

        .form-error {
            margin-top: 4px;
            font-size: 12px;
            color: var(--red);
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

        .panel-body {
            flex: 1;
            min-height: 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .content-scroll {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: calc(var(--nav-h) + 14px);
        }

        .profile-content {
            padding-bottom: calc(var(--nav-h) + 14px);
        }

        .mechanic-tab-panels {
            position: relative;
        }

        .mechanic-tab-panel {
            opacity: 0;
            transform: translateY(10px);
            max-height: 0;
            overflow: hidden;
            pointer-events: none;
            transition: opacity .28s ease, transform .28s ease, max-height .28s ease;
        }

        .mechanic-tab-panel.open {
            opacity: 1;
            transform: translateY(0);
            max-height: 9999px;
            pointer-events: auto;
        }

        .mechanic-content {
            flex: 1;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            padding: 0 14px calc(var(--nav-h) + 12px);
            display: flex;
            flex-direction: column;
            gap: 12px;
            background: transparent;
            width: 100%;
            max-width: 100%;
        }

        .job-card,
        .profile-box,
        .job-map-wrap,
        .job-map {
            width: 100%;
            max-width: 100%;
        }

        .job-card {
            background: var(--surface);
            border-radius: 18px;
            padding: 16px;
            border: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            gap: 12px;
            box-shadow: 0 1px 4px rgba(15, 23, 42, .06);
        }

        .job-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
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
            color: var(--text-3);
            margin: 4px 0 0;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 5px 11px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
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
            gap: 6px;
            margin: 4px 0 0;
        }

        .rs-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
            background: var(--border-2);
            transition: background .3s ease;
        }

        .rs-dot.done,
        .rs-dot.active {
            background: var(--brand);
        }

        .rs-line {
            flex: 1;
            height: 2px;
            background: var(--border);
            transition: background .3s ease;
        }

        .rs-line.done {
            background: var(--brand);
        }

        .status-strip {
            background: rgba(247, 148, 29, .08);
            border-radius: 14px;
            padding: 10px 12px;
            font-size: 13px;
            font-weight: 700;
            color: var(--brand-dk);
            line-height: 1.45;
        }

        .job-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: stretch;
            width: 100%;
        }

        .act-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 11px 14px;
            border-radius: 14px;
            border: none;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: transform .15s ease, background .15s ease;
            text-decoration: none;
            letter-spacing: .02em;
            -webkit-tap-highlight-color: transparent;
            min-width: 0;
            flex: 1;
        }

        .act-btn:active {
            transform: translateY(1px);
        }

        .act-btn-primary {
            background: var(--brand);
            color: #000;
            flex: 1;
        }

        .act-btn-secondary {
            background: #fff;
            color: var(--text-1);
            border: 1px solid rgba(220, 226, 235, .9);
        }

        /* ── JOB MAP ── */
        .job-map-wrap {
            position: relative;
            display: block;
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid rgba(220, 226, 235, .9);
            text-decoration: none;
        }

        .job-map {
            width: 100%;
            height: 160px;
            background: #EFF3F7;
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
            background: #fff;
            box-shadow: 0 8px 20px rgba(0, 0, 0, .08);
            color: var(--action);
            border: 1px solid rgba(30, 41, 59, .1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
            pointer-events: all;
        }

        .job-map-dir:active {
            background: #F5F6F8;
        }

        @media (max-width: 420px) {
            .top-header {
                padding: 12px 12px;
                gap: 10px;
            }

            .top-header-logo {
                width: 34px;
                height: 34px;
                font-size: 14px;
            }

            .top-header-title h1 {
                font-size: 16px;
            }

            .mechanic-content {
                padding: 10px 12px calc(var(--nav-h) + 12px);
                gap: 8px;
            }

            .job-card {
                padding: 12px;
            }

            .job-header {
                gap: 6px;
            }

            .job-title h3 {
                font-size: 14px;
            }

            .status-strip {
                padding: 7px 10px;
                font-size: 11px;
            }

            .job-actions {
                gap: 6px;
                flex-direction: column;
            }

            .act-btn {
                padding: 10px 12px;
                font-size: 11px;
                width: 100%;
            }

            .job-map {
                height: 140px;
            }

            .job-map-dir {
                width: 36px;
                height: 36px;
            }

            .no-jobs {
                padding: 46px 16px 16px;
            }

            .no-jobs-icon {
                width: 64px;
                height: 64px;
                font-size: 26px;
            }
        }

        @media (max-width: 340px) {
            .top-header {
                padding: 10px 10px;
            }

            .mechanic-content {
                padding: 10px 10px calc(var(--nav-h) + 10px);
            }

            .job-card {
                padding: 10px;
            }

            .act-btn {
                padding: 10px 10px;
                font-size: 10px;
            }
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
            padding: 40px 20px 20px;
            text-align: center;
            gap: 14px;
            background: #fff;
            border-radius: 18px;
            border: 1px solid rgba(220, 226, 235, .9);
        }

        .no-jobs-icon {
            width: 62px;
            height: 62px;
            border-radius: 16px;
            background: rgba(247, 148, 29, .12);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
        }

        .no-jobs-title {
            font-size: 16px;
            font-weight: 800;
            color: var(--text-1);
            margin: 0;
        }

        .no-jobs-text {
            font-size: 13px;
            color: var(--text-2);
            line-height: 1.6;
            max-width: 300px;
            margin: 0;
        }

        #bottomNav {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: min(100%, 430px);
            max-width: 390px;
            height: calc(var(--nav-h) + env(safe-area-inset-bottom));
            padding-bottom: env(safe-area-inset-bottom);
            z-index: 40;
            background: rgba(255, 255, 255, .94);
            border-top: 1px solid rgba(226, 232, 240, .95);
            display: flex;
            align-items: stretch;
            -webkit-tap-highlight-color: transparent;
            box-shadow: 0 -2px 12px rgba(15, 23, 42, .08);
            backdrop-filter: blur(12px);
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
            text-decoration: none;
            padding: 4px 0;
            min-height: var(--nav-h);
            position: relative;
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
        <div id="mechanicPanels">
            <section id="jobsPanel" class="panel open">
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
                                    data-lng="{{ $req->longitude }}" data-mech-lat="{{ $req->mechanic_lat ?? '' }}"
                                    data-mech-lng="{{ $req->mechanic_lng ?? '' }}"
                                    data-shop-lat="{{ $req->shop->latitude ?? '' }}"
                                    data-shop-lng="{{ $req->shop->longitude ?? '' }}"></div>
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

                            <button onclick="location.href='{{ route('mechanic.chat', ['dispatchId' => $req->id]) }}?conversation_type=motorist'"
                                class="act-btn act-btn-secondary">
                                <i class="fas fa-comments"></i> Chat
                            </button>

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
            </section>

            <section id="messagesPanel" class="panel">
                <div class="top-header">
                    <div class="top-header-logo"><i class="fas fa-comments"></i></div>
                    <div class="top-header-title">
                        <h1>Messages</h1>
                        <p class="top-header-sub">Shop and motorist conversations</p>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="px-4 py-4 border-b border-slate-200/70">
                        <div class="flex items-center gap-3 mb-4">
                            <input id="message-search" type="search" placeholder="Search conversations..." class="w-full rounded-[14px] border border-slate-300 px-4 py-3 text-[13px] text-slate-700 placeholder:text-slate-400 outline-none focus:border-[#F7941D] transition-colors" aria-label="Search conversations">
                        </div>
                        <div class="flex gap-2">
                            <button id="tab-shop" type="button" class="chat-tab active" onclick="switchMechanicMessageTab('shop')">Shop</button>
                            <button id="tab-motorist" type="button" class="chat-tab" onclick="switchMechanicMessageTab('motorist')">Motorist</button>
                        </div>
                    </div>
                    <div id="chat-list-panel" class="content-scroll px-4 py-4 space-y-3 mechanic-tab-panels">
                        <div id="shop-panel" class="mechanic-tab-panel open space-y-2">
                            @forelse($shopConversations as $conv)
                                <a href="{{ route('mechanic.chat', ['dispatchId' => $conv['dispatch_id']]) }}?conversation_type=shop" class="chat-item chat-link">
                                    <div class="flex gap-3 items-start">
                                        <div class="w-12 h-12 rounded-[12px] bg-slate-100 text-slate-900 border border-slate-200 flex items-center justify-center text-lg font-extrabold">
                                            {{ strtoupper(substr($conv['shop_name'] ?? 'S', 0, 1)) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-2 mb-1">
                                                <h3 class="text-[13px] font-semibold truncate">{{ $conv['shop_name'] }}</h3>
                                                <span class="text-[11px] text-slate-500 whitespace-nowrap" data-time>—</span>
                                            </div>
                                            <p class="text-[12px] text-slate-500 truncate mb-1">Motorist: {{ $conv['motorist_name'] }}</p>
                                            <p class="text-[12px] text-slate-500 truncate">{{ ucfirst(str_replace('_', ' ', $conv['issue_type'])) }}</p>
                                        </div>
                                        <div class="chat-badge hidden rounded-full bg-[#F7941D] px-2 py-1 text-[11px] font-semibold text-white" data-unread-count="{{ $conv['unread_count'] ?? 0 }}"></div>
                                    </div>
                                </a>
                            @empty
                                <div class="flex justify-center items-center h-32">
                                    <div class="text-center">
                                        <p class="text-slate-500 text-[13px] font-semibold">No shop conversations yet</p>
                                        <p class="text-[12px] text-slate-400">Accept a dispatch to start chatting.</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        <div id="motorist-panel" class="mechanic-tab-panel space-y-2">
                            @forelse($motoristConversations as $conv)
                                <a href="{{ route('mechanic.chat', ['dispatchId' => $conv['dispatch_id']]) }}?conversation_type=motorist" class="chat-item chat-link">
                                    <div class="flex gap-3 items-start">
                                        <div class="w-12 h-12 rounded-[12px] bg-slate-100 text-slate-900 border border-slate-200 flex items-center justify-center text-lg font-extrabold">
                                            {{ strtoupper(substr($conv['motorist_name'] ?? 'M', 0, 1)) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-2 mb-1">
                                                <h3 class="text-[13px] font-semibold truncate">{{ $conv['motorist_name'] }}</h3>
                                                <span class="text-[11px] text-slate-500 whitespace-nowrap" data-time>—</span>
                                            </div>
                                            <p class="text-[12px] text-slate-500 truncate mb-1">Shop: {{ $conv['shop_name'] }}</p>
                                            <p class="text-[12px] text-slate-500 truncate">{{ ucfirst(str_replace('_', ' ', $conv['issue_type'])) }}</p>
                                        </div>
                                        <div class="chat-badge hidden rounded-full bg-[#F7941D] px-2 py-1 text-[11px] font-semibold text-white" data-unread-count="{{ $conv['unread_count'] ?? 0 }}"></div>
                                    </div>
                                </a>
                            @empty
                                <div class="flex justify-center items-center h-32">
                                    <div class="text-center">
                                        <p class="text-slate-500 text-[13px] font-semibold">No motorist conversations yet</p>
                                        <p class="text-[12px] text-slate-400">Accept a dispatch to start chatting.</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>

            <section id="profilePanel" class="panel">
                <div class="top-header">
                    <div class="top-header-logo"><i class="fas fa-user"></i></div>
                    <div class="top-header-title">
                        <h1>Profile</h1>
                        <p class="top-header-sub">Manage your mechanic account</p>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="content-scroll profile-content">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if (session('pw_success'))
                            <div class="alert alert-success">{{ session('pw_success') }}</div>
                        @endif

                        <div class="prof-hero">
                            <div class="prof-avatar">{{ strtoupper(substr($mechanic->name ?? 'M', 0, 1)) }}</div>
                            <div class="prof-hero-name">{{ $mechanic->name }}</div>
                            <div class="prof-hero-email">{{ $mechanic->email }}</div>
                            <div class="prof-hero-badge"><i class="fas fa-user-cog" style="margin-right:4px"></i>Mechanic Account</div>
                        </div>

                        <div style="padding: 20px 0 0; display:flex; flex-direction:column; gap:20px;">
                            <div>
                                <p class="prof-section-label">Profile summary</p>
                                <div class="prof-group">
                                    <button class="prof-row" type="button" onclick="openMechanicSubPanel('editProfilePanel')">
                                        <div class="prof-row-icon"><i class="fas fa-phone"></i></div>
                                        <div class="prof-row-body">
                                            <div class="prof-row-title">Contact number</div>
                                            <div class="prof-row-sub {{ !$profile->phone ? 'empty' : '' }}">{{ $profile->phone ?? 'Tap to add' }}</div>
                                        </div>
                                        <i class="fa-chevron-right fa-solid prof-row-chevron"></i>
                                    </button>
                                    <button class="prof-row" type="button" onclick="openMechanicSubPanel('editProfilePanel')">
                                        <div class="prof-row-icon"><i class="fas fa-id-badge"></i></div>
                                        <div class="prof-row-body">
                                            <div class="prof-row-title">Plate number</div>
                                            <div class="prof-row-sub {{ !$profile->plate_number ? 'empty' : '' }}">{{ $profile->plate_number ?? 'Tap to add' }}</div>
                                        </div>
                                        <i class="fa-chevron-right fa-solid prof-row-chevron"></i>
                                    </button>
                                    <div class="prof-row no-action">
                                        <div class="prof-row-icon red"><i class="fas fa-check-circle"></i></div>
                                        <div class="prof-row-body">
                                            <div class="prof-row-title">Status</div>
                                            <div class="prof-row-sub">{{ ucfirst(optional($profile)->status ?? 'Active') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <p class="prof-section-label">Account & Security</p>
                                <div class="prof-group">
                                    <button class="prof-row" type="button" onclick="openMechanicSubPanel('changePasswordPanel')">
                                        <div class="prof-row-icon"><i class="fas fa-lock"></i></div>
                                        <div class="prof-row-body">
                                            <div class="prof-row-title">Change password</div>
                                            <div class="prof-row-sub">Update your login password</div>
                                        </div>
                                        <i class="fa-chevron-right fa-solid prof-row-chevron"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Logout --}}
                        <div style="padding: 0 20px 20px;">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="logout-btn">
                                    <i class="fa-right-from-bracket fa-solid"></i> Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

            <section id="editProfilePanel" class="panel sub-panel" style="display:none;">
                <div class="ph">
                    <button class="ph-back" type="button" onclick="closeMechanicSubPanel('editProfilePanel')">
                        <i class="fa-arrow-left fa-solid"></i>
                    </button>
                    <div class="ph-title">Edit profile</div>
                </div>
                <div class="ph-note">Update your phone number and plate number. These values are shared with dispatch and messaging.</div>
                <div style="padding: 12px 14px 20px; overflow-y:auto; display: flex; flex-direction: column; gap: 14px;">
                    <form method="POST" action="{{ route('mechanic.profile.update') }}" class="space-y-4">
                        @csrf
                        <div class="field-group">
                            <label class="field-label" for="phone">PHONE NUMBER</label>
                            <input id="phone" name="phone" type="tel" value="{{ old('phone', $profile->phone ?? '') }}" class="profile-field" placeholder="09171234567">
                            @if ($errors->has('phone'))
                                <p class="form-error">{{ $errors->first('phone') }}</p>
                            @endif
                        </div>
                        <div class="field-group">
                            <label class="field-label" for="plate_number">PLATE NUMBER</label>
                            <input id="plate_number" name="plate_number" type="text" value="{{ old('plate_number', $profile->plate_number ?? '') }}" class="profile-field" placeholder="ABC 1234">
                            @if ($errors->has('plate_number'))
                                <p class="form-error">{{ $errors->first('plate_number') }}</p>
                            @endif
                        </div>
                        <button type="submit" class="button-primary">Save profile</button>
                    </form>
                </div>
            </section>

            <section id="changePasswordPanel" class="panel sub-panel" style="display:none;">
                <div class="ph">
                    <button class="ph-back" type="button" onclick="closeMechanicSubPanel('changePasswordPanel')">
                        <i class="fa-arrow-left fa-solid"></i>
                    </button>
                    <div class="ph-title">Change password</div>
                </div>
                <div class="ph-note">For security, enter your current password first and choose a new password you haven't used before.</div>
                <div style="padding: 12px 14px 20px; overflow-y:auto; display: flex; flex-direction: column; gap: 14px;">
                    <form method="POST" action="{{ route('mechanic.profile.password') }}" class="space-y-4">
                        @csrf
                        <div class="field-group">
                            <label class="field-label" for="current_password">CURRENT PASSWORD</label>
                            <input id="current_password" name="current_password" type="password" class="profile-field" autocomplete="current-password">
                            @if ($errors->has('current_password'))
                                <p class="form-error">{{ $errors->first('current_password') }}</p>
                            @endif
                        </div>
                        <div class="field-group">
                            <label class="field-label" for="password">NEW PASSWORD</label>
                            <input id="password" name="password" type="password" class="profile-field" autocomplete="new-password">
                            @if ($errors->has('password'))
                                <p class="form-error">{{ $errors->first('password') }}</p>
                            @endif
                        </div>
                        <div class="field-group">
                            <label class="field-label" for="password_confirmation">CONFIRM PASSWORD</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="profile-field" autocomplete="new-password">
                        </div>
                        <button type="submit" class="button-primary">Update password</button>
                    </form>
                </div>
            </section>
        </div>

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

        // Store map instances to allow updating without re-creating
        function getJobMaps() {
            window._jobMaps = window._jobMaps || {};
            return window._jobMaps;
        }

        function updateMechanicHash(value) {
            const url = window.location.pathname + window.location.search + '#' + value;
            if (window.location.href !== url) {
                history.replaceState(null, '', url);
            }
        }

        function showMechanicTab(tab) {
            const validTabs = ['jobs', 'messages', 'profile'];
            if (!validTabs.includes(tab)) return;

            validTabs.forEach(name => {
                const panel = document.getElementById(`${name}Panel`);
                if (panel) panel.classList.toggle('open', name === tab);
                const navBtn = document.getElementById(`nav${name.charAt(0).toUpperCase() + name.slice(1)}`);
                if (navBtn) navBtn.classList.toggle('active', name === tab);
            });

            try {
                localStorage.setItem('mf_mechanic_active_tab', tab);
            } catch (_e) {
                // ignore
            }

            updateMechanicHash(tab);
        }

        function switchMechanicMessageTab(tab) {
            const shopPanel = document.getElementById('shop-panel');
            const motoristPanel = document.getElementById('motorist-panel');
            const shopTab = document.getElementById('tab-shop');
            const motoristTab = document.getElementById('tab-motorist');

            if (tab === 'shop') {
                shopPanel.classList.add('open');
                motoristPanel.classList.remove('open');
                shopTab.classList.add('active');
                motoristTab.classList.remove('active');
            } else {
                shopPanel.classList.remove('open');
                motoristPanel.classList.add('open');
                shopTab.classList.remove('active');
                motoristTab.classList.add('active');
            }
        }

        function openMechanicSubPanel(id) {
            const panel = document.getElementById(id);
            if (!panel) return;
            panel.style.display = 'block';
            requestAnimationFrame(() => panel.classList.add('open'));
            updateMechanicHash(id);
        }

        function closeMechanicSubPanel(id) {
            const panel = document.getElementById(id);
            if (!panel) return;
            panel.classList.remove('open');
            setTimeout(() => {
                panel.style.display = 'none';
            }, 320);
            if (window.location.hash.replace('#', '') === id) {
                updateMechanicHash('profile');
            }
        }

        function _drawRoute(entry, mechLat, mechLng, destLat, destLng) {
            const {
                map
            } = entry;
            if (entry.routeLayer) {
                map.removeLayer(entry.routeLayer);
                entry.routeLayer = null;
            }
            if (entry.mechMarker) {
                map.removeLayer(entry.mechMarker);
                entry.mechMarker = null;
            }

            entry.mechMarker = L.marker([mechLat, mechLng], {
                icon: L.divIcon({
                    className: '',
                    html: '<div class="mf-pin mf-pin-mech"><i class="fa-solid fa-user-gear"></i></div>',
                    iconSize: [34, 34],
                    iconAnchor: [17, 17]
                })
            }).addTo(map);

            fetch(
                    `https://router.project-osrm.org/route/v1/driving/${mechLng},${mechLat};${destLng},${destLat}?overview=full&geometries=geojson`
                    )
                .then(r => r.json())
                .then(data => {
                    map.invalidateSize();
                    if (data.code === 'Ok' && data.routes.length) {
                        entry.routeLayer = L.geoJSON(data.routes[0].geometry, {
                            style: {
                                color: '#3B82F6',
                                weight: 5,
                                opacity: .9,
                                lineJoin: 'round',
                                lineCap: 'round'
                            }
                        }).addTo(map);
                        map.fitBounds(entry.routeLayer.getBounds().pad(0.18));
                    } else {
                        map.fitBounds([
                            [mechLat, mechLng],
                            [destLat, destLng]
                        ], {
                            padding: [22, 22]
                        });
                    }
                })
                .catch(() => {
                    map.invalidateSize();
                    map.fitBounds([
                        [mechLat, mechLng],
                        [destLat, destLng]
                    ], {
                        padding: [22, 22]
                    });
                });
        }

        function initJobMaps(gpsMechLat, gpsMechLng) {
            document.querySelectorAll('.job-map').forEach(el => {
                const destLat = parseFloat(el.dataset.lat);
                const destLng = parseFloat(el.dataset.lng);
                if (isNaN(destLat) || isNaN(destLng)) return;

                // Tier 1: live GPS  Tier 2: last stored mechanic pos  Tier 3: shop location
                const storedLat = el.dataset.mechLat !== '' ? parseFloat(el.dataset.mechLat) : null;
                const storedLng = el.dataset.mechLng !== '' ? parseFloat(el.dataset.mechLng) : null;
                const shopLat = el.dataset.shopLat !== '' ? parseFloat(el.dataset.shopLat) : null;
                const shopLng = el.dataset.shopLng !== '' ? parseFloat(el.dataset.shopLng) : null;
                const mechLat = (gpsMechLat !== null) ? gpsMechLat :
                    (storedLat !== null) ? storedLat :
                    shopLat;
                const mechLng = (gpsMechLng !== null) ? gpsMechLng :
                    (storedLng !== null) ? storedLng :
                    shopLng;

                const _jobMaps = getJobMaps();
                // Map already exists — just update route with better position
                if (_jobMaps[el.id]) {
                    if (mechLat !== null && !isNaN(mechLat)) {
                        _drawRoute(_jobMaps[el.id], mechLat, mechLng, destLat, destLng);
                    }
                    return;
                }

                // Create Leaflet map
                const map = L.map(el, {
                    zoomControl: false,
                    attributionControl: false,
                    dragging: false,
                    scrollWheelZoom: false,
                    doubleClickZoom: false,
                    touchZoom: false
                });
                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                    maxZoom: 19,
                    subdomains: 'abcd',
                    crossOrigin: true
                }).addTo(map);
                L.control.attribution({
                    prefix: '© OpenStreetMap',
                    position: 'bottomleft'
                }).addTo(map);

                // Destination pin — orange motorcycle
                L.marker([destLat, destLng], {
                    icon: L.divIcon({
                        className: '',
                        html: '<div class="mf-pin mf-pin-dest"><i class="fa-solid fa-motorcycle"></i></div>',
                        iconSize: [34, 34],
                        iconAnchor: [17, 17]
                    })
                }).addTo(map);

                const entry = {
                    map,
                    mechMarker: null,
                    routeLayer: null
                };
                _jobMaps[el.id] = entry;

                if (mechLat !== null && !isNaN(mechLat)) {
                    _drawRoute(entry, mechLat, mechLng, destLat, destLng);
                } else {
                    map.invalidateSize();
                    map.setView([destLat, destLng], 15);
                }
            });
        }

        // Init immediately with stored DB positions — shows route without waiting for GPS
        initJobMaps(null, null);

        // Upgrade with live GPS (updates markers + re-draws route more accurately)
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                pos => initJobMaps(pos.coords.latitude, pos.coords.longitude),
                () => {
                    /* already rendered from stored positions */
                }, {
                    timeout: 8000,
                    maximumAge: 60000
                }
            );
        }

        /* ── MECHANIC LOCATION TRACKING ── */
        // IDs of jobs that need live location (en_route / arrived)
        const _trackingIds = @json($jobs->filter(fn($j) => in_array($j->dispatchRequest?->status, ['en_route', 'arrived']))->pluck('dispatch_request_id')->values());

        let _watchId = null;
        const _lastSent = {}; // requestId → timestamp ms
        const SEND_INTERVAL = 10000; // 10 s

        function _sendLocation(requestId, lat, lng) {
            const now = Date.now();
            if (_lastSent[requestId] && now - _lastSent[requestId] < SEND_INTERVAL) return;
            _lastSent[requestId] = now;
            fetch('/mechanic/request/' + requestId + '/location', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    lat,
                    lng
                })
            }).catch(() => {
                /* non-critical */
            });
        }

        if (_trackingIds.length > 0 && navigator.geolocation) {
            _watchId = navigator.geolocation.watchPosition(pos => {
                const {
                    latitude: lat,
                    longitude: lng
                } = pos.coords;
                _trackingIds.forEach(id => _sendLocation(id, lat, lng));
            }, () => {}, {
                enableHighAccuracy: true,
                maximumAge: 5000,
                timeout: 15000
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('message-search');
            searchInput?.addEventListener('input', () => {
                const phrase = searchInput.value.toLowerCase();
                document.querySelectorAll('#chat-list-panel .chat-item').forEach(item => {
                    const text = item.textContent.toLowerCase();
                    item.style.display = text.includes(phrase) ? 'block' : 'none';
                });
            });

            const params = new URLSearchParams(window.location.search);
            const requestedTab = params.get('tab');
            const hashValue = window.location.hash.replace('#', '');
            const savedTab = localStorage.getItem('mf_mechanic_active_tab');
            const validTabs = ['jobs', 'messages', 'profile'];
            const validPanels = ['editProfilePanel', 'changePasswordPanel'];
            const activeTab = validTabs.includes(hashValue)
                ? hashValue
                : (requestedTab && validTabs.includes(requestedTab)
                    ? requestedTab
                    : (savedTab && validTabs.includes(savedTab) ? savedTab : 'jobs'));

            showMechanicTab(activeTab);
            if (validPanels.includes(hashValue)) {
                showMechanicTab('profile');
                openMechanicSubPanel(hashValue);
            }
            if (requestedTab && !validTabs.includes(hashValue) && !validPanels.includes(hashValue)) {
                params.delete('tab');
                const search = params.toString();
                const baseUrl = window.location.pathname + (search ? `?${search}` : '');
                history.replaceState(null, '', baseUrl + '#' + activeTab);
            }
            window.addEventListener('hashchange', () => {
                const newHash = window.location.hash.replace('#', '');
                if (validTabs.includes(newHash)) {
                    showMechanicTab(newHash);
                } else if (validPanels.includes(newHash)) {
                    showMechanicTab('profile');
                    openMechanicSubPanel(newHash);
                }
            });

            @if(session('success') || session('pw_success') || $errors->any())
                showMechanicTab('profile');
            @endif

            @if($errors->has('current_password') || $errors->has('password') || $errors->has('password_confirmation'))
                openMechanicSubPanel('changePasswordPanel');
            @elseif($errors->has('phone') || $errors->has('plate_number'))
                openMechanicSubPanel('editProfilePanel');
            @endif
        });
    </script>

@endsection
