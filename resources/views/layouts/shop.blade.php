<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MechFinder Shop</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        window.shopId = {{ Auth::check() && Auth::user()->shop_id ? (int) Auth::user()->shop_id : 'null' }};
        window.STATUS_MAP = @json($shopStatusConfigs ?? []);
    </script>

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f4f6fb;
            color: #1d273b;
            margin: 0;
        }

        /* ── Sidebar ─────────────────── */
        .t-sidebar {
            background: #182433;
            width: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
        }

        .t-nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            border-radius: 6px;
            color: #adb9c7;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all .15s;
            margin: 1px 0;
            position: relative;
        }

        .t-nav-item:hover {
            background: rgba(255, 255, 255, .06);
            color: #fff;
        }

        .t-nav-item.active {
            background: rgba(32, 107, 196, .18);
            color: #fff;
        }

        .t-nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: #206bc4;
            border-radius: 0 3px 3px 0;
        }

        .t-nav-item i {
            width: 16px;
            text-align: center;
            font-size: 14px;
            opacity: .8;
        }

        /* ── Card ────────────────────── */
        .t-card {
            background: #fff;
            border: 1px solid #e6e7eb;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .07);
        }

        /* ── Buttons ─────────────────── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all .15s;
            text-decoration: none;
            line-height: 1.4;
        }

        .btn-primary {
            background: #206bc4;
            color: #fff;
            border-color: #206bc4;
        }

        .btn-primary:hover {
            background: #1a5ba8;
            border-color: #1a5ba8;
            color: #fff;
        }

        .btn-danger {
            background: #d63939;
            color: #fff;
            border-color: #d63939;
        }

        .btn-danger:hover {
            background: #b82e2e;
            border-color: #b82e2e;
        }

        .btn-success {
            background: #2fb344;
            color: #fff;
            border-color: #2fb344;
        }

        .btn-success:hover {
            background: #26953a;
            border-color: #26953a;
        }

        .btn-warning {
            background: #f76707;
            color: #fff;
            border-color: #f76707;
        }

        .btn-warning:hover {
            background: #d45b05;
            border-color: #d45b05;
        }

        .btn-outline-primary {
            background: transparent;
            color: #206bc4;
            border-color: #206bc4;
        }

        .btn-outline-primary:hover {
            background: #206bc4;
            color: #fff;
        }

        .btn-secondary {
            background: #fff;
            color: #667382;
            border-color: #c8ccd0;
        }

        .btn-secondary:hover {
            background: #f4f6fb;
        }

        .btn-sm {
            padding: 4px 10px;
            font-size: 12px;
        }

        .btn-lg {
            padding: 10px 20px;
            font-size: 15px;
        }

        /* ── Badges ──────────────────── */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-success {
            background: #d1f7d6;
            color: #1e6b28;
        }

        .badge-warning {
            background: #fff3cd;
            color: #664d03;
        }

        .badge-danger {
            background: #fde8e8;
            color: #b82e2e;
        }

        .badge-info {
            background: #daeeff;
            color: #0c5aa6;
        }

        .badge-secondary {
            background: #f0f0f0;
            color: #667382;
        }

        .badge-primary {
            background: #d1e4f6;
            color: #0c5aa6;
        }

        .badge-purple {
            background: #ede9fe;
            color: #5b21b6;
        }

        .badge-orange {
            background: #ffedd5;
            color: #9a3412;
        }

        /* ── Form ────────────────────── */
        .form-control {
            width: 100%;
            background: #fff;
            border: 1px solid #c8ccd0;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 14px;
            color: #1d273b;
            outline: none;
            transition: border-color .15s;
        }

        .form-control:focus {
            border-color: #206bc4;
            box-shadow: 0 0 0 3px rgba(32, 107, 196, .15);
        }

        select.form-control {
            cursor: pointer;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #1d273b;
            margin-bottom: 5px;
        }

        /* ── Page header ─────────────── */
        .page-header {
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 20px;
            font-weight: 700;
            color: #1d273b;
            margin: 0 0 4px;
        }

        .page-pretitle {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #667382;
            margin: 0 0 4px;
        }

        /* ── Alerts ──────────────────── */
        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
            border: 1px solid;
            font-size: 14px;
        }

        .alert-success {
            background: #d1f7d6;
            border-color: #a3e6b3;
            color: #1e6b28;
        }

        .alert-danger {
            background: #fde8e8;
            border-color: #f5c0c0;
            color: #b82e2e;
        }

        /* ── Toast ───────────────────── */
        #dash-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 8px;
            pointer-events: none;
            max-width: 320px;
        }

        .toast-item {
            background: #fff;
            border: 1px solid #e6e7eb;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 13px;
            color: #1d273b;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .12);
            animation: tIn .2s ease;
            pointer-events: all;
        }

        .toast-item.success {
            border-left: 3px solid #2fb344;
        }

        .toast-item.error {
            border-left: 3px solid #d63939;
        }

        .toast-item.info {
            border-left: 3px solid #206bc4;
        }

        .toast-item.warn {
            border-left: 3px solid #f76707;
        }

        .toast-item.new {
            border-left: 3px solid #206bc4;
        }

        @keyframes tIn {
            from {
                transform: translateX(12px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <div style="display:flex; min-height:100vh;">
        @include('components.sidebar')
        <div style="flex:1; display:flex; flex-direction:column; min-width:0; overflow:hidden;">

            {{-- ── Topbar ── --}}
            <header
                style="background:#fff; border-bottom:1px solid #e6e7eb; padding:0 24px; height:56px; display:flex; align-items:center; justify-content:flex-end; gap:10px; flex-shrink:0; position:sticky; top:0; z-index:200;">

                {{-- Shop Status Pill — styled entirely by updateStatusUI() on load --}}
                <div id="topbar-status-pill" data-status="{{ $shopStatusId ?? 4 }}"
                    style="display:flex; align-items:center; gap:6px; padding:4px 10px 4px 8px; border-radius:20px;">
                    <span id="topbar-status-dot"
                        style="width:8px; height:8px; border-radius:50%; flex-shrink:0;"></span>
                    <span id="topbar-status-text" style="font-size:12px; font-weight:700;"></span>
                </div>
                <button onclick="toggleShopStatus()"
                    style="font-size:12px; font-weight:600; color:#667382; background:#f4f6fb; border:1px solid #e6e7eb; border-radius:6px; padding:5px 12px; cursor:pointer; transition:all .15s; white-space:nowrap;"
                    onmouseover="this.style.background='#e9ecf2'; this.style.color='#1d273b'"
                    onmouseout="this.style.background='#f4f6fb'; this.style.color='#667382'">
                    <i class="fas fa-power-off" style="font-size:11px; margin-right:4px;"></i>Toggle Status
                </button>

                <div style="width:1px; height:24px; background:#e6e7eb;"></div>
                <div style="position:relative;">
                    <button id="notif-btn" onclick="toggleNotif(event)"
                        style="width:36px; height:36px; border-radius:8px; background:#f4f6fb; border:1px solid #e6e7eb; cursor:pointer; display:flex; align-items:center; justify-content:center; position:relative; transition:background .15s;"
                        onmouseover="this.style.background='#e9ecf2'" onmouseout="this.style.background='#f4f6fb'">
                        <i class="fas fa-bell" style="font-size:15px; color:#667382;"></i>
                        <span id="notif-badge"
                            style="display:none; position:absolute; top:-5px; right:-5px; background:#d63939; color:#fff; font-size:10px; font-weight:700; min-width:17px; height:17px; border-radius:10px; padding:0 4px; align-items:center; justify-content:center; border:2px solid #fff; line-height:1;"></span>
                    </button>

                    {{-- Dropdown --}}
                    <div id="notif-dropdown"
                        style="display:none; position:absolute; right:0; top:44px; width:320px; background:#fff; border:1px solid #e6e7eb; border-radius:10px; box-shadow:0 8px 28px rgba(0,0,0,.13); z-index:9998; overflow:hidden;">
                        <div
                            style="padding:12px 16px; border-bottom:1px solid #e6e7eb; display:flex; align-items:center; justify-content:space-between;">
                            <p style="font-size:13px; font-weight:700; color:#1d273b; margin:0;">Notifications</p>
                            <a href="/shop/requests?status=requested"
                                style="font-size:12px; color:#206bc4; text-decoration:none; font-weight:600;">View
                                all</a>
                        </div>
                        <div id="notif-list" style="max-height:320px; overflow-y:auto;"></div>
                        <div id="notif-empty" style="padding:28px 16px; text-align:center; display:none;">
                            <i class="fas fa-check-circle"
                                style="font-size:22px; color:#2fb344; display:block; margin-bottom:8px;"></i>
                            <p style="font-size:13px; color:#667382; margin:0;">All caught up!</p>
                        </div>
                    </div>
                </div>

                {{-- User avatar --}}
                <div
                    style="display:flex; align-items:center; gap:8px; padding:4px 8px; border-radius:8px; cursor:default;">
                    <div
                        style="width:30px; height:30px; background:#206bc4; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <span
                            style="font-size:12px; font-weight:700; color:#fff;">{{ strtoupper(substr(Auth::user()->name ?? 'S', 0, 1)) }}</span>
                    </div>
                    <span
                        style="font-size:13px; font-weight:600; color:#1d273b; display:none; display:inline;">{{ Auth::user()->name ?? 'Shop' }}</span>
                </div>
            </header>

            <main style="flex:1; padding:28px; overflow-y:auto;">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Notification JS --}}
    <script>
        (function() {
            var isOpen = false;

            window.toggleNotif = function(e) {
                e.stopPropagation();
                isOpen = !isOpen;
                document.getElementById('notif-dropdown').style.display = isOpen ? 'block' : 'none';
                if (isOpen) fetchNotifs();
            };

            document.addEventListener('click', function(e) {
                if (isOpen && !document.getElementById('notif-btn').contains(e.target) && !document
                    .getElementById('notif-dropdown').contains(e.target)) {
                    isOpen = false;
                    document.getElementById('notif-dropdown').style.display = 'none';
                }
            });

            function setCount(n) {
                var b = document.getElementById('notif-badge');
                if (n > 0) {
                    b.style.display = 'flex';
                    b.textContent = n > 99 ? '99+' : n;
                } else {
                    b.style.display = 'none';
                }
            }

            function fetchNotifs() {
                fetch('/shop/dashboard-map-data')
                    .then(function(r) {
                        return r.json();
                    })
                    .then(function(d) {
                        var reqs = (d.requests || []).filter(function(r) {
                            return r.status === 'requested';
                        });
                        renderNotifs(reqs);
                    }).catch(function() {});
            }

            function renderNotifs(reqs) {
                var list = document.getElementById('notif-list');
                var empty = document.getElementById('notif-empty');
                list.innerHTML = '';
                if (reqs.length === 0) {
                    empty.style.display = 'block';
                    return;
                }
                empty.style.display = 'none';
                reqs.slice(0, 8).forEach(function(r) {
                    var name = r.motorist_name || r.guest_name || 'Unknown Motorist';
                    var issue = r.issue_type || 'Motorcycle Issue';
                    var el = document.createElement('a');
                    el.href = '/shop/requests?status=requested';
                    el.style.cssText =
                        'display:flex; align-items:flex-start; gap:10px; padding:10px 16px; border-bottom:1px solid #f0f2f5; text-decoration:none; transition:background .1s;';
                    el.onmouseover = function() {
                        this.style.background = '#f9fafb';
                    };
                    el.onmouseout = function() {
                        this.style.background = '';
                    };
                    el.innerHTML =
                        '<div style="width:32px; height:32px; background:#fde8e8; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:2px;">' +
                        '<i class="fas fa-motorcycle" style="font-size:13px; color:#d63939;"></i></div>' +
                        '<div style="min-width:0;">' +
                        '<p style="font-size:13px; font-weight:600; color:#1d273b; margin:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">' +
                        escN(issue) + '</p>' +
                        '<p style="font-size:12px; color:#667382; margin:2px 0 0;">' + escN(name) +
                        ' &bull; <span style="color:#d63939;">Pending</span></p>' +
                        '</div>';
                    list.appendChild(el);
                });
            }

            function escN(s) {
                return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            }

            function pollCount() {
                fetch('/shop/dashboard-map-data')
                    .then(function(r) {
                        return r.json();
                    })
                    .then(function(d) {
                        var n = (d.requests || []).filter(function(r) {
                            return r.status === 'requested';
                        }).length;
                        setCount(n);
                    }).catch(function() {});
            }

            if (window.shopId) {
                pollCount();
                setInterval(pollCount, 30000);
            }
        })();
    </script>

    {{-- ── Shared Confirm Modal ── --}}
    <div id="confirm-modal"
        style="display:none; position:fixed; inset:0; z-index:99999; background:rgba(0,0,0,.45); align-items:center; justify-content:center; padding:16px;">
        <div
            style="width:100%; max-width:380px; background:#fff; border-radius:12px; box-shadow:0 16px 48px rgba(0,0,0,.18); overflow:hidden;">
            <div style="padding:22px 24px 0;">
                <p id="confirm-title" style="font-size:16px; font-weight:700; color:#1d273b; margin:0 0 8px;"></p>
                <p id="confirm-message" style="font-size:14px; color:#667382; margin:0; line-height:1.5;"></p>
            </div>
            <div style="display:flex; gap:8px; padding:20px 24px; justify-content:flex-end;">
                <button id="confirm-cancel"
                    style="padding:8px 18px; border-radius:6px; border:1px solid #e6e7eb; background:#fff; color:#667382; font-size:13px; font-weight:600; cursor:pointer; transition:all .15s;"
                    onmouseover="this.style.background='#f4f6fb'" onmouseout="this.style.background='#fff'">
                    Cancel
                </button>
                <button id="confirm-ok"
                    style="padding:8px 18px; border-radius:6px; border:none; background:#206bc4; color:#fff; font-size:13px; font-weight:600; cursor:pointer; transition:background .15s;"
                    onmouseover="this.style.background='#1a5aab'" onmouseout="this.style.background='#206bc4'">
                    Confirm
                </button>
            </div>
        </div>
    </div>
    <script>
        window.showConfirmModal = function(title, message, onConfirm, okLabel, okColor) {
            var modal = document.getElementById('confirm-modal');
            document.getElementById('confirm-title').textContent = title;
            document.getElementById('confirm-message').textContent = message;
            var okBtn = document.getElementById('confirm-ok');
            okBtn.textContent = okLabel || 'Confirm';
            okBtn.style.background = okColor || '#206bc4';
            okBtn.onmouseover = function() {
                this.style.background = okColor ? shadeColor(okColor, -15) : '#1a5aab';
            };
            okBtn.onmouseout = function() {
                this.style.background = okColor || '#206bc4';
            };
            modal.style.display = 'flex';

            function close() {
                modal.style.display = 'none';
            }
            document.getElementById('confirm-cancel').onclick = close;
            okBtn.onclick = function() {
                close();
                onConfirm();
            };
            modal.onclick = function(e) {
                if (e.target === modal) close();
            };
        };

        function shadeColor(hex, pct) {
            var n = parseInt(hex.slice(1), 16),
                t = pct < 0 ? 0 : 255,
                p = pct < 0 ? -pct : pct;
            return '#' + [16, 8, 0].map(function(s) {
                return Math.round((n >> s & 0xff) + (t - (n >> s & 0xff)) * p / 100).toString(16).padStart(2, '0');
            }).join('');
        }
    </script>

    @yield('body_after')
</body>

</html>
