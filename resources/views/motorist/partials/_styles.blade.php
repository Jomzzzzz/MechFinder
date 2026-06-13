    <style>
        /* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
                                                                                                                                                                                                                                                                           MECHFINDER â€” PROFESSIONAL LIGHT THEME
                                                                                                                                                                                                                                                                           â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */
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
            /* action color â€” buttons (not orange) */
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
            flex: 1 1 auto;
            min-height: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: #E8ECF0;
            font-family: Inter, system-ui, -apple-system, sans-serif;
            color: var(--text-1);
            -webkit-font-smoothing: antialiased;
        }

        /* â”€â”€ MAP AREA (everything above the nav) â”€â”€ */
        #mapArea {
            flex: 1;
            position: relative;
            min-height: 0;
            overflow: hidden;
        }

        /* â”€â”€ MAP â”€â”€ */
        #map {
            position: absolute;
            inset: 0;
            bottom: var(--bar-h);
            z-index: 1;
        }

        /* â”€â”€ TOP BAR â”€â”€ */
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

        /* â”€â”€ STEP TRACKER (Grab-style) â”€â”€ */
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

        /* â”€â”€ LOCATE FAB â”€â”€ */
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

        /* â”€â”€ RESCUE BAR â”€â”€ */
        #rescueBar {
            position: absolute;
            left: 12px;
            right: 12px;
            bottom: 10px;
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

        /* â”€â”€ RESCUE FAB (floating circle on map) â”€â”€ */
        #rescueFab {
            position: absolute;
            right: 12px;
            bottom: calc(var(--bar-h) + 68px);
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

        /* â”€â”€ SHOP LIST (inside shops panel) â”€â”€ */
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

        /* â”€â”€ PANELS â”€â”€ */
        .panel {
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            z-index: 30;
            background: var(--surface-2);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 16px;
            transform: translateY(100%);
            transition: transform .33s cubic-bezier(.4, 0, .2, 1);
        }

        .panel.open {
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

        .ph-subtitle {
            font-size: 11px;
            color: var(--text-2);
            margin-top: 1px;
        }

        /* â”€â”€ ISSUE TILES â”€â”€ */
        /* â”€â”€ RESCUE FORM â”€â”€ */
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

        /* â”€â”€ INPUTS â”€â”€ */
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

        /* â”€â”€ BUTTONS â”€â”€ */
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

        /* â”€â”€ SEARCH OVERLAY â”€â”€ */
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



        /* â”€â”€ PROFILE â”€â”€ */
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

        /* â”€â”€ TOAST â”€â”€ */
        #mfToast {
            position: absolute;
            bottom: 14px;
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

        /* â”€â”€ BOTTOM NAV â”€â”€ */
        #bottomNav {
            flex: 0 0 var(--nav-h);
            height: var(--nav-h);
            min-height: 60px;
            width: 100%;
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

        /* â”€â”€ REQUEST CARD â”€â”€ */
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

        /* â”€â”€ MAP PINS â”€â”€ */
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

        /* â”€â”€ CONFIRM MODAL â”€â”€ */
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

        /* â”€â”€ COMPLETION MODAL â”€â”€ */
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

        /* Smooth swap for the request list */
        #requestsList {
            transition: opacity .28s ease, transform .28s ease;
            will-change: opacity, transform;
        }

        /* â”€â”€ SPLASH LOADER â”€â”€ */
        #mfLoader {
            position: absolute;
            inset: 0;
            z-index: 9999;
            background: var(--surface);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 16px;
            transition: opacity .35s ease, visibility .35s ease;
        }

        #mfLoader.hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .mf-loader-logo {
            width: 56px;
            height: 56px;
            background: var(--brand);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: #fff;
            box-shadow: 0 8px 24px rgba(247, 148, 29, .35);
        }

        .mf-loader-label {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-1);
            letter-spacing: .01em;
        }

        .mf-loader-sub {
            font-size: 12px;
            color: var(--text-2);
            margin-top: -10px;
        }

        .mf-loader-spinner {
            width: 28px;
            height: 28px;
            border: 3px solid var(--border);
            border-top-color: var(--brand);
            border-radius: 50%;
            animation: mf-spin .7s linear infinite;
        }

        @keyframes mf-spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
