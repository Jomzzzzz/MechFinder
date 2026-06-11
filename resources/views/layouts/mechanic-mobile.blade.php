<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="referrer" content="no-referrer">

    <title>MechFinder Mechanic</title>

    <link rel="manifest" href="/manifest-mechanic.json">
    <meta name="theme-color" content="#050505">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="MechFinder">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <link rel="apple-touch-icon" href="/icons/mobile-logo1.png">
    <link rel="icon" type="image/png" href="/icons/mobile-logo1.png">

    @vite(['resources/css/app.css'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <style>
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
            --action: #1E293B;
            --action-2: #2D3F55;
            --action-bg: rgba(30, 41, 59, .06);
            --r1: 6px;
            --r2: 10px;
            --r3: 14px;
            --sh-float: 0 4px 18px rgba(0, 0, 0, .12);
            --sh-card: 0 1px 4px rgba(0, 0, 0, .08);
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            background: #E8ECF0;
            font-family: Inter, system-ui, -apple-system, sans-serif;
            overscroll-behavior: none;
            overflow-x: hidden;
            color: var(--text-1);
            -webkit-font-smoothing: antialiased;
        }

        .mechanic-app {
            min-height: 100vh;
            background:
                radial-gradient(circle at top, rgba(247, 148, 29, 0.12), transparent 28%),
                linear-gradient(180deg, #11141b 0%, #0b0f14 40%, #050505 100%);
            overflow-x: hidden;
        }

        .phone {
            position: relative;
            max-width: 430px;
            margin: 0 auto;
            min-height: 100vh;
        }

        .mechanic-main {
            /* Padding is handled per-page for better control */
        }

        .glass {
            background: linear-gradient(135deg, rgba(255, 255, 255, .08), rgba(255, 255, 255, .03));
            border: 1px solid rgba(255, 255, 255, .09);
            box-shadow: 0 12px 30px rgba(0, 0, 0, .35);
        }

        .brand-btn {
            background: linear-gradient(135deg, #ffbf2f, #f7941d);
            color: #111;
            font-weight: 900;
        }

        .dark-btn {
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(255, 255, 255, .1);
        }

        #bottomNav {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: calc(var(--nav-h) + env(safe-area-inset-bottom));
            padding-bottom: env(safe-area-inset-bottom);
            z-index: 40;
            background: rgba(255, 255, 255, 0.94);
            border-top: 1px solid rgba(226, 232, 240, .95);
            display: flex;
            align-items: stretch;
            box-shadow: 0 -2px 12px rgba(15, 23, 42, 0.08);
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
            line-height: 1.1;
            white-space: nowrap;
        }

        @media (max-width: 430px) {
            .phone {
                padding-left: 12px;
                padding-right: 12px;
            }
        }

        @media (max-width: 400px) {
            .phone {
                padding-left: 8px;
                padding-right: 8px;
            }

            .nav-btn {
                gap: 2px;
                padding: 0 6px;
                min-height: calc(var(--nav-h) - 4px);
            }
        }

        @media (max-width: 380px) {
            .phone {
                padding-left: 10px;
                padding-right: 10px;
            }
        }

        @media (max-width: 320px) {
            .phone {
                padding-left: 8px;
                padding-right: 8px;
            }
        }

        @media (min-width: 768px) {
            .phone {
                width: 100%;
                max-width: 430px;
                padding-top: 20px;
                padding-bottom: calc(var(--nav-h) + 40px);
                padding-left: 20px;
                padding-right: 20px;
                min-height: unset;
            }

            #bottomNav {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                background: rgba(255, 255, 255, 0.98);
                border: 1px solid var(--border);
            }
        }
    </style>
</head>

<body>
    <div class="mechanic-app">
        <main id="mechanicMainContent" class="@yield('main-class', 'px-4 pt-4 pb-8') phone">
            @yield('content')

            @unless(View::hasSection('hide-bottom-nav'))
                <nav id="bottomNav">
                    @if(request()->routeIs('mechanic.dashboard'))
                        <button type="button" id="navJobs" class="nav-btn active" title="Jobs" onclick="showMechanicTab('jobs')">
                            <span class="n-icon"><i class="fas fa-briefcase"></i></span>
                            <span class="n-label">Jobs</span>
                        </button>
                        <button type="button" id="navMessages" class="nav-btn" title="Messages" onclick="showMechanicTab('messages')">
                            <span class="n-icon"><i class="fas fa-comments"></i></span>
                            <span class="n-label">Messages</span>
                        </button>
                        <button type="button" id="navProfile" class="nav-btn" title="Profile" onclick="showMechanicTab('profile')">
                            <span class="n-icon"><i class="fas fa-user"></i></span>
                            <span class="n-label">Profile</span>
                        </button>
                    @else
                        <a href="{{ route('mechanic.dashboard') }}" class="nav-btn {{ request()->routeIs('mechanic.dashboard') ? 'active' : '' }}" title="Jobs">
                            <span class="n-icon"><i class="fas fa-briefcase"></i></span>
                            <span class="n-label">Jobs</span>
                        </a>
                        <a href="{{ route('mechanic.messages') }}" class="nav-btn {{ request()->routeIs('mechanic.messages') ? 'active' : '' }}" title="Messages">
                            <span class="n-icon"><i class="fas fa-comments"></i></span>
                            <span class="n-label">Messages</span>
                        </a>
                        <a href="{{ route('mechanic.profile') }}" class="nav-btn {{ request()->routeIs('mechanic.profile') ? 'active' : '' }}" title="Profile">
                            <span class="n-icon"><i class="fas fa-user"></i></span>
                            <span class="n-label">Profile</span>
                        </a>
                    @endif
                </nav>
            @endunless
        </main>
    </div>

    <div id="installBanner" class="hidden top-0 right-0 left-0 z-50 fixed bg-orange-500 shadow-xl px-4 py-3 text-black">
        <div class="flex justify-between items-center gap-3 mx-auto max-w-md">
            <div>
                <p class="font-black text-sm">Install MechFinder</p>
                <p class="text-xs">Add this app to your phone home screen.</p>
            </div>
            <button id="installBtn" class="bg-black px-4 py-2 rounded-xl font-bold text-white text-sm">Install</button>
            <button id="closeInstall" class="font-black">×</button>
        </div>
    </div>

    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>

    <script>
        window.pusherKey = '{{ config('broadcasting.connections.pusher.key') }}';
        window.pusherCluster = '{{ config('broadcasting.connections.pusher.options.cluster') }}';
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw-mechanic.js', {
                    scope: '/mechanic/'
                });
            });
        }

        let deferredPrompt = null;
        const banner = document.getElementById('installBanner');

        const installed =
            window.matchMedia('(display-mode: standalone)').matches ||
            window.navigator.standalone === true;

        window.addEventListener('beforeinstallprompt', e => {
            e.preventDefault();
            deferredPrompt = e;

            if (!installed && !sessionStorage.getItem('mech_install_closed')) {
                banner.classList.remove('hidden');
            }
        });

        document.getElementById('installBtn')?.addEventListener('click', async () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                await deferredPrompt.userChoice;
                deferredPrompt = null;
                banner.classList.add('hidden');
            } else {
                alert(
                    'Android: tap browser menu then Add to Home screen.\n\niPhone: tap Share then Add to Home Screen.'
                );
            }
        });

        document.getElementById('closeInstall')?.addEventListener('click', () => {
            banner.classList.add('hidden');
            sessionStorage.setItem('mech_install_closed', '1');
        });
    </script>

    <script>
        (function subscribeDispatchEvents(attempts) {
            if (!window.pusherKey) return;
            if (typeof Pusher === 'undefined') {
                if (attempts < 30) return setTimeout(() => subscribeDispatchEvents(attempts + 1), 200);
                return;
            }

            try {
                const pusherClient = new Pusher(window.pusherKey, {
                    cluster: window.pusherCluster,
                    forceTLS: true
                });

                const stages = ['requested', 'accepted', 'en_route', 'arrived', 'completed'];
                const statusMessages = {
                    requested: 'Repair request sent',
                    accepted: 'You accepted the job',
                    en_route: "You're on the way",
                    arrived: 'You have arrived',
                    completed: 'Job completed'
                };

                function updateCard(dispatchId, status) {
                    const card = document.querySelector('[data-dispatch-id="' + dispatchId + '"]');
                    if (!card) return;

                    const indicator = card.querySelector('.status-indicator');
                    if (indicator) {
                        indicator.textContent = (status || '').replace(/_/g, ' ').toUpperCase();
                        // remove old status-* classes
                        Array.from(indicator.classList).forEach(c => {
                            if (c.indexOf('status-') === 0) indicator.classList.remove(c);
                        });
                        indicator.classList.add('status-' + (status || '').replace(/_/g, ''));
                    }

                    const msg = card.querySelector('.status-message');
                    if (msg) {
                        msg.textContent = statusMessages[status] || (status || '').replace(/_/g, ' ');
                    }

                    const timelineItems = card.querySelectorAll('.timeline-item');
                    const idx = stages.indexOf(status);
                    if (timelineItems && timelineItems.length) {
                        timelineItems.forEach((el, i) => {
                            if (i <= idx) el.classList.add('active');
                            else el.classList.remove('active');
                        });
                    }

                    const actions = card.querySelector('.job-actions');
                    if (actions) {
                        if (status === 'completed') {
                            actions.innerHTML =
                                '<div style="padding:8px 10px;border-radius:8px;background:rgba(16,185,129,.08);color:#047857;font-weight:700;">Completed</div>';
                        }
                    }
                }

                // subscribe to each dispatch id present on the page
                const cards = document.querySelectorAll('[data-dispatch-id]');
                cards.forEach(c => {
                    const id = c.getAttribute('data-dispatch-id');
                    if (!id) return;
                    try {
                        const ch = pusherClient.subscribe('dispatch-status.' + id);
                        ch.bind('dispatch.status', function(payload) {
                            try {
                                const status = payload?.status || payload?.status || payload?.state ||
                                    null;
                                updateCard(id, status);
                            } catch (e) {}
                        });
                    } catch (e) {}
                });

            } catch (e) {
                if (attempts < 30) setTimeout(() => subscribeDispatchEvents(attempts + 1), 200);
            }
        })(0);
    </script>

    <script>
        (function () {
            const mainEl = document.getElementById('mechanicMainContent');
            const nav = document.getElementById('bottomNav');

            function isMechanicNavUrl(href) {
                try {
                    const url = new URL(href, location.href);
                    if (url.origin !== location.origin) return false;
                    const path = url.pathname.replace(/\/$/, '');
                    return ['/mechanic', '/mechanic/dashboard', '/mechanic/messages', '/mechanic/profile'].includes(path);
                } catch (error) {
                    return false;
                }
            }

            function runScript(script) {
                const newScript = document.createElement('script');
                if (script.src) {
                    newScript.src = script.src;
                    newScript.async = false;
                } else {
                    newScript.textContent = script.textContent;
                }
                document.body.appendChild(newScript);
                document.body.removeChild(newScript);
            }

            async function loadMechanicPage(url, replaceHistory = false) {
                try {
                    const response = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Unable to load page');
                    }

                    const html = await response.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newMain = doc.querySelector('main');

                    if (!newMain) {
                        throw new Error('Invalid page response');
                    }

                    mainEl.className = newMain.className;
                    const cloned = newMain.cloneNode(true);
                    const scripts = Array.from(cloned.querySelectorAll('script'));
                    scripts.forEach((script) => script.remove());
                    mainEl.innerHTML = cloned.innerHTML;
                    document.title = doc.title || document.title;
                    syncActiveNav(url);
                    if (replaceHistory) {
                        history.replaceState({ ajax: true }, '', url);
                    } else {
                        history.pushState({ ajax: true }, '', url);
                    }
                    window.scrollTo(0, 0);
                    scripts.forEach(runScript);
                } catch (e) {
                    window.location.href = url;
                }
            }

            function syncActiveNav(href) {
                if (!nav) return;
                const path = new URL(href, location.href).pathname.replace(/\/$/, '');
                nav.querySelectorAll('a').forEach((link) => {
                    const linkPath = new URL(link.href, location.href).pathname.replace(/\/$/, '');
                    if (path === '/mechanic' && linkPath === '/mechanic/dashboard') {
                        link.classList.add('active');
                    } else {
                        link.classList.toggle('active', linkPath === path);
                    }
                });
            }

            nav?.addEventListener('click', (event) => {
                const anchor = event.target.closest('a');
                if (!anchor) return;
                const href = anchor.href;
                if (isMechanicNavUrl(href)) {
                    event.preventDefault();
                    if (href === location.href) return;
                    loadMechanicPage(href);
                }
            });

            window.addEventListener('popstate', () => {
                if (location.pathname.startsWith('/mechanic')) {
                    loadMechanicPage(location.href, true);
                }
            });

            if (!history.state) {
                history.replaceState({ ajax: true }, '', location.href);
            }
        })();
    </script>
</body>

</html>
