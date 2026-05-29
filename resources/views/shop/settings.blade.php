@extends('layouts.shop')

@section('content')

    <div class="mb-8">
        <h2 class="heading-font text-3xl mb-2">Settings</h2>
        <p class="text-gray-400">Manage your shop settings and preferences</p>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 rounded-lg border border-green-500/30 bg-green-500/10 text-green-400">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-4 rounded-lg border border-red-500/30 bg-red-500/10 text-red-400">
            <div class="font-semibold mb-2">Please fix the following:</div>
            <ul class="list-disc ml-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid lg:grid-cols-2 gap-8">



        <div class="bg-[#121214] p-6 rounded-xl border border-white/5">
            <h3 class="text-lg font-bold mb-6">Edit Shop Information</h3>

            <form method="POST" action="{{ route('shop.update') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs text-gray-500 font-semibold mb-2">Shop Name</label>
                    <input type="text" name="shop_name" value="{{ old('shop_name', $shop->shop_name ?? '') }}"
                        class="w-full bg-[#1A1A1A] border border-white/10 rounded-lg px-4 py-3 text-sm text-white outline-none focus:border-orange-500"
                        required>
                </div>

                <div>
                    <label class="block text-xs text-gray-500 font-semibold mb-2">Address</label>
                    <input type="text" name="address" value="{{ old('address', $shop->address ?? '') }}"
                        class="w-full bg-[#1A1A1A] border border-white/10 rounded-lg px-4 py-3 text-sm text-white outline-none focus:border-orange-500"
                        required>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-gray-500 font-semibold mb-2">Latitude</label>
                        <input type="text" name="latitude" value="{{ old('latitude', $shop->latitude ?? '') }}"
                            class="w-full bg-[#1A1A1A] border border-white/10 rounded-lg px-4 py-3 text-sm text-white outline-none focus:border-orange-500"
                            required>
                    </div>

                    <div>
                        <label class="block text-xs text-gray-500 font-semibold mb-2">Longitude</label>
                        <input type="text" name="longitude" value="{{ old('longitude', $shop->longitude ?? '') }}"
                            class="w-full bg-[#1A1A1A] border border-white/10 rounded-lg px-4 py-3 text-sm text-white outline-none focus:border-orange-500"
                            required>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-gray-500 font-semibold mb-2">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $shop->phone ?? '') }}"
                            class="w-full bg-[#1A1A1A] border border-white/10 rounded-lg px-4 py-3 text-sm text-white outline-none focus:border-orange-500">
                    </div>

                    <div>
                        <label class="block text-xs text-gray-500 font-semibold mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email', $shop->email ?? '') }}"
                            class="w-full bg-[#1A1A1A] border border-white/10 rounded-lg px-4 py-3 text-sm text-white outline-none focus:border-orange-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs text-gray-500 font-semibold mb-2">Status</label>
                    <select name="status"
                        class="w-full bg-[#1A1A1A] border border-white/10 rounded-lg px-4 py-3 text-sm text-white outline-none focus:border-orange-500"
                        required>
                        <option value="open" {{ old('status', $shop->status ?? '') === 'open' ? 'selected' : '' }}>Open
                        </option>
                        <option value="busy" {{ old('status', $shop->status ?? '') === 'busy' ? 'selected' : '' }}>Busy
                        </option>
                        <option value="closed" {{ old('status', $shop->status ?? '') === 'closed' ? 'selected' : '' }}>
                            Closed</option>
                    </select>
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="px-6 py-3 bg-[#F7941D] text-black rounded-lg font-bold hover:bg-[#FF6B35] transition">
                        Save Shop Information
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-[#121214] p-6 rounded-xl border border-white/5">
            <h3 class="text-lg font-bold mb-6">Current Shop Preview</h3>

            <div class="space-y-4 text-sm">
                <div>
                    <label class="text-xs text-gray-500 font-semibold">Shop Name</label>
                    <p class="text-sm font-medium mt-2">{{ $shop->shop_name ?? 'Your Shop' }}</p>
                </div>

                <div>
                    <label class="text-xs text-gray-500 font-semibold">Address</label>
                    <p class="text-sm font-medium mt-2">{{ $shop->address ?? 'Not set' }}</p>
                </div>

                <div>
                    <label class="text-xs text-gray-500 font-semibold">Phone</label>
                    <p class="text-sm font-medium mt-2">{{ $shop->phone ?? 'Not set' }}</p>
                </div>

                <div>
                    <label class="text-xs text-gray-500 font-semibold">Email</label>
                    <p class="text-sm font-medium mt-2">{{ $shop->email ?? 'Not set' }}</p>
                </div>

                <div>
                    <label class="text-xs text-gray-500 font-semibold">Rating</label>
                    <p class="text-sm font-medium mt-2">⭐ {{ $shop->rating ?? '0.0' }} / 5.0</p>
                </div>
            </div>
        </div>

    </div>

    {{-- ── SHOP IMAGES ── --}}
    <div class="mt-8 bg-[#121214] p-6 rounded-xl border border-white/5">
        <h3 class="text-lg font-bold mb-6">Shop Images</h3>

        <form method="POST" action="{{ route('shop.upload-images') }}" enctype="multipart/form-data">
            @csrf

            <div class="grid md:grid-cols-2 gap-8">

                {{-- Logo --}}
                <div>
                    <label class="block text-xs text-gray-500 font-semibold mb-3">Shop Logo</label>
                    <div class="flex items-center gap-4 mb-3">
                        <div id="logoPreview"
                            class="w-16 h-16 rounded-xl bg-[#1A1A1A] border border-white/10 overflow-hidden flex items-center justify-center flex-shrink-0">
                            @if (!empty($shop->logo))
                                <img src="{{ Storage::url($shop->logo) }}" class="w-full h-full object-cover"
                                    id="logoImg" alt="Logo">
                            @else
                                <i class="fa-solid fa-store text-2xl text-gray-600" id="logoPlaceholder"></i>
                            @endif
                        </div>
                        <div>
                            <input type="file" name="logo" id="logoInput" accept="image/*" class="hidden"
                                onchange="previewImg(this,'logoPreview','logoImg','logoPlaceholder')">
                            <button type="button" onclick="document.getElementById('logoInput').click()"
                                class="px-4 py-2 bg-[#1A1A1A] border border-white/10 rounded-lg text-sm hover:border-orange-500 transition">
                                <i class="fa-solid fa-upload mr-2 text-orange-400"></i>Upload Logo
                            </button>
                            <p class="text-xs text-gray-600 mt-1">JPG, PNG, WEBP · Max 2MB</p>
                        </div>
                    </div>
                </div>

                {{-- Cover Photo --}}
                <div>
                    <label class="block text-xs text-gray-500 font-semibold mb-3">Cover Photo</label>
                    <div id="coverPreview"
                        class="rounded-xl overflow-hidden bg-[#1A1A1A] border border-white/10 mb-3 flex items-center justify-center"
                        style="height:100px;">
                        @if (!empty($shop->cover_photo))
                            <img src="{{ Storage::url($shop->cover_photo) }}" class="w-full h-full object-cover"
                                id="coverImg" alt="Cover">
                        @else
                            <i class="fa-solid fa-image text-3xl text-gray-600" id="coverPlaceholder"></i>
                        @endif
                    </div>
                    <input type="file" name="cover_photo" id="coverInput" accept="image/*" class="hidden"
                        onchange="previewImg(this,'coverPreview','coverImg','coverPlaceholder')">
                    <button type="button" onclick="document.getElementById('coverInput').click()"
                        class="px-4 py-2 bg-[#1A1A1A] border border-white/10 rounded-lg text-sm hover:border-orange-500 transition">
                        <i class="fa-solid fa-upload mr-2 text-orange-400"></i>Upload Cover
                    </button>
                    <p class="text-xs text-gray-600 mt-1">JPG, PNG, WEBP · Max 4MB · 1200×400 recommended</p>
                </div>

            </div>

            <div class="mt-6">
                <button type="submit"
                    class="px-6 py-3 bg-[#F7941D] text-black rounded-lg font-bold hover:bg-[#FF6B35] transition">
                    Save Images
                </button>
            </div>
        </form>
    </div>

    <script>
        function previewImg(input, previewId, imgId, placeholderId) {
            if (!input.files || !input.files[0]) return;
            const url = URL.createObjectURL(input.files[0]);
            const wrap = document.getElementById(previewId);
            let img = document.getElementById(imgId);
            const ph = document.getElementById(placeholderId);
            if (!img) {
                img = document.createElement('img');
                img.id = imgId;
                img.className = 'w-full h-full object-cover';
                wrap.innerHTML = '';
                wrap.appendChild(img);
            }
            img.src = url;
            img.style.display = 'block';
            if (ph) ph.style.display = 'none';
        }

        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        }

        async function toggleShopStatus() {
            try {
                const response = await fetch('/shop/settings/toggle-status', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    location.reload();
                } else {
                    alert('Failed to toggle shop status.');
                }
            } catch (error) {
                alert('Error toggling shop status.');
            }
        }
    </script>

@endsection
