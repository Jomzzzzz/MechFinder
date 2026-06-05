@extends('layouts.motorist')

@section('content')
    <div class="min-h-screen flex flex-col text-white">

        <div class="sticky top-0 z-20 bg-[#0d1118]/95 backdrop-blur border-b border-white/5 px-4 pt-4 pb-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('motorist.shop-detail', ['id' => $shop->id, 'lat' => $userLat, 'lng' => $userLng]) }}"
                    class="w-9 h-9 rounded-full bg-white/10 border border-white/10 flex items-center justify-center text-sm shrink-0">←</a>
                <div class="text-[14px] font-extrabold tracking-wide">REQUEST MECHANIC</div>
            </div>
        </div>

        <div class="px-4 pt-4 pb-6 space-y-4 flex-1 overflow-y-auto">

            <!-- Your Location Section -->
            <div>
                <p class="text-[13px] font-bold text-gray-300 mb-3 tracking-wide">YOUR LOCATION</p>
                <div id="gps-box"
                    class="glass-card rounded-[16px] px-4 py-3 text-[13px] text-gray-300 flex items-center gap-2">
                    📍 <span>Detecting GPS location...</span>
                </div>
            </div>

            <!-- Issue Type Section -->
            <div>
                <p class="text-[13px] font-bold text-gray-300 mb-3 tracking-wide">ISSUE TYPE</p>
                <div class="space-y-2">
                    <button type="button"
                        class="issue-btn w-full text-left glass-card rounded-[16px] px-4 py-3 flex items-center gap-3 transition-all"
                        data-issue="Flat Tire / Blowout">
                        <span
                            class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center text-[18px]">🛞</span>
                        <span class="text-[13px] font-medium">Flat Tire / Blowout</span>
                    </button>

                    <button type="button"
                        class="issue-btn w-full text-left rounded-[16px] px-4 py-3 flex items-center gap-3 border border-orange-500 bg-orange-500/15 transition-all"
                        data-issue="Engine Stall">
                        <span
                            class="w-10 h-10 rounded-lg bg-orange-500/20 flex items-center justify-center text-[18px]">⚙️</span>
                        <span class="text-[13px] font-semibold text-orange-300">Engine Stall</span>
                        <span class="text-orange-300 ml-auto text-[12px]">✓</span>
                    </button>

                    <button type="button"
                        class="issue-btn w-full text-left glass-card rounded-[16px] px-4 py-3 flex items-center gap-3 transition-all"
                        data-issue="Oil / Fluid Leak">
                        <span
                            class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center text-[18px]">🛢️</span>
                        <span class="text-[13px] font-medium">Oil / Fluid Leak</span>
                    </button>

                    <button type="button"
                        class="issue-btn w-full text-left glass-card rounded-[16px] px-4 py-3 flex items-center gap-3 transition-all"
                        data-issue="Battery / Electrical">
                        <span
                            class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center text-[18px]">🔋</span>
                        <span class="text-[13px] font-medium">Battery / Electrical</span>
                    </button>
                </div>
            </div>

            <!-- Your Information Section -->
            <div>
                <p class="text-[13px] font-bold text-gray-300 mb-3 tracking-wide">YOUR INFORMATION</p>
                <div class="space-y-2">
                    <div>
                        <label class="block text-[11px] text-gray-500 mb-2 font-semibold">OWNER/MOTORIST NAME *</label>
                        <input type="text" id="owner_name"
                            class="w-full glass-card rounded-[16px] px-4 py-3 text-[13px] text-white placeholder:text-gray-600 outline-none focus:border-orange-500"
                            placeholder="e.g., Juan Dela Cruz" />
                    </div>

                    <div>
                        <label class="block text-[11px] text-gray-500 mb-2 font-semibold">CONTACT NUMBER (SMS/WHATSAPP)
                            *</label>
                        <input type="tel" id="contact_number"
                            class="w-full glass-card rounded-[16px] px-4 py-3 text-[13px] text-white placeholder:text-gray-600 outline-none focus:border-orange-500"
                            placeholder="e.g., 09123456789" />
                    </div>
                </div>
            </div>

            <!-- Vehicle Information Section -->
            <div>
                <p class="text-[13px] font-bold text-gray-300 mb-3 tracking-wide">VEHICLE INFORMATION</p>
                <div class="space-y-2">
                    <div>
                        <label class="block text-[11px] text-gray-500 mb-2 font-semibold">MAKE/MODEL *</label>
                        <input type="text" id="vehicle_make_model"
                            class="w-full glass-card rounded-[16px] px-4 py-3 text-[13px] text-white placeholder:text-gray-600 outline-none focus:border-orange-500"
                            placeholder="e.g., Honda Wave 110" />
                    </div>

                    <div>
                        <label class="block text-[11px] text-gray-500 mb-2 font-semibold">VARIANT/COLOR</label>
                        <input type="text" id="vehicle_variant_color"
                            class="w-full glass-card rounded-[16px] px-4 py-3 text-[13px] text-white placeholder:text-gray-600 outline-none focus:border-orange-500"
                            placeholder="e.g., Red v3, Black 2020" />
                    </div>

                    <div>
                        <label class="block text-[11px] text-gray-500 mb-2 font-semibold">LICENSE PLATE / TEMP
                            NUMBER</label>
                        <input type="text" id="plate_temp_number"
                            class="w-full glass-card rounded-[16px] px-4 py-3 text-[13px] text-white placeholder:text-gray-600 outline-none focus:border-orange-500"
                            placeholder="e.g., ABC-1234, TEMP-0001" />
                    </div>
                </div>
            </div>

            <!-- Additional Notes Section -->
            <div>
                <p class="text-[13px] font-bold text-gray-300 mb-3 tracking-wide">ADDITIONAL NOTES</p>
                <textarea id="description" rows="3"
                    class="w-full glass-card rounded-[16px] px-4 py-3 text-[13px] text-white placeholder:text-gray-600 outline-none resize-none focus:border-orange-500"
                    placeholder="Describe your situation... (optional)"></textarea>
            </div>

        </div>

        <!-- Fixed Bottom Section -->
        <div class="px-4 pb-5 pt-3 border-t border-white/5 bg-[#090909]/95 sticky bottom-0">
            <button id="submit-dispatch" type="button"
                class="w-full rounded-[16px] px-5 py-4 danger-btn text-[14px] font-extrabold tracking-wide transition-all hover:shadow-lg">
                🚨 SEND DISPATCH REQUEST
            </button>
            <p class="text-center text-[12px] text-gray-600 mt-3">
                Nearest available mechanic will be notified
            </p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const shopId = {{ $shop->id }};
            let userLat = {{ $userLat }};
            let userLng = {{ $userLng }};
            let selectedIssue = 'Engine Stall';

            const issueButtons = document.querySelectorAll('.issue-btn');
            const submitBtn = document.getElementById('submit-dispatch');
            const descriptionInput = document.getElementById('description');
            const gpsBox = document.getElementById('gps-box');

            // Pre-fill form from DB-backed profile cache (populated by layout)
            function prefillFromProfile() {
                const p = window._mfProfile || {};
                if (p.owner_name) document.getElementById('owner_name').value = p.owner_name;
                if (p.contact_number) document.getElementById('contact_number').value = p.contact_number;
                if (p.vehicle_make_model) document.getElementById('vehicle_make_model').value = p
                .vehicle_make_model;
                if (p.vehicle_variant_color) document.getElementById('vehicle_variant_color').value = p
                    .vehicle_variant_color;
                if (p.plate_temp_number) document.getElementById('plate_temp_number').value = p.plate_temp_number;
            }
            if (window._mfProfile) {
                prefillFromProfile();
            } else {
                window.addEventListener('mfProfileLoaded', prefillFromProfile, {
                    once: true
                });
            }

            issueButtons.forEach(button => {
                button.addEventListener('click', function() {
                    issueButtons.forEach(btn => {
                        btn.classList.remove('border-orange-500', 'bg-orange-500/10');
                        btn.classList.add('glass-card');
                    });

                    this.classList.remove('glass-card');
                    this.classList.add('border-orange-500', 'bg-orange-500/10');

                    selectedIssue = this.dataset.issue;
                });
            });

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition((position) => {
                    userLat = position.coords.latitude;
                    userLng = position.coords.longitude;
                    gpsBox.textContent = `📍 ${userLat.toFixed(6)}, ${userLng.toFixed(6)}`;
                }, () => {
                    gpsBox.textContent = '📍 Unable to detect exact GPS location.';
                });
            }

            submitBtn.addEventListener('click', function() {
                const guest = getGuestIdentity();
                const ownerName = document.getElementById('owner_name').value;
                const contactNumber = document.getElementById('contact_number').value;
                const vehicleMakeModel = document.getElementById('vehicle_make_model').value;
                const vehicleVariantColor = document.getElementById('vehicle_variant_color').value;
                const plateTempNumber = document.getElementById('plate_temp_number').value;

                // Validate required fields
                if (!ownerName.trim() || !contactNumber.trim() || !vehicleMakeModel.trim()) {
                    alert('Please fill in Owner Name, Contact Number, and Vehicle Make/Model.');
                    return;
                }

                // Save motorist info to DB (storeDispatch already handles this)
                // Pre-fill is loaded from DB via window._mfProfile on page load

                fetch('/api/dispatch-requests', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            shop_id: shopId,
                            issue_type: selectedIssue,
                            description: descriptionInput.value,
                            location: `${userLat}, ${userLng}`,
                            distance: 0,
                            latitude: userLat,
                            longitude: userLng,
                            guest_token: guest.guestToken,
                            guest_name: guest.guestName,
                            owner_name: ownerName,
                            contact_number: contactNumber,
                            vehicle_make_model: vehicleMakeModel,
                            vehicle_variant_color: vehicleVariantColor,
                            plate_temp_number: plateTempNumber
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert('Dispatch request sent successfully.');
                            window.location.href = '/motorist/requests';
                        } else {
                            alert(data.message || 'Failed to send dispatch request.');
                        }
                    })
                    .catch(() => {
                        alert('Something went wrong while sending the request.');
                    });
            });
        });

        function getGuestIdentity() {
            const p = window._mfProfile || {};
            let token = p.guest_token || localStorage.getItem('mf_guest_token');
            if (!token) {
                token = 'MF-' + Date.now() + '-' + Math.random().toString(36).substring(2, 8).toUpperCase();
                localStorage.setItem('mf_guest_token', token);
            }
            return {
                guestToken: token,
                guestName: p.owner_name || 'Guest Motorist',
            };
        }
    </script>
@endsection
