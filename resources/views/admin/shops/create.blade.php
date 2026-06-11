@extends('layouts.admin')

@section('content')
<div class="mb-8">
            <div>
                <div class="text-[#F4B942] text-2xl heading-font">Create New Shop</div>
                <div class="text-[#AAA] text-sm mt-1">Enter shop details and location for new shops.</div>
            </div>
    </div>

    @if ($errors->any())
        <div class="bg-red-900 mb-4 p-4 rounded text-red-200 text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.shops.store') }}" method="POST">
        @csrf
        <div class="bg-[#111113] border border-[#1E1E21] rounded-xl p-6 space-y-6">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm text-[#AAA] mb-2" for="shop_name">Shop Name</label>
                    <input id="shop_name" name="shop_name" type="text" value="{{ old('shop_name') }}"
                        class="w-full rounded-lg border border-[#1E1E21] bg-[#0F1115] px-4 py-3 text-[#EEE]">
                </div>
                <div>
                    <label class="block text-sm text-[#AAA] mb-2" for="address">Address</label>
                    <div class="flex gap-2">
                        <input id="address" name="address" type="text" value="{{ old('address') }}"
                            class="flex-1 rounded-lg border border-[#1E1E21] bg-[#0F1115] px-4 py-3 text-[#EEE]">
                        <button id="geocode-address" type="button"
                            class="rounded-lg border border-[#1E1E21] bg-slate-800 px-4 py-3 text-[#EEE] hover:bg-slate-700">
                            Find Coordinates
                        </button>
                    </div>
                    <p id="formatted-address" class="mt-2 text-xs text-[#888]"></p>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm text-[#AAA] mb-2" for="latitude">Latitude</label>
                    <input id="latitude" name="latitude" type="text" value="{{ old('latitude') }}"
                        class="w-full rounded-lg border border-[#1E1E21] bg-[#0F1115] px-4 py-3 text-[#EEE]">
                </div>
                <div>
                    <label class="block text-sm text-[#AAA] mb-2" for="longitude">Longitude</label>
                    <input id="longitude" name="longitude" type="text" value="{{ old('longitude') }}"
                        class="w-full rounded-lg border border-[#1E1E21] bg-[#0F1115] px-4 py-3 text-[#EEE]">
                </div>
            </div>

            <div class="text-sm text-[#AAA] space-y-2">
                <p><strong>Tip:</strong> If geocoding doesn't find your address, copy coordinates directly from Google Maps:</p>
                <ol class="list-decimal list-inside ml-4 space-y-1">
                    <li>Open Google Maps and find your location.</li>
                    <li>Right-click on the location pin and select "What's here?".</li>
                    <li>Copy the coordinates shown (for example, 14.8386, 120.2842).</li>
                    <li>Paste them into the latitude and longitude fields above.</li>
                </ol>
            </div>

            <div class="mt-4">
                <a id="google-maps-link" href="https://www.google.com/maps" target="_blank" rel="noopener noreferrer"
                    class="inline-flex items-center justify-center rounded-lg border border-[#1E1E21] bg-[#111113] px-5 py-3 text-sm font-semibold text-[#EEE] hover:bg-[#1E1E21] hover:text-white">
                    go to google maps
                </a>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm text-[#AAA] mb-2" for="phone">Phone</label>
                    <input id="phone" name="phone" type="text" value="{{ old('phone') }}"
                        class="w-full rounded-lg border border-[#1E1E21] bg-[#0F1115] px-4 py-3 text-[#EEE]">
                </div>
                <div>
                    <label class="block text-sm text-[#AAA] mb-2" for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}"
                        class="w-full rounded-lg border border-[#1E1E21] bg-[#0F1115] px-4 py-3 text-[#EEE]">
                </div>
            </div>

            <div>
                <label class="block text-sm text-[#AAA] mb-2" for="status_id">Status</label>
                <select id="status_id" name="status_id"
                    class="w-full rounded-lg border border-[#1E1E21] bg-[#0F1115] px-4 py-3 text-[#EEE]">
                    @foreach ($shopStatuses as $status)
                        <option value="{{ $status->id }}" {{ old('status_id') == $status->id ? 'selected' : '' }}>
                            {{ $status->label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="text-right">
                <button type="submit"
                    class="bg-[#F4B942] text-[#0A0A0B] px-5 py-3 rounded-lg font-semibold hover:bg-[#e6b12a]">Create Shop</button>
            </div>
        </div>
        </div>
    </form>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const addressInput = document.getElementById('address');
                const geocodeButton = document.getElementById('geocode-address');
                const formattedAddress = document.getElementById('formatted-address');
                const latInput = document.getElementById('latitude');
                const lngInput = document.getElementById('longitude');
                const googleMapsLink = document.getElementById('google-maps-link');

                function updateGoogleMapsLink() {
                    const lat = parseFloat(latInput.value);
                    const lng = parseFloat(lngInput.value);
                    
                    if (!isNaN(lat) && !isNaN(lng)) {
                        googleMapsLink.href = `https://www.google.com/maps?q=${lat},${lng}`;
                        googleMapsLink.style.display = 'inline-block';
                    } else {
                        googleMapsLink.style.display = 'none';
                    }
                }

                async function geocodeAddress() {
                    const address = addressInput.value.trim();
                    if (!address) {
                        formattedAddress.textContent = 'Please enter an address to geocode.';
                        return;
                    }

                    geocodeButton.disabled = true;
                    geocodeButton.textContent = 'Locating...';
                    formattedAddress.textContent = '';

                    try {
                        const response = await fetch(`{{ route('admin.geocode') }}?address=${encodeURIComponent(address)}`);
                        const data = await response.json();

                        if (!response.ok) {
                            formattedAddress.textContent = data.error || 'Could not geocode address. Try copying coordinates from Google Maps instead.';
                            return;
                        }

                        latInput.value = data.lat.toFixed(7);
                        lngInput.value = data.lng.toFixed(7);
                        formattedAddress.textContent = `Found: ${data.formatted_address}`;
                        updateGoogleMapsLink();
                    } catch (error) {
                        formattedAddress.textContent = 'Geocoding request failed. Try copying coordinates from Google Maps instead.';
                    } finally {
                        geocodeButton.disabled = false;
                        geocodeButton.textContent = 'Find Coordinates';
                    }
                }

                function parseAndApplyCoordinates() {
                    const input = coordinatesInput.value.trim();
                    if (!input) {
                        alert('Please paste coordinates in the format: 14.8386, 120.2842');
                        return;
                    }

                    // Support multiple formats: "lat, lng" or "(lat, lng)" or "lat,lng"
                    const match = input.match(/([+-]?\d+\.\d+)[,\s]+([+-]?\d+\.\d+)/);
                    if (!match) {
                        alert('Invalid format. Please use: 14.8386, 120.2842');
                        return;
                    }

                    const lat = parseFloat(match[1]);
                    const lng = parseFloat(match[2]);

                    if (lat < -90 || lat > 90) {
                        alert('Latitude must be between -90 and 90.');
                        return;
                    }

                    if (lng < -180 || lng > 180) {
                        alert('Longitude must be between -180 and 180.');
                        return;
                    }

                    latInput.value = lat.toFixed(7);
                    lngInput.value = lng.toFixed(7);
                    coordinatesInput.value = '';
                    updateGoogleMapsLink();
                    alert('Coordinates applied successfully!');
                }

                geocodeButton.addEventListener('click', geocodeAddress);
                pasteCoordinatesButton.addEventListener('click', parseAndApplyCoordinates);
                coordinatesInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') parseAndApplyCoordinates();
                });
                latInput.addEventListener('change', updateGoogleMapsLink);
                lngInput.addEventListener('change', updateGoogleMapsLink);
                
                // Initialize link on page load if coordinates exist
                updateGoogleMapsLink();
            });
        </script>
    @endpush
@endsection
