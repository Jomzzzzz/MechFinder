@extends('layouts.shop')

@section('content')
    <div class="page-header">
        <div class="page-pretitle">Shop</div>
        <h1 class="page-title">Settings</h1>
    </div>

    @if (session('success'))
        <div class="alert alert-success"><i class="fas fa-circle-check" style="margin-right:6px;"></i>{{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <div style="font-weight:600; margin-bottom:6px;">Please fix the following:</div>
            <ul style="margin:0; padding-left:18px;">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">

        {{-- Edit Shop Information --}}
        <div class="t-card" style="padding:24px;">
            <h3 style="font-size:15px; font-weight:700; margin:0 0 20px; color:#1d273b;">Shop Information</h3>
            <form method="POST" action="{{ route('shop.update') }}" style="display:flex; flex-direction:column; gap:14px;">
                @csrf
                <div>
                    <label class="form-label">Shop Name</label>
                    <input type="text" name="shop_name" value="{{ old('shop_name', $shop->shop_name ?? '') }}"
                        class="form-control" required>
                </div>
                <div>
                    <label class="form-label">Address</label>
                    <input type="text" name="address" value="{{ old('address', $shop->address ?? '') }}"
                        class="form-control" required>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div>
                        <label class="form-label">Latitude</label>
                        <input type="text" name="latitude" value="{{ old('latitude', $shop->latitude ?? '') }}"
                            class="form-control" required>
                    </div>
                    <div>
                        <label class="form-label">Longitude</label>
                        <input type="text" name="longitude" value="{{ old('longitude', $shop->longitude ?? '') }}"
                            class="form-control" required>
                    </div>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div>
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $shop->phone ?? '') }}"
                            class="form-control">
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email', $shop->email ?? '') }}"
                            class="form-control">
                    </div>
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select name="status_id" class="form-control" required>
                        @foreach ($shopStatuses as $ss)
                            <option value="{{ $ss->id }}"
                                {{ (int) old('status_id', $shop->status_id ?? 0) === (int) $ss->id ? 'selected' : '' }}>
                                {{ $ss->label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Save Information</button>
                </div>
            </form>
        </div>

        {{-- Shop Preview --}}
        <div class="t-card" style="padding:24px;">
            <h3 style="font-size:15px; font-weight:700; margin:0 0 20px; color:#1d273b;">Current Info</h3>
            <div style="display:flex; flex-direction:column; gap:14px;">
                @foreach (['Shop Name' => $shop->shop_name ?? 'Not set', 'Address' => $shop->address ?? 'Not set', 'Phone' => $shop->phone ?? 'Not set', 'Email' => $shop->email ?? 'Not set'] as $lbl => $val)
                    <div style="padding-bottom:14px; border-bottom:1px solid #f0f2f5;">
                        <p
                            style="font-size:11px; font-weight:600; color:#667382; text-transform:uppercase; margin:0 0 4px;">
                            {{ $lbl }}</p>
                        <p style="font-size:14px; color:#1d273b; margin:0;">{{ $val }}</p>
                    </div>
                @endforeach
                <div>
                    <p style="font-size:11px; font-weight:600; color:#667382; text-transform:uppercase; margin:0 0 4px;">
                        Status</p>
                    @php
                        $sBadges = [
                            'open' => 'badge-success',
                            'busy' => 'badge-warning',
                            'closed' => 'badge-danger',
                            'maintenance' => 'badge-info',
                        ];
                        $sb = $sBadges[$shop->status ?? 'closed'] ?? 'badge-secondary';
                    @endphp
                    <span class="badge {{ $sb }}">{{ ucfirst($shop->status ?? 'closed') }}</span>
                </div>
                @if (($shop->rating ?? 0) > 0)
                    <div>
                        <p
                            style="font-size:11px; font-weight:600; color:#667382; text-transform:uppercase; margin:0 0 4px;">
                            Rating</p>
                        <p style="font-size:14px; color:#1d273b; margin:0;">⭐ {{ $shop->rating }} / 5.0</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Shop Images --}}
    <div class="t-card" style="padding:24px; margin-bottom:20px;">
        <h3 style="font-size:15px; font-weight:700; margin:0 0 20px; color:#1d273b;">Shop Images</h3>
        <form method="POST" action="{{ route('shop.upload-images') }}" enctype="multipart/form-data">
            @csrf
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px;">
                {{-- Logo --}}
                <div>
                    <label class="form-label">Shop Logo</label>
                    <div style="display:flex; align-items:center; gap:14px; margin-bottom:10px;">
                        <div id="logoPreview"
                            style="width:64px; height:64px; border-radius:8px; background:#f4f6fb; border:1px solid #e6e7eb; overflow:hidden; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            @if (!empty($shop->logo))
                                <img src="{{ Storage::url($shop->logo) }}"
                                    style="width:100%; height:100%; object-fit:cover;" id="logoImg" alt="Logo">
                            @else
                                <i class="fas fa-store" style="font-size:24px; color:#c8ccd0;" id="logoPlaceholder"></i>
                            @endif
                        </div>
                        <div>
                            <input type="file" name="logo" id="logoInput" accept="image/*" style="display:none;"
                                onchange="previewImg(this,'logoPreview','logoImg','logoPlaceholder')">
                            <button type="button" onclick="document.getElementById('logoInput').click()"
                                class="btn btn-secondary btn-sm">
                                <i class="fas fa-upload"></i> Upload Logo
                            </button>
                            <p style="font-size:11px; color:#a0a8b1; margin:4px 0 0;">JPG, PNG · Max 2MB</p>
                        </div>
                    </div>
                </div>
                {{-- Cover --}}
                <div>
                    <label class="form-label">Cover Photo</label>
                    <div id="coverPreview"
                        style="border-radius:8px; overflow:hidden; background:#f4f6fb; border:1px solid #e6e7eb; margin-bottom:10px; height:80px; display:flex; align-items:center; justify-content:center;">
                        @if (!empty($shop->cover_photo))
                            <img src="{{ Storage::url($shop->cover_photo) }}"
                                style="width:100%; height:100%; object-fit:cover;" id="coverImg" alt="Cover">
                        @else
                            <i class="fas fa-image" style="font-size:28px; color:#c8ccd0;" id="coverPlaceholder"></i>
                        @endif
                    </div>
                    <input type="file" name="cover_photo" id="coverInput" accept="image/*" style="display:none;"
                        onchange="previewImg(this,'coverPreview','coverImg','coverPlaceholder')">
                    <button type="button" onclick="document.getElementById('coverInput').click()"
                        class="btn btn-secondary btn-sm">
                        <i class="fas fa-upload"></i> Upload Cover
                    </button>
                    <p style="font-size:11px; color:#a0a8b1; margin:4px 0 0;">JPG, PNG · Max 4MB · 1200×400 recommended</p>
                </div>
            </div>
            <div style="margin-top:18px;">
                <button type="submit" class="btn btn-primary">Save Images</button>
            </div>
        </form>
    </div>

    <script>
        function previewImg(input, previewId, imgId, placeholderId) {
            const file = input.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById(previewId);
                let img = document.getElementById(imgId);
                const placeholder = document.getElementById(placeholderId);
                if (!img) {
                    img = document.createElement('img');
                    img.id = imgId;
                    img.style = 'width:100%; height:100%; object-fit:cover;';
                    preview.innerHTML = '';
                    preview.appendChild(img);
                }
                img.src = e.target.result;
                if (placeholder) placeholder.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    </script>
@endsection
