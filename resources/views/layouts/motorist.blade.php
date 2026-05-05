<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>MechFinder Motorist</title>

    <link rel="manifest" href="/manifest-motorist.json">
    <meta name="theme-color" content="#050505">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="MechFinder">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <link rel="apple-touch-icon" href="/icons/mobile-logo1.png">
    <link rel="icon" type="image/png" href="/icons/mobile-logo1.png">

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">

    <style>
        html, body {
            margin: 0;
            min-height: 100%;
            background: #050505;
            color: white;
            font-family: Inter, system-ui, sans-serif;
        }

        .motorist-app {
            min-height: 100vh;
            background:
                radial-gradient(circle at top, rgba(247,148,29,0.12), transparent 28%),
                linear-gradient(180deg, #11141b 0%, #0b0f14 40%, #050505 100%);
        }

        .phone {
            max-width: 430px;
            margin: 0 auto;
            min-height: 100vh;
        }

        .glass {
            background: linear-gradient(135deg, rgba(255,255,255,.08), rgba(255,255,255,.03));
            border: 1px solid rgba(255,255,255,.09);
            box-shadow: 0 12px 30px rgba(0,0,0,.35);
        }

        .brand-btn {
            background: linear-gradient(135deg, #ffbf2f, #f7941d);
            color: #111;
            font-weight: 900;
        }

        .dark-btn {
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.1);
        }

        #map {
            height: 330px;
            border-radius: 22px;
            overflow: hidden;
            background: #111;
        }

        .leaflet-popup-content-wrapper,
        .leaflet-popup-tip {
            background: #111;
            color: #fff;
        }
    </style>
</head>
<body>
<div class="motorist-app">
    <main class="phone px-4 pt-4 pb-8">
        @yield('content')
    </main>
</div>

<div id="installBanner" class="hidden fixed top-0 left-0 right-0 z-50 bg-orange-500 text-black px-4 py-3 shadow-xl">
    <div class="max-w-md mx-auto flex justify-between items-center gap-3">
        <div>
            <p class="font-black text-sm">Install MechFinder</p>
            <p class="text-xs">Add this app to your phone home screen.</p>
        </div>
        <button id="installBtn" class="bg-black text-white rounded-xl px-4 py-2 text-sm font-bold">Install</button>
        <button id="closeInstall" class="font-black">×</button>
    </div>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
    window.csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    function mfIdentity() {
        let token = localStorage.getItem('mf_guest_token');

        if (!token) {
            token = 'mf_' + Math.random().toString(36).slice(2, 12);
            localStorage.setItem('mf_guest_token', token);
        }

        return {
            guest_token: token,
            owner_name: localStorage.getItem('mf_owner_name') || '',
            contact_number: localStorage.getItem('mf_contact_number') || '',
            vehicle_make_model: localStorage.getItem('mf_vehicle_make_model') || '',
            vehicle_variant_color: localStorage.getItem('mf_vehicle_variant_color') || '',
            plate_temp_number: localStorage.getItem('mf_plate_temp_number') || '',
        };
    }

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw-motorist.js', { scope: '/motorist/' });
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
            alert('Android: tap browser menu then Add to Home screen.\n\niPhone: tap Share then Add to Home Screen.');
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