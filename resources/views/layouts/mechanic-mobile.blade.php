<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

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

        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            background: #E8ECF0;
            font-family: Inter, system-ui, -apple-system, sans-serif;
            overscroll-behavior: none;
            color: var(--text-1);
            -webkit-font-smoothing: antialiased;
        }

        .mechanic-app {
            min-height: 100svh;
            min-height: 100dvh;
            background:
                radial-gradient(circle at top, rgba(247, 148, 29, 0.12), transparent 28%),
                linear-gradient(180deg, #11141b 0%, #0b0f14 40%, #050505 100%);
        }

        .phone {
            max-width: 430px;
            margin: 0 auto;
            min-height: 100svh;
            min-height: 100dvh;
            padding-top: env(safe-area-inset-top);
            padding-bottom: env(safe-area-inset-bottom);
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
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: calc(var(--nav-h) + env(safe-area-inset-bottom));
            padding-bottom: env(safe-area-inset-bottom);
            z-index: 40;
            background: rgba(255, 255, 255, 0.96);
            border-top: 1px solid var(--border);
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
            transition: color .15s;
            -webkit-tap-highlight-color: transparent;
            text-decoration: none;
            padding: 0 6px;
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

        @media (min-width: 768px) {
            #bottomNav {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="mechanic-app">
        <main class="@yield('main-class', 'px-4 pt-4 pb-8') phone bg-white flex-1 flex flex-col">
            @yield('content')
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
</body>

</html>
