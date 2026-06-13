    <script>
        /* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
                                                                                                                                                                                                                                                                                       MECHFINDER â€” APP LOGIC
                                                                                                                                                                                                                                                                                       â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */

        /* Plain headline text for the active bar */
        const STATUS_TITLE = {
            requested: 'Finding nearest mechanicâ€¦',
            accepted: 'Shop accepted your request',
            en_route: 'Mechanic is on the way',
            arrived: 'Mechanic has arrived!',
            completed: 'All done â€” job complete!',
        };
        /* Sub-message below the step dots */
        const SUB_MSG = {
            requested: 'Looking for an available mechanicâ€¦',
            accepted: 'Your mechanic is getting ready to head out',
            en_route: 'Sit tight â€” your mechanic is on the way to you',
            arrived: 'Your mechanic is on-site and working on your vehicle',
        };

        const STATUS_LABEL = {
            requested: '<i class="fa-solid fa-hourglass-half"></i> Finding nearest shopâ€¦',
            accepted: '<i class="fa-solid fa-circle-check"></i> Shop accepted your request',
            en_route: '<i class="fa-solid fa-motorcycle"></i> Mechanic is on the way',
            arrived: '<i class="fa-solid fa-location-dot"></i> Mechanic has arrived â€” job complete!',
            completed: '<i class="fa-solid fa-circle-check"></i> All done!',
            declined: '<i class="fa-solid fa-circle-xmark"></i> No shops available',
            cancelled: '<i class="fa-solid fa-ban"></i> Request cancelled',
        };
        const STATUS_TEXT = {
            requested: 'Finding nearest shop',
            accepted: 'Shop accepted your request',
            en_route: 'Mechanic is on the way',
            arrived: 'Mechanic has arrived â€” job complete',
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

        /* â”€â”€ STATE â”€â”€ */
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
        let currentDispatchShopId = null;
        let currentMotoristId = null;
        let reviewContext = {
            requestId: null,
            shopId: null,
            motoristId: null,
        };

        /* â”€â”€ REVIEW STATE â”€â”€ */
        let currentReviewRating = 0;
        let selectedServiceTags = new Set();
        const currentRequestStored = LS.get('mf_current_request_id');
        if (currentRequestStored) {
            try {
                const parsed = JSON.parse(currentRequestStored);
                currentRequestId = parsed?.id ?? currentRequestStored;
            } catch {
                currentRequestId = currentRequestStored;
            }
        }
        let pusherClient = null;
        let shopStatusClient = null;
        let _statusPollTimer = null;
        let _lastKnownStatus = null;
        let allShops = [];

        /* â”€â”€ BOOT â”€â”€ */
        let _loaderMapReady = false;
        let _loaderProfileReady = false;

        function _checkLoaderDone() {
            if (_loaderMapReady && _loaderProfileReady) {
                const loader = document.getElementById('mfLoader');
                if (loader) loader.classList.add('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Measure the rescue bar BEFORE initMap so Leaflet gets the correct
            // #map height on first render and doesn't leave a grey strip.
            const _barPre = document.getElementById('rescueBar');
            if (_barPre) {
                const h = Math.ceil(_barPre.getBoundingClientRect().height);
                // +10 = the 10px gap between bar bottom edge and mapArea bottom (bar is bottom:10px inside #mapArea)
                if (h > 0) document.documentElement.style.setProperty('--bar-h', (h + 10) + 'px');
            }
            initMap();
            // Give the browser one frame to apply the CSS variable before Leaflet
            // finalises tile positions, then force a size recalculation.
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    if (map) map.invalidateSize();
                });
            });
            locateUser();
            subscribeToShopStatus();
            if (currentRequestId) resumeActiveRequest(currentRequestId);
            const validTabs = ['map', 'requests', 'profile'];
            const validPanels = ['rescuePanel', 'shopsPanel', 'requestsPanel', 'profilePanel'];
            const hashState = getMotoristHashState();

            // Only restore from hash (deep link) — never from storage.
            // Storage-based restore causes panels to re-open after login redirect.
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
                }
            }
            // Default: map tab — always on fresh load / login
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
                    }
                }
            });
            // Refresh profile UI once DB profile has loaded
            window.addEventListener('mfProfileLoaded', () => {
                refreshIdentityCard();
                renderProfileSummary();
                _loaderProfileReady = true;
                _checkLoaderDone();
            });
            // If already loaded before DOMContentLoaded, refresh immediately
            if (window._mfProfile) {
                refreshIdentityCard();
                renderProfileSummary();
                _loaderProfileReady = true;
                _checkLoaderDone();
            }
            // Keep --bar-h in sync with the bar's actual rendered height
            const _bar = document.getElementById('rescueBar');
            if (_bar && window.ResizeObserver) {
                new ResizeObserver(() => {
                    // +10 = the 10px gap between bar bottom edge and mapArea bottom
                    const bh = Math.ceil(_bar.getBoundingClientRect().height);
                    if (bh > 0) document.documentElement.style.setProperty('--bar-h', (bh + 10) + 'px');
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

        /* â”€â”€ MAP â”€â”€ */
        function initMap() {
            map = L.map('map', {
                    zoomControl: false,
                    attributionControl: false
                })
                .setView([userLat, userLng], 14);
            const tileLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                maxZoom: 19,
                subdomains: 'abcd',
                crossOrigin: true,
                updateWhenIdle: true,
                updateWhenZooming: false,
                keepBuffer: 3
            }).addTo(map);
            // Dismiss loader once first batch of map tiles has rendered
            tileLayer.once('load', () => {
                _loaderMapReady = true;
                _checkLoaderDone();
            });
            // Safety fallback â€” dismiss loader after 4s regardless
            setTimeout(() => {
                _loaderMapReady = true;
                _loaderProfileReady = true;
                _checkLoaderDone();
            }, 4000);
            L.control.attribution({
                prefix: 'Â© OpenStreetMap',
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

        /* â”€â”€ SHOPS (pins only) â”€â”€ */
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
          ${open?'â— Open':'â— Closed'}
        </span>
        ${open?`<span style="font-size:11px;color:#6B7280;margin-left:6px;"><i class="fa-solid fa-star" style="color:#F59E0B;font-size:10px;"></i> ${stars}</span>`:''}
      </div>
    `);
                shopMarkers.push(m);
            });
        }

        /* â”€â”€ SHOP LIST + SEARCH â”€â”€ */
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

        /* â”€â”€ ROUTE PLOTTING (OSRM) â”€â”€ */
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
                // Fit map to show full route â€” defer so bar has finished expanding first
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

        /* â”€â”€ LIVE MECHANIC MARKER â”€â”€ */
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
                // reuse existing route layer â€” just shift the start; re-fetch route
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

        /* â”€â”€ RESCUE FORM â”€â”€ */
        function openPanel(id) {
            if (id === 'rescuePanel') refreshIdentityCard();
            if (id === 'shopsPanel') {
                renderShopList(allShops, '');
                loadShops(); // always fetch fresh status when panel opens
            }
            const el = document.getElementById(id);
            el.style.display = 'block';
            requestAnimationFrame(() => el.classList.add('open'));
            updateMotoristHash(null, id);
        }

        function closePanel(id) {
            const el = document.getElementById(id);
            el.classList.remove('open');
            setTimeout(() => {
                el.style.display = 'none';
            }, 340);
            if (id === 'rescuePanel' || id === 'shopsPanel') {
                updateMotoristHash('map');
            } else if (id === 'requestsPanel') {
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
                `<div style="font-size:11px;color:var(--text-2);line-height:1.2;margin-top:1px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${id.vehicle_make_model}${id.vehicle_variant_color?' Â· '+id.vehicle_variant_color:''}</div>`;
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

        /* â”€â”€ DISPATCH SUBMISSION â”€â”€ */
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
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sendingâ€¦';
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
                    LS.set('mf_current_request_id', JSON.stringify({
                        id: data.request_id
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

        /* â”€â”€ REAL-TIME SHOP STATUS â”€â”€ */
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

        /* â”€â”€ STATUS POLLING FALLBACK â”€â”€ */
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
                                if (!document.getElementById('reviewModal') || document.getElementById(
                                        'reviewModal').style.display === 'none') {
                                    LS.del('mf_current_request_id');
                                    currentRequestId = null;
                                    currentDispatchShopId = null;
                                    currentMotoristId = null;
                                    reviewContext = {
                                        requestId: null,
                                        shopId: null,
                                        motoristId: null
                                    };
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

        /* â”€â”€ REAL-TIME STATUS â”€â”€ */
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
                    console.log('[Pusher] connected â€” subscribing to dispatch-status.' + requestId);
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
                        console.log(
                            '[Pusher dispatch.status] final status detected, fetching request data for requestId:',
                            requestId);
                        fetch(`/motorist/request/${requestId}`)
                            .then(r => {
                                console.log('[Pusher dispatch.status] fetch response status:', r.status);
                                return r.json();
                            })
                            .then(d => {
                                console.log('[Pusher dispatch.status] request data received:', d);
                                console.log('[Pusher dispatch.status] shop_id from response:', d?.shop_id);
                                console.log('[Pusher dispatch.status] motorist_id from response:', d
                                    ?.motorist_id);

                                if (status === 'completed') {
                                    currentDispatchShopId = d?.shop_id || null;
                                    currentMotoristId = d?.motorist_id || currentUserId || null;
                                    reviewContext = {
                                        requestId: requestId,
                                        shopId: currentDispatchShopId,
                                        motoristId: currentMotoristId,
                                    };
                                    console.log('[Pusher dispatch.status] set currentDispatchShopId:',
                                        currentDispatchShopId);
                                    console.log('[Pusher dispatch.status] set currentMotoristId:',
                                        currentMotoristId);
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
                            if (!document.getElementById('reviewModal') || document.getElementById(
                                    'reviewModal').style.display === 'none') {
                                LS.del('mf_current_request_id');
                                currentRequestId = null;
                                currentDispatchShopId = null;
                                currentMotoristId = null;
                                reviewContext = {
                                    requestId: null,
                                    shopId: null,
                                    motoristId: null
                                };
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
            // Polling fallback â€” always runs regardless of Pusher
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
                document.documentElement.style.setProperty('--bar-h', '140px');
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
                document.documentElement.style.setProperty('--bar-h', '88px');
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
            document.documentElement.style.setProperty('--bar-h', '106px');
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
                    alert(data.error ?? 'Could not cancel â€” request may have already been accepted.');
                }
            } catch {
                alert('Network error. Please try again.');
            }
        }

        /* â”€â”€ NAVIGATION â”€â”€ */
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
            // panels that use slide animation
            const panels = ['requestsPanel', 'profilePanel', 'rescuePanel', 'shopsPanel'];
            const currentlyOpen = panels.find(id => document.getElementById(id) && document.getElementById(id).classList
                .contains('open'));

            function openTarget() {
                if (tab === 'map') {
                    // nothing to open; keep map visible
                    setTimeout(() => {
                        try {
                            map.invalidateSize();
                        } catch {}
                    }, 50);
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
                if (tabTransitionTimer) {
                    clearTimeout(tabTransitionTimer);
                    tabTransitionTimer = null;
                }
                openTarget();
                if (tab === 'map') setTimeout(() => map.invalidateSize(), window.MF_TAB_TRANS_MS + 50);
                return;
            }

            // If the target panel is already open, do nothing
            if ((tab === 'requests' && currentlyOpen === 'requestsPanel') || (tab === 'profile' && currentlyOpen ===
                    'profilePanel')) {
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

        /* â”€â”€ PROFILE â”€â”€ */
        function renderProfileSummary() {
            const id = mfIdentity();
            // Motorcycle
            const motoSub = document.getElementById('profMotoSub');
            if (id.vehicle_make_model) {
                const parts = [id.vehicle_make_model, id.vehicle_variant_color, id.plate_temp_number].filter(Boolean);
                motoSub.textContent = parts.join(' Â· ');
                motoSub.classList.remove('empty');
            } else {
                motoSub.textContent = 'Tap to add â€” make, model, plate';
                motoSub.classList.add('empty');
            }
            // Contact
            const contactSub = document.getElementById('profContactSub');
            if (id.owner_name || id.contact_number) {
                const parts = [id.owner_name, id.contact_number].filter(Boolean);
                contactSub.textContent = parts.join(' Â· ');
                contactSub.classList.remove('empty');
            } else {
                contactSub.textContent = 'Tap to add â€” name & phone number';
                contactSub.classList.add('empty');
            }
        }

        function openSubPanel(id) {
            // Load current values into sub-panel inputs before showing
            const identity = mfIdentity();
            const locked = window._mfProfile && window._mfProfile.profile_locked;
            const changeRequested = window._mfProfile && window._mfProfile.profile_change_requested;

            if (id === 'editMotoPanel') {
                document.getElementById('pMakeModel').value = identity.vehicle_make_model;
                document.getElementById('pColor').value = identity.vehicle_variant_color;
                document.getElementById('pPlate').value = identity.plate_temp_number;
            } else if (id === 'editContactPanel') {
                document.getElementById('pName').value = identity.owner_name;
                document.getElementById('pContact').value = identity.contact_number;
            }

            // Apply locked state to the panel
            const panel = document.getElementById(id);
            const inputs = panel.querySelectorAll('input, textarea');
            const saveBtn = panel.querySelector('.save-panel-btn');
            let lockBanner = panel.querySelector('.prof-lock-banner');

            if (locked) {
                inputs.forEach(el => {
                    el.disabled = true;
                    el.style.opacity = '0.55';
                });
                if (saveBtn) saveBtn.style.display = 'none';
                if (!lockBanner) {
                    lockBanner = document.createElement('div');
                    lockBanner.className = 'prof-lock-banner';
                    lockBanner.style.cssText =
                        'background:rgba(59,130,246,.08);border:1px solid rgba(59,130,246,.25);border-radius:var(--r2);padding:11px 13px;font-size:12px;color:#3b82f6;display:flex;gap:8px;align-items:flex-start;margin:0 14px 4px;';
                    const icon = changeRequested ?
                        '<i class="fa-solid fa-clock" style="margin-top:1px;flex-shrink:0;"></i>' :
                        '<i class="fa-solid fa-lock" style="margin-top:1px;flex-shrink:0;"></i>';
                    const msg = changeRequested ?
                        'Your change request has been sent. Admin will review and unlock your profile.' :
                        'This profile is locked. <button onclick="_openProfileChangeReq()" style="background:none;border:none;color:#3b82f6;font-weight:700;font-size:12px;cursor:pointer;padding:0;text-decoration:underline;">Request a change</button>';
                    lockBanner.innerHTML = icon + '<span>' + msg + '</span>';
                    const bodyEl = panel.querySelector('.ph');
                    if (bodyEl) bodyEl.insertAdjacentElement('afterend', lockBanner);
                }
            } else {
                inputs.forEach(el => {
                    el.disabled = false;
                    el.style.opacity = '';
                });
                if (saveBtn) saveBtn.style.display = '';
                if (lockBanner) lockBanner.remove();
            }

            panel.style.display = 'block';
            requestAnimationFrame(() => panel.classList.add('open'));
        }

        function closeSubPanel(id) {
            const el = document.getElementById(id);
            el.classList.remove('open');
            setTimeout(() => {
                el.style.display = 'none';
            }, 320);
            renderProfileSummary();
        }

        /* â”€â”€ PROFILE SAVE CONFIRM MODAL â”€â”€ */
        let _profSavePending = null;

        function _profSaveOpen(title, body, onConfirm) {
            document.getElementById('profSaveModalTitle').textContent = title;
            document.getElementById('profSaveModalBody').textContent = body;
            _profSavePending = onConfirm;
            const modal = document.getElementById('profileSaveModal');
            modal.style.pointerEvents = 'all';
            modal.style.opacity = '1';
            modal.querySelector('.cm-sheet').style.transform = 'translateY(0)';
        }

        function _profSaveClose() {
            _profSavePending = null;
            const modal = document.getElementById('profileSaveModal');
            modal.querySelector('.cm-sheet').style.transform = 'translateY(100%)';
            modal.style.opacity = '0';
            setTimeout(() => {
                modal.style.pointerEvents = 'none';
            }, 250);
        }

        document.getElementById('profSaveConfirmBtn').addEventListener('click', async () => {
            if (!_profSavePending) return;
            const fn = _profSavePending;
            _profSaveClose();
            await fn();
        });

        /* â”€â”€ PROFILE CHANGE REQUEST MODAL â”€â”€ */
        function _openProfileChangeReq() {
            document.getElementById('profChangeReason').value = '';
            document.getElementById('profChangeReasonErr').style.display = 'none';
            const modal = document.getElementById('profileChangeReqModal');
            modal.style.pointerEvents = 'all';
            modal.style.opacity = '1';
            modal.querySelector('.cm-sheet').style.transform = 'translateY(0)';
        }

        function _profChangeClose() {
            const modal = document.getElementById('profileChangeReqModal');
            modal.querySelector('.cm-sheet').style.transform = 'translateY(100%)';
            modal.style.opacity = '0';
            setTimeout(() => {
                modal.style.pointerEvents = 'none';
            }, 250);
        }

        document.getElementById('profChangeSubmitBtn').addEventListener('click', async () => {
            const reason = document.getElementById('profChangeReason').value.trim();
            if (!reason) {
                document.getElementById('profChangeReasonErr').style.display = 'block';
                return;
            }
            document.getElementById('profChangeReasonErr').style.display = 'none';
            const token = _mfGetToken();
            const body = token ? {
                guest_token: token,
                reason
            } : {
                reason
            };
            try {
                const res = await fetch('/api/motorist/profile-change-request', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.csrfToken
                    },
                    body: JSON.stringify(body),
                });
                const data = await res.json();
                if (data.success) {
                    _profChangeClose();
                    await mfLoadProfile();
                    // Re-render any open lock banner
                    ['editMotoPanel', 'editContactPanel'].forEach(pid => {
                        const banner = document.getElementById(pid)?.querySelector('.prof-lock-banner');
                        if (banner) banner.innerHTML =
                            '<i class="fa-solid fa-clock" style="margin-top:1px;flex-shrink:0;"></i><span>Your change request has been sent. Admin will review and unlock your profile.</span>';
                    });
                    showToast('Change request sent. Admin will review it shortly.', 'success');
                } else {
                    showToast(data.error || 'Failed to send request.', 'error');
                }
            } catch {
                showToast('Something went wrong. Please try again.', 'error');
            }
        });

        function saveMoto() {
            const makeModel = document.getElementById('pMakeModel').value.trim();
            if (!makeModel) {
                showToast('Make & Model is required.', 'error');
                return;
            }
            _profSaveOpen(
                'Save Motorcycle Info?',
                'This info will be shared with the mechanic when you request rescue.',
                async () => {
                    const patch = {
                        vehicle_make_model: makeModel,
                        vehicle_variant_color: document.getElementById('pColor').value.trim(),
                        plate_temp_number: document.getElementById('pPlate').value.trim(),
                    };
                    await mfSaveProfile(patch);
                    closeSubPanel('editMotoPanel');
                    showToast('Motorcycle info saved.', 'success');
                }
            );
        }

        function saveContact() {
            const ownerName = document.getElementById('pName').value.trim();
            const contactNumber = document.getElementById('pContact').value.trim();
            if (!ownerName || !contactNumber) {
                showToast('Name and phone number are required.', 'error');
                return;
            }
            _profSaveOpen(
                'Save Contact Info?',
                'Your name and contact number will be shared with mechanics during rescue.',
                async () => {
                    const patch = {
                        owner_name: ownerName,
                        contact_number: contactNumber
                    };
                    await mfSaveProfile(patch);
                    closeSubPanel('editContactPanel');
                    showToast('Contact info saved.', 'success');
                }
            );
        }

        /* â”€â”€ REQUEST HISTORY â”€â”€ */
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
            const identity = typeof mfIdentity === 'function' ? mfIdentity() : {
                guest_token: null
            };
            const token = identity.guest_token || getRequestTokenEntry(entry.id) || null;
            if (token) {
                saveRequestToken(entry.id, token);
            }
            const h = JSON.parse(LS.get('mf_request_history') ?? '[]');
            const next = [{
                    ...entry,
                    guest_token: token,
                    time: new Date().toISOString()
                },
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
                    return {
                        ...entry,
                        guest_token: guestToken
                    };
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
                '<div style="padding:12px 0;color:var(--text-3);font-size:12px;text-align:center;">Loadingâ€¦</div>';
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
                return buildRequestCard(entry.id, shopName, issueType, status, entry.time, entry
                    .guest_token);
            }));
            list.innerHTML = cards.join('');
        }

        function buildRequestCard(id, shopName, issueType, status, timeStr, guestToken = '') {
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
                `<span> Â· <i class="fa-solid fa-store" style="font-size:9px;"></i> ${shopName}</span>` :
                '<span style="color:var(--text-3);"> Â· Finding shopâ€¦</span>';

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
      <button onclick="window.location.href='/motorist/chat/${id}'" style="flex:1;padding:8px 12px;background-color:#F7941D;color:black;border:none;border-radius:8px;text-align:center;font-size:12px;font-weight:600;transition:background-color 0.2s;cursor:pointer;" onmouseover="this.style.backgroundColor='#ff9e2a'" onmouseout="this.style.backgroundColor='#F7941D'">Message</button>
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

        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
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
            return date ? date.toLocaleString([], {
                hour: 'numeric',
                minute: '2-digit'
            }) : value || '';
        }

        /* â”€â”€ REQUEST DETAILS MODAL â”€â”€ */
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
                document.getElementById('detailStatus').textContent = (STATUS_TEXT[data.status] || data.status ||
                    'Unknown');
                const detailTime = parseTimestamp(data.created_at);
                document.getElementById('detailTime').textContent = detailTime ? detailTime.toLocaleString() :
                    'Unknown';
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
            window.location.href = '/motorist/chat/' + encodeURIComponent(currentRequestId);
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
                document.getElementById('detailStatus').textContent = (STATUS_TEXT[data.status] || data.status ||
                    'Unknown');
                const detailTime = parseTimestamp(data.created_at);
                document.getElementById('detailTime').textContent = detailTime ? detailTime.toLocaleString() :
                    'Unknown';
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

        /* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
           REVIEW SYSTEM - COMPLETELY REWRITTEN
           â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */

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

            window.reviewData = {
                rating: 0,
                tags: new Set(),
                comment: ''
            };
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

            overlay.addEventListener('click', window.hideReviewModal, {
                once: true
            });
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
                        'X-CSRF-TOKEN': window.csrfToken || document.querySelector(
                            'meta[name="csrf-token"]')?.getAttribute('content') || ''
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
