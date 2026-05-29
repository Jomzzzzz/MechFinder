@extends('layouts.shop')

@section('content')

    <div class="page-header"
        style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <div>
            <div class="page-pretitle">Shop</div>
            <h1 class="page-title">Mechanics</h1>
        </div>
        <button onclick="document.getElementById('add-mechanic-modal').style.display='flex'" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Mechanic
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success"><i class="fas fa-circle-check" style="margin-right:6px;"></i>{{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul style="margin:0; padding-left:18px;">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($mechanics->isEmpty())
        <div class="t-card" style="padding:56px 24px; text-align:center;">
            <i class="fas fa-tools" style="font-size:36px; color:#c8ccd0; margin-bottom:14px; display:block;"></i>
            <p style="font-size:16px; font-weight:600; color:#667382; margin:0 0 6px;">No mechanics added yet</p>
            <p style="font-size:13px; color:#a0a8b1; margin:0;">Click <strong>Add Mechanic</strong> to get started.</p>
        </div>
    @else
        <div class="t-card" style="overflow:hidden;">
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="border-bottom:1px solid #e6e7eb;">
                            <th
                                style="padding:12px 16px; text-align:left; font-size:11px; font-weight:700; color:#667382; text-transform:uppercase; letter-spacing:.04em;">
                                Name</th>
                            <th
                                style="padding:12px 16px; text-align:left; font-size:11px; font-weight:700; color:#667382; text-transform:uppercase; letter-spacing:.04em;">
                                Email</th>
                            <th
                                style="padding:12px 16px; text-align:left; font-size:11px; font-weight:700; color:#667382; text-transform:uppercase; letter-spacing:.04em;">
                                Plate</th>
                            <th
                                style="padding:12px 16px; text-align:left; font-size:11px; font-weight:700; color:#667382; text-transform:uppercase; letter-spacing:.04em;">
                                Phone</th>
                            <th
                                style="padding:12px 16px; text-align:left; font-size:11px; font-weight:700; color:#667382; text-transform:uppercase; letter-spacing:.04em;">
                                Status</th>
                            <th
                                style="padding:12px 16px; text-align:right; font-size:11px; font-weight:700; color:#667382; text-transform:uppercase; letter-spacing:.04em;">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mechanics as $profile)
                            @php
                                $mStatus = $profile->status ?? 'available';
                                $mBadge = match ($mStatus) {
                                    'dispatched' => 'badge-warning',
                                    'off_duty' => 'badge-secondary',
                                    default => 'badge-success',
                                };
                            @endphp
                            <tr style="border-bottom:1px solid #f0f2f5; transition:background .1s;"
                                onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background=''">
                                <td style="padding:12px 16px; font-size:14px; font-weight:600; color:#1d273b;">
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <div
                                            style="width:32px; height:32px; background:#206bc4; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                            <span
                                                style="font-size:12px; font-weight:700; color:#fff;">{{ strtoupper(substr($profile->user->name ?? 'M', 0, 1)) }}</span>
                                        </div>
                                        {{ $profile->user->name ?? '—' }}
                                    </div>
                                </td>
                                <td style="padding:12px 16px; font-size:13px; color:#667382;">
                                    {{ $profile->user->email ?? '—' }}</td>
                                <td
                                    style="padding:12px 16px; font-size:13px; font-family:monospace; color:#206bc4; font-weight:600;">
                                    {{ $profile->plate_number ?? '—' }}</td>
                                <td style="padding:12px 16px; font-size:13px; color:#667382;">{{ $profile->phone ?? '—' }}
                                </td>
                                <td style="padding:12px 16px;">
                                    <span
                                        class="badge {{ $mBadge }}">{{ strtoupper(str_replace('_', ' ', $mStatus)) }}</span>
                                </td>
                                <td style="padding:12px 16px; text-align:right;">
                                    <form method="POST" action="{{ route('shop.mechanics.delete', $profile->id) }}"
                                        id="del-mech-{{ $profile->id }}" onsubmit="return false;">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="showConfirmModal('Remove Mechanic','Remove {{ addslashes($profile->user->name ?? 'this mechanic') }} from your shop?',function(){document.getElementById('del-mech-{{ $profile->id }}').onsubmit=null;document.getElementById('del-mech-{{ $profile->id }}').submit();},'Remove','#d63939')">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Add Mechanic Modal --}}
    <div id="add-mechanic-modal"
        style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,.5); align-items:center; justify-content:center; padding:16px;">
        <div style="width:100%; max-width:460px; background:#fff; border-radius:10px; padding:28px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h3 style="font-size:17px; font-weight:700; color:#1d273b; margin:0;">Add Mechanic</h3>
                <button onclick="document.getElementById('add-mechanic-modal').style.display='none'"
                    style="background:none; border:none; font-size:20px; color:#667382; cursor:pointer; line-height:1;">&times;</button>
            </div>
            <form method="POST" action="{{ route('shop.mechanics.store') }}"
                style="display:flex; flex-direction:column; gap:14px;">
                @csrf
                <div><label class="form-label">Full Name</label><input type="text" name="name" required
                        placeholder="Juan dela Cruz" class="form-control"></div>
                <div><label class="form-label">Email Address</label><input type="email" name="email" required
                        placeholder="mechanic@example.com" class="form-control"></div>
                <div><label class="form-label">Temporary Password</label><input type="password" name="password" required
                        placeholder="Minimum 6 characters" class="form-control"></div>
                <div><label class="form-label">Plate Number <span
                            style="color:#a0a8b1; font-weight:400;">(optional)</span></label><input type="text"
                        name="plate_number" placeholder="ABC 1234" class="form-control" style="text-transform:uppercase;">
                </div>
                <div><label class="form-label">Phone <span
                            style="color:#a0a8b1; font-weight:400;">(optional)</span></label><input type="text"
                        name="phone" placeholder="09XXXXXXXXX" class="form-control"></div>
                <button type="submit" class="btn btn-primary"
                    style="width:100%; justify-content:center; margin-top:4px;">Add Mechanic</button>
            </form>
        </div>
    </div>

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.getElementById('add-mechanic-modal').style.display = 'flex';
            });
        </script>
    @endif

@endsection
