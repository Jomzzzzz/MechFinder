<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="referrer" content="no-referrer">

    <title>MechFinder Motorist</title>

    <link rel="manifest" href="/manifest-motorist.json">
    <meta name="theme-color" content="#050505">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="MechFinder">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <link rel="apple-touch-icon" href="/icons/mobile-logo1.png">
    <link rel="icon" type="image/png" href="/icons/mobile-logo1.png">

    @vite(['resources/css/app.css'])

    @stack('styles')

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer">

    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            overscroll-behavior: none;
        }

        /* Fixed to viewport — immune to Tailwind resets, browser chrome, and height chain issues */
        .motorist-app {
            position: fixed;
            inset: 0;
            overflow: hidden;
            background: #E8ECF0;
            display: flex;
            flex-direction: column;
        }

        .phone {
            position: relative;
            max-width: 430px;
            width: 100%;
            flex: 1 1 auto;
            min-height: 0;
            overflow: hidden;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
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

        /* Map sizing is controlled per-view */
    </style>
</head>

<body>
    <div class="motorist-app">
        <main class="phone" style="padding:0;margin-top:0;">
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

    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.js" crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>

    <script>
        window.pusherKey = '{{ config('broadcasting.connections.pusher.key') }}';
        window.pusherCluster = '{{ config('broadcasting.connections.pusher.options.cluster') }}';
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Profile is always fetched fresh from DB — nothing is cached in localStorage.
        window._mfProfile = null;
        window._mfAuthUserId = {{ Auth::check() && Auth::user()->role === 'motorist' ? Auth::id() : 'null' }};

        function _mfGetToken() {
            // Auth users are identified server-side via session — no token needed.
            if (window._mfAuthUserId) return null;
            let token = localStorage.getItem('mf_guest_token');
            if (!token) {
                token = 'mf_' + Math.random().toString(36).slice(2, 12);
                localStorage.setItem('mf_guest_token', token);
            }
            return token;
        }

        async function mfLoadProfile() {
            try {
                const token = _mfGetToken();
                const url = token ?
                    '/api/motorist/guest-profile?guest_token=' + encodeURIComponent(token) :
                    '/api/motorist/guest-profile';
                const res = await fetch(url);
                const data = await res.json();
                window._mfProfile = data.profile || {};
                if (token && !window._mfProfile.guest_token) {
                    window._mfProfile.guest_token = token;
                }
            } catch {
                const token = _mfGetToken();
                window._mfProfile = token ? {
                    guest_token: token
                } : {};
            }
            window.dispatchEvent(new Event('mfProfileLoaded'));
        }

        async function mfSaveProfile(patch) {
            const token = _mfGetToken();
            const body = token ? Object.assign({
                guest_token: token
            }, patch) : patch;
            await fetch('/api/motorist/guest-profile', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken
                },
                body: JSON.stringify(body),
            });
            // Always re-fetch from DB after save — never trust local state
            await mfLoadProfile();
        }

        function mfIdentity() {
            const p = window._mfProfile || {};
            const token = p.guest_token || _mfGetToken();
            return {
                guest_token: token,
                owner_name: p.owner_name || '',
                contact_number: p.contact_number || '',
                vehicle_make_model: p.vehicle_make_model || '',
                vehicle_variant_color: p.vehicle_variant_color || '',
                plate_temp_number: p.plate_temp_number || '',
            };
        }

        // Alias used by shops.blade.php (camelCase keys)
        function getGuestIdentity() {
            const p = window._mfProfile || {};
            const token = p.guest_token || _mfGetToken();
            return {
                guestToken: token,
                guestName: p.owner_name || 'Guest Motorist',
                ownerName: p.owner_name || '',
                contactNumber: p.contact_number || '',
                vehicleMakeModel: p.vehicle_make_model || '',
                vehicleVariantColor: p.vehicle_variant_color || '',
                plateTempNumber: p.plate_temp_number || '',
            };
        }

        // Kick off profile load immediately
        mfLoadProfile();

        // Notify Leaflet when the visible area changes (keyboard open/close on Android)
        // so the map tiles re-render to fill the correct dimensions.
        if (window.visualViewport) {
            window.visualViewport.addEventListener('resize', () => {
                if (typeof map !== 'undefined' && map) map.invalidateSize();
            });
        }

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw-motorist.js', {
                    scope: '/motorist/'
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

            if (!installed && !sessionStorage.getItem('mf_install_closed')) {
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
            sessionStorage.setItem('mf_install_closed', '1');
        });
    </script>

    @yield('scripts')
</body>

</html>
