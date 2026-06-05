@extends('layouts.mechanic-mobile')

@section('main-class', '')

@section('content')
    <style>
        :root {
            --nav-h: 60px;
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
            --sh-card: 0 1px 4px rgba(0, 0, 0, .08);
        }

        #mfApp {
            height: 100svh;
            height: 100dvh;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .profile-content {
            flex: 1;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            padding: 20px 14px 48px;
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .prof-hero {
            background: linear-gradient(145deg, var(--action) 0%, var(--action-2) 100%);
            padding: 28px 20px 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            border-radius: 0;
            box-shadow: none;
            width: 100%;
        }

        .prof-avatar {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background: var(--brand);
            color: #fff;
            font-size: 26px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid rgba(255, 255, 255, .2);
        }

        .prof-hero-name {
            font-size: 17px;
            font-weight: 700;
            color: #fff;
            text-align: center;
        }

        .prof-hero-email {
            font-size: 12px;
            color: rgba(255, 255, 255, .65);
            margin-top: 1px;
            text-align: center;
        }

        .prof-hero-badge {
            margin-top: 6px;
            padding: 3px 10px;
            border-radius: 99px;
            background: rgba(255, 255, 255, .12);
            font-size: 10px;
            font-weight: 600;
            color: rgba(255, 255, 255, .8);
            letter-spacing: .05em;
        }

        .prof-section-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .09em;
            color: var(--text-3);
            padding: 0 2px;
            margin-bottom: 6px;
        }

        .prof-group {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r2);
            overflow: hidden;
        }

        .prof-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 14px;
            background: none;
            width: 100%;
            text-align: left;
            cursor: default;
        }

        .prof-row+.prof-row {
            border-top: 1px solid var(--border);
        }

        .prof-row-icon {
            width: 34px;
            height: 34px;
            border-radius: var(--r1);
            flex-shrink: 0;
            background: var(--action-bg);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .prof-row-icon i {
            font-size: 13px;
            color: var(--action);
        }

        .prof-row-icon.red {
            background: rgba(239, 68, 68, .08);
        }

        .prof-row-icon.red i {
            color: var(--red);
        }

        .prof-row-body {
            flex: 1;
            min-width: 0;
        }

        .prof-row-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-1);
        }

        .prof-row-sub {
            font-size: 11px;
            color: var(--text-2);
            margin-top: 1px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .prof-row-sub.empty {
            color: var(--text-3);
            font-style: italic;
        }

        .prof-row-chevron {
            font-size: 10px;
            color: var(--text-3);
            flex-shrink: 0;
        }

        .logout-btn {
            width: 100%;
            padding: 14px;
            background: none;
            border: 1.5px solid var(--red);
            border-radius: var(--r2);
            color: var(--red);
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .info-note {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            padding: 10px;
            background: rgba(59, 130, 246, .05);
            border-left: 3px solid #3B82F6;
            border-radius: 6px;
            font-size: 11px;
            color: var(--text-2);
            margin-top: 8px;
        }

        .info-note i {
            color: #3B82F6;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .logout-btn:active {
            background: rgba(239, 68, 68, .08);
            transform: scale(.98);
        }

        

        /* ── BOTTOM NAVIGATION ── */
        #bottomNav {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: var(--nav-h);
            z-index: 40;
            background: var(--surface);
            border-top: 1px solid var(--border);
            display: flex;
            align-items: stretch;
            -webkit-tap-highlight-color: transparent;
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
            position: relative;
            text-decoration: none;
            padding: 0;
        }

        .nav-btn.active {
            color: var(--brand);
        }

        .n-icon {
            font-size: 20px;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .n-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            line-height: 1.1;
        }

        .nav-badge {
            position: absolute;
            top: 10px;
            right: calc(50% - 15px);
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--brand);
            border: 2px solid var(--surface);
            display: none;
            z-index: 1;
        }

        .nav-badge.show {
            display: block;
        }

        @media (min-width: 768px) {
            .profile-content {
                padding-bottom: 1rem;
            }
        }

        .alert {
            background: rgba(34, 197, 94, .1);
            border: 1px solid rgba(34, 197, 94, .2);
            color: #047857;
            padding: 12px;
            border-radius: 8px;
            font-size: 12px;
            margin-bottom: 12px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 8px;
        }

        .form-label {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-1);
        }

        .form-input {
            width: 100%;
            padding: 12px 14px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: #F8FAFC;
            color: var(--text-1);
            font-size: 14px;
        }

        .form-error {
            color: var(--red);
            font-size: 11px;
            margin-top: 4px;
        }

        .form-submit {
            width: 100%;
            margin-top: 6px;
        }

        .prof-row.clickable {
            cursor: pointer;
        }

        .prof-row.clickable:hover {
            background: rgba(247, 148, 29, .04);
        }

        .edit-panel {
            display: none;
            animation: fadeIn .18s ease;
        }

        .edit-panel.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <div id="mfApp">
        <div class="prof-hero">
            <div class="prof-avatar">{{ strtoupper(substr($mechanic->name, 0, 1)) }}</div>
            <div>
                <div class="prof-hero-name">{{ $mechanic->name }}</div>
                <div class="prof-hero-email">{{ $mechanic->email }}</div>
            </div>
            <div class="prof-hero-badge"><i class="fas fa-shield-halved" style="margin-right:4px;"></i>Mechanic Account</div>
        </div>

        <div class="profile-content">
        @if (session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif
        @if (session('pw_success'))
            <div class="alert">{{ session('pw_success') }}</div>
        @endif

        @php
            $showPhonePanel = old('phone') !== null || $errors->has('phone');
            $showPlatePanel = old('plate_number') !== null || $errors->has('plate_number');
            $showPasswordPanel = $errors->has('current_password') || $errors->has('password');
        @endphp

        @if ($profile)
            <div>
                <p class="prof-section-label">Service Information</p>
                <div class="prof-group">
                    <div class="prof-row">
                        <div class="prof-row-icon"><i class="fas fa-wrench"></i></div>
                        <div class="prof-row-body">
                            <div class="prof-row-title">Status</div>
                            <div class="prof-row-sub">{{ ucfirst($profile->status ?? 'Active') }}</div>
                        </div>
                        <i class="fa-chevron-right fa-solid prof-row-chevron"></i>
                    </div>
                    <div class="prof-row clickable" data-panel="phonePanel">
                        <div class="prof-row-icon"><i class="fas fa-phone"></i></div>
                        <div class="prof-row-body">
                            <div class="prof-row-title">Contact Number</div>
                            <div class="prof-row-sub {{ !$profile->phone ? 'empty' : '' }}">{{ $profile->phone ?? 'Tap to update' }}</div>
                        </div>
                        <i class="fa-chevron-right fa-solid prof-row-chevron"></i>
                    </div>
                    <div class="prof-row clickable" data-panel="platePanel">
                        <div class="prof-row-icon"><i class="fas fa-id-card"></i></div>
                        <div class="prof-row-body">
                            <div class="prof-row-title">Vehicle Plate</div>
                            <div class="prof-row-sub {{ !$profile->plate_number ? 'empty' : '' }}">{{ $profile->plate_number ?? 'Tap to update' }}</div>
                        </div>
                        <i class="fa-chevron-right fa-solid prof-row-chevron"></i>
                    </div>
                </div>
                <p style="font-size:10px;color:var(--text-3);margin-top:6px;padding:0 4px;line-height:1.5;">
                    <i class="fas fa-circle-info" style="margin-right:3px;"></i>
                    This info is shared with the motorist when you accept their service request.
                </p>
            </div>

            <div id="phonePanel" class="edit-panel{{ $showPhonePanel ? ' active' : '' }}">
                <p class="prof-section-label">Edit Contact Number</p>
                <div class="prof-group">
                    <form method="POST" action="{{ route('mechanic.profile.update') }}">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="phone">Contact Number</label>
                            <input id="phone" name="phone" type="text" class="form-input" value="{{ old('phone', $profile->phone) }}">
                            @error('phone') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <button type="submit" class="action-btn action-btn-primary form-submit">Save Contact Number</button>
                    </form>
                </div>
            </div>

            <div id="platePanel" class="edit-panel{{ $showPlatePanel ? ' active' : '' }}">
                <p class="prof-section-label">Edit Vehicle Plate</p>
                <div class="prof-group">
                    <form method="POST" action="{{ route('mechanic.profile.update') }}">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="plate_number">Plate Number</label>
                            <input id="plate_number" name="plate_number" type="text" class="form-input" value="{{ old('plate_number', $profile->plate_number) }}">
                            @error('plate_number') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <button type="submit" class="action-btn action-btn-primary form-submit">Save Plate Number</button>
                    </form>
                </div>
            </div>

            <div>
                <p class="prof-section-label">Account & Security</p>
                <div class="prof-group">
                    <div class="prof-row clickable" data-panel="passwordPanel">
                        <div class="prof-row-icon red"><i class="fas fa-lock"></i></div>
                        <div class="prof-row-body">
                            <div class="prof-row-title">Change Password</div>
                            <div class="prof-row-sub">Update your login password</div>
                        </div>
                        <i class="fa-chevron-right fa-solid prof-row-chevron"></i>
                    </div>
                </div>
            </div>

            <div id="passwordPanel" class="edit-panel{{ $showPasswordPanel ? ' active' : '' }}">
                <p class="prof-section-label">Change Password</p>
                <div class="prof-group">
                    <form method="POST" action="{{ route('mechanic.profile.password') }}">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="current_password">Current Password</label>
                            <input id="current_password" name="current_password" type="password" class="form-input">
                            @error('current_password') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="password">New Password</label>
                            <input id="password" name="password" type="password" class="form-input">
                            @error('password') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="password_confirmation">Confirm Password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="form-input">
                        </div>
                        <button type="submit" class="action-btn action-btn-primary form-submit">Update Password</button>
                    </form>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Log Out
            </button>
        </form>

        <nav id="bottomNav">
            <a href="{{ route('mechanic.dashboard') }}" class="nav-btn {{ request()->routeIs('mechanic.dashboard') ? 'active' : '' }}" title="Jobs">
                <span class="n-icon"><i class="fas fa-briefcase"></i></span>
                <span class="n-label">Jobs</span>
            </a>
            <a href="{{ route('mechanic.profile') }}" class="nav-btn {{ request()->routeIs('mechanic.profile') ? 'active' : '' }}" title="Profile">
                <span class="n-icon"><i class="fas fa-user"></i></span>
                <span class="n-label">Profile</span>
            </a>
        </nav>
    </div>

    <script>
        document.querySelectorAll('.prof-row.clickable').forEach((row) => {
            row.addEventListener('click', () => {
                const panelId = row.getAttribute('data-panel');
                if (!panelId) return;

                document.querySelectorAll('.edit-panel').forEach((panel) => {
                    if (panel.id === panelId) {
                        panel.classList.toggle('active');
                    } else {
                        panel.classList.remove('active');
                    }
                });
            });
        });
    </script>
@endsection

