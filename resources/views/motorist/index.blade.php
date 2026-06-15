@extends('layouts.motorist')

@section('main-class', '')

@push('styles')
    @include('motorist.partials._styles')
@endpush

@section('content')
    <div id="mfApp">
        <div id="mapArea">

            {{-- SPLASH LOADER --}}
            <div id="mfLoader">
                <div class="mf-loader-logo"><i class="fa-solid fa-motorcycle"></i></div>
                <div class="mf-loader-label">MechFinder</div>
                <div class="mf-loader-sub">Getting everything readyâ€¦</div>
                <div class="mf-loader-spinner"></div>
            </div>

            {{-- MAP --}}
            <div id="map"></div>

            {{-- LOCATE FAB --}}
            <button id="locateFab" onclick="locateUser()" title="Find my location">
                <i class="fa-solid fa-crosshairs"></i>
            </button>

            {{-- RESCUE FAB --}}
            <button id="rescueFab" onclick="openPanel('rescuePanel')" title="Request Rescue">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </button>

            {{-- RESCUE BAR --}}
            <div id="rescueBar">
                {{-- Idle state: shown when no active request --}}
                <div id="barIdle" style="display:flex;align-items:center;width:100%;">
                    <button class="btn-shops" onclick="openPanel('shopsPanel')">
                        <div class="btn-shops-icon"><i class="fa-solid fa-wrench"></i></div>
                        <div class="btn-shops-text">
                            <div class="btn-shops-title">Find a Shop</div>
                            <div class="btn-shops-sub">
                                <i class="fa-solid fa-location-dot"></i>
                                <span id="locationLineBtm">Detectingâ€¦</span>
                                &nbsp;&middot;&nbsp;
                                <i class="fa-solid fa-circle-check" style="color:var(--green);"></i>
                                <span id="openShopsCount">â€¦</span> open
                            </div>
                        </div>
                        <i class="fa-chevron-right fa-solid" style="font-size:11px;color:var(--text-3);"></i>
                    </button>
                </div>
                {{-- Active state: Grab-style step tracker --}}
                <div id="barActive" style="display:none;flex-direction:column;width:100%;" onclick="showTab('requests')">
                    {{-- Row 1: title + cancel --}}
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;width:100%;">
                        <div style="flex:1;min-width:0;">
                            <div
                                style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-3);">
                                Active Rescue</div>
                            <div id="barActiveText"
                                style="font-size:15px;font-weight:700;color:var(--text-1);margin-top:1px;line-height:1.25;">
                            </div>
                        </div>
                        <button id="cancelBtn" onclick="event.stopPropagation();cancelDispatch()"
                            style="display:none;flex-shrink:0;width:30px;height:30px;background:transparent;border:2px solid var(--red);color:var(--red);border-radius:50%;cursor:pointer;align-items:center;justify-content:center;font-size:13px;padding:0;margin-top:2px;">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    {{-- Row 2: step dots --}}
                    <div class="step-track">
                        <div class="step-dot" id="track-step-0"><i class="fa-solid fa-store"></i></div>
                        <div class="step-line" id="track-line-0"></div>
                        <div class="step-dot" id="track-step-1"><i class="fa-solid fa-motorcycle"></i></div>
                        <div class="step-line" id="track-line-1"></div>
                        <div class="step-dot" id="track-step-2"><i class="fa-solid fa-circle-check"></i></div>
                    </div>
                    {{-- Distance (prominent) --}}
                    <div id="barDistance"
                        style="display:none;align-items:center;justify-content:center;gap:8px;margin-top:8px;padding:9px 14px;background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.22);border-radius:12px;">
                        <i class="fa-solid fa-route" style="font-size:14px;color:#3B82F6;"></i>
                        <div style="display:flex;align-items:baseline;gap:3px;">
                            <span id="barDistanceVal"
                                style="font-size:22px;font-weight:800;color:#3B82F6;letter-spacing:-.5px;line-height:1;"></span>
                            <span style="font-size:11px;font-weight:600;color:var(--text-3);">away</span>
                        </div>
                    </div>
                    {{-- Mechanic chip (visible when en_route / arrived) --}}
                    <div id="barMechInfo"
                        style="display:none;align-items:center;gap:10px;margin-top:8px;padding:10px 12px;background:var(--surface-2);border-radius:12px;width:100%;">
                        <div
                            style="width:36px;height:36px;border-radius:50%;background:var(--brand-bg);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fa-solid fa-user-gear" style="font-size:14px;color:var(--brand);"></i>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div id="barMechName"
                                style="font-size:13px;font-weight:700;color:var(--text-1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            </div>
                            <div style="display:flex;align-items:center;gap:8px;margin-top:3px;flex-wrap:wrap;">
                                <span style="display:flex;align-items:center;gap:3px;">
                                    <i class="fa-solid fa-phone" style="font-size:8px;color:var(--text-3);"></i>
                                    <span id="barMechPhone" style="font-size:10px;color:var(--text-3);"></span>
                                </span>
                                <span id="barMechPlate" style="display:none;align-items:center;gap:3px;">
                                    <i class="fa-solid fa-motorcycle" style="font-size:8px;color:var(--text-3);"></i>
                                    <span id="barMechPlateVal" style="font-size:10px;color:var(--text-3);"></span>
                                </span>
                            </div>
                        </div>
                        <a id="barMsgBtn" onclick="event.stopPropagation()" href="#" target="_self"
                            style="width:36px;height:36px;background:var(--brand);border:none;color:#fff;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:15px;padding:0;text-decoration:none;flex-shrink:0;">
                            <i class="fa-solid fa-comment"></i>
                        </a>
                    </div>
                    {{-- Sub message --}}
                    <div id="barSubMsg" style="font-size:11px;color:var(--text-3);margin-top:5px;"></div>
                </div>
            </div>

            {{-- â•â• REQUEST DETAILS MODAL â•â• --}}
            <div id="detailsModal"
                style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.18);backdrop-filter:blur(10px);z-index:1000;overflow-y:auto;padding:20px;">
                <div
                    style="max-width:560px;width:100%;margin:40px auto;border-radius:24px;overflow:hidden;background:#FFFFFF;border:1px solid rgba(15,23,42,0.08);box-shadow:0 24px 60px rgba(15,23,42,0.12);">
                    <div
                        style="display:flex;justify-content:space-between;align-items:center;gap:14px;padding:22px 22px 16px;background:#FFFFFF;">
                        <div>

                            <h2 style="margin:0;font-size:22px;font-weight:700;color:#111827;letter-spacing:-0.02em;">
                                Request
                                details</h2>
                        </div>
                        <button onclick="closeDetailsModal()"
                            style="width:36px;height:36px;background:#F8FAFC;border:1px solid #E5E7EB;color:#111827;font-size:16px;cursor:pointer;border-radius:12px;display:grid;place-items:center;transition:background 0.2s;"
                            onmouseover="this.style.background='#EFF6FF'" onmouseout="this.style.background='#F8FAFC'">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div style="display:grid;gap:16px;padding:20px 22px 22px;">
                        <div style="display:grid;gap:8px;">
                            <div
                                style="font-size:11px;color:#6B7280;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;">
                                Issue</div>
                            <div id="detailIssueType"
                                style="font-size:20px;font-weight:700;color:#111827;line-height:1.2;">
                            </div>
                        </div>
                        <div style="display:grid;gap:8px;">
                            <div
                                style="font-size:11px;color:#6B7280;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;">
                                Location</div>
                            <div id="detailLocation" style="font-size:15px;color:#475569;line-height:1.6;"></div>
                        </div>
                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;">
                            <div style="border:1px solid #E5E7EB;border-radius:18px;padding:16px;background:#F8FAFC;">
                                <div
                                    style="font-size:11px;color:#6B7280;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;margin-bottom:6px;">
                                    Status</div>
                                <div id="detailStatus"
                                    style="font-size:14px;font-weight:700;color:#047857;line-height:1.4;">
                                </div>
                            </div>
                            <div style="border:1px solid #E5E7EB;border-radius:18px;padding:16px;background:#F8FAFC;">
                                <div
                                    style="font-size:11px;color:#6B7280;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;margin-bottom:6px;">
                                    Time</div>
                                <div id="detailTime"
                                    style="font-size:14px;font-weight:700;color:#475569;line-height:1.4;">
                                </div>
                            </div>
                        </div>
                        <div style="display:grid;gap:12px;">
                            <div
                                style="padding:18px;border:1px solid #E5E7EB;border-radius:20px;background:#F8FAFC;display:flex;justify-content:space-between;align-items:center;gap:12px;">
                                <span
                                    style="font-size:11px;color:#6B7280;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;">Shop</span>
                                <span id="detailShop" style="font-size:15px;color:#111827;font-weight:700;"></span>
                            </div>
                            <div
                                style="padding:18px;border:1px solid #E5E7EB;border-radius:20px;background:#F8FAFC;display:flex;justify-content:space-between;align-items:center;gap:12px;">
                                <span
                                    style="font-size:11px;color:#6B7280;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;">Mechanic</span>
                                <span id="detailMechanic" style="font-size:15px;color:#111827;font-weight:700;"></span>
                            </div>
                        </div>
                    </div>
                    <div
                        style="display:flex;flex-wrap:wrap;gap:12px;padding:18px 22px 22px;background:#FFFFFF;border-top:1px solid #E5E7EB;">
                        <button onclick="closeDetailsModal()"
                            style="flex:1;min-width:140px;padding:14px 18px;background:#FFFFFF;border:1px solid #D1D5DB;border-radius:16px;color:#111827;font-size:14px;font-weight:700;cursor:pointer;transition:background 0.2s;"
                            onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#FFFFFF'">
                            Close
                        </button>
                        <button onclick="goToMessage()"
                            style="flex:1;min-width:140px;padding:14px 18px;background:#2563EB;border:none;border-radius:16px;color:#FFFFFF;font-size:14px;font-weight:700;cursor:pointer;transition:transform 0.18s,box-shadow 0.18s;box-shadow:0 12px 30px rgba(37,99,235,0.18);"
                            onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 18px 40px rgba(37,99,235,0.24)'"
                            onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 12px 30px rgba(37,99,235,0.18)'">
                            Message
                        </button>
                    </div>
                </div>
            </div>

            {{-- â•â• SHOPS PANEL â•â• --}}
            <div id="shopsPanel" class="panel" style="display:none;">
                <div class="ph">
                    <button class="ph-back" onclick="closePanel('shopsPanel')">
                        <i class="fa-arrow-left fa-solid"></i>
                    </button>
                    <div>
                        <div class="ph-title">Nearby Shops</div>
                        <div class="ph-subtitle">Tap <i class="fa-solid fa-location-arrow" style="font-size:9px;"></i>
                            for
                            directions</div>
                    </div>
                </div>
                <div class="panel-search-wrap">
                    <div class="panel-search">
                        <i class="fa-solid fa-magnifying-glass" style="color:var(--text-3);font-size:13px;"></i>
                        <input id="shopSearchInput" type="text" placeholder="Search shop nameâ€¦"
                            oninput="filterShopList(this.value)">
                        <button id="shopSearchClear" onclick="clearShopSearch()"
                            style="display:none;background:none;border:none;color:var(--text-3);cursor:pointer;font-size:13px;padding:0;">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>
                <div id="shopList" style="padding:12px 14px 32px;"></div>
            </div>

            {{-- PROFILE SAVE CONFIRM MODAL --}}
            <div id="profileSaveModal" onclick="if(event.target===this)_profSaveClose()"
                style="position:absolute;inset:0;z-index:70;background:rgba(0,0,0,.45);display:flex;align-items:flex-end;justify-content:center;opacity:0;pointer-events:none;transition:opacity .2s ease;">
                <div class="cm-sheet"
                    style="transform:translateY(100%);transition:transform .25s cubic-bezier(.4,0,.2,1);">
                    <div class="cm-icon" style="background:rgba(251,191,36,.1);color:#f59e0b;"><i
                            class="fa-solid fa-floppy-disk"></i></div>
                    <div class="cm-title" id="profSaveModalTitle">Save Changes?</div>
                    <div class="cm-body" id="profSaveModalBody">Are you sure you want to save these changes?</div>
                    <div class="cm-actions">
                        <button id="profSaveConfirmBtn" class="cm-btn-cancel" style="background:var(--action);"><i
                                class="fa-solid fa-check"></i> Save</button>
                        <button class="cm-btn-back" onclick="_profSaveClose()">Cancel</button>
                    </div>
                </div>
            </div>

            {{-- PROFILE CHANGE REQUEST MODAL --}}
            <div id="profileChangeReqModal" onclick="if(event.target===this)_profChangeClose()"
                style="position:absolute;inset:0;z-index:70;background:rgba(0,0,0,.45);display:flex;align-items:flex-end;justify-content:center;opacity:0;pointer-events:none;transition:opacity .2s ease;">
                <div class="cm-sheet"
                    style="transform:translateY(100%);transition:transform .25s cubic-bezier(.4,0,.2,1);">
                    <div class="cm-icon" style="background:rgba(59,130,246,.1);color:#3b82f6;"><i
                            class="fa-solid fa-lock"></i></div>
                    <div class="cm-title">Request Profile Change</div>
                    <div class="cm-body">Your profile is locked for accuracy. Briefly explain why you need to update it â€”
                        our
                        admin will review and unlock it for you.</div>
                    <textarea id="profChangeReason"
                        style="width:100%;border:1px solid var(--border);border-radius:var(--r2);padding:10px 12px;font-size:13px;color:var(--text-1);background:var(--surface-2);resize:none;height:80px;outline:none;margin-bottom:4px;"
                        placeholder="e.g. I changed my motorcycle, wrong plate number enteredâ€¦"></textarea>
                    <div id="profChangeReasonErr"
                        style="font-size:11px;color:var(--red);margin-bottom:12px;display:none;">
                        Please explain the reason for your request.</div>
                    <div class="cm-actions">
                        <button id="profChangeSubmitBtn" class="cm-btn-cancel" style="background:var(--blue,#3b82f6);"><i
                                class="fa-solid fa-paper-plane"></i> Send Request</button>
                        <button class="cm-btn-back" onclick="_profChangeClose()">Cancel</button>
                    </div>
                </div>
            </div>

            {{-- CANCEL CONFIRMATION MODAL --}}
            <div id="confirmModal" onclick="_cmBgClick(event)">
                <div class="cm-sheet">
                    <div class="cm-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <div class="cm-title">Cancel Rescue Request?</div>
                    <div class="cm-body">Your request will be removed and no mechanic will be dispatched. You can submit a
                        new
                        one anytime.</div>
                    <div class="cm-actions">
                        <button class="cm-btn-cancel" onclick="_cmConfirm()"><i class="fa-solid fa-xmark"></i> Yes,
                            Cancel
                            Request</button>
                        <button class="cm-btn-back" onclick="_cmClose()">Keep Waiting</button>
                    </div>
                </div>
            </div>

          {{-- REVIEW MODAL --}}
<div id="reviewNotificationOverlay" class="review-overlay"></div>

<div id="reviewModal" class="review-modal">

    <div class="review-card">

        <!-- Success Icon -->
        <div class="review-icon">
            <i class="fa-solid fa-circle-check"></i>
        </div>

        <!-- Title -->
        <h2 class="review-title">
            Rescue Completed
        </h2>

        <p class="review-subtitle">
            Thank you for using MechFinder.
            Your feedback helps improve our rescue service.
        </p>


        <!-- Rating -->

        <div class="review-section">

            <label class="review-label">
                Rate your experience
            </label>

            <div class="rating-stars">

                <button onclick="window.setReviewRating(1)" class="rating-star" data-rating="1">★</button>

                <button onclick="window.setReviewRating(2)" class="rating-star" data-rating="2">★</button>

                <button onclick="window.setReviewRating(3)" class="rating-star" data-rating="3">★</button>

                <button onclick="window.setReviewRating(4)" class="rating-star" data-rating="4">★</button>

                <button onclick="window.setReviewRating(5)" class="rating-star" data-rating="5">★</button>

            </div>

            <p id="ratingLabel" class="rating-text">
                Select your rating
            </p>

        </div>


        <!-- Comment -->

        <div class="review-section">

            <label class="review-label">
                Comment (Optional)
            </label>

            <textarea
                id="reviewCommentField"
                maxlength="300"
                placeholder="Share your experience..."
            ></textarea>

            <div class="char-counter">
                <span id="charCountDisplay">0</span>/300
            </div>

        </div>


        <!-- Tags -->

        <div class="review-section">

            <label class="review-label">
                Highlights
            </label>

            <div class="tag-grid">

                <button onclick="toggleTag('Professional')" class="tag-btn" data-tag="Professional">Professional</button>

                <button onclick="toggleTag('Timely')" class="tag-btn" data-tag="Timely">Timely</button>

                <button onclick="toggleTag('Friendly')" class="tag-btn" data-tag="Friendly">Friendly</button>

                <button onclick="toggleTag('Fair Price')" class="tag-btn" data-tag="Fair Price">Fair Price</button>

            </div>

        </div>


        <!-- Buttons -->

        <div class="review-buttons">

            <button id="submitBtn" onclick="submitReview()" class="submit-btn">
                Submit Review
            </button>

            <button onclick="skipReview()" class="skip-btn">
                Skip
            </button>

        </div>

    </div>

</div>

            {{-- SEARCH OVERLAY --}}
            <div id="searchOverlay">
                <div class="radar-wrap">
                    <div class="radar-ring"></div>
                    <div class="radar-ring"></div>
                    <div class="radar-ring"></div>
                    <div class="radar-center"><i class="fa-solid fa-magnifying-glass"></i></div>
                </div>
                <div style="text-align:center;padding:0 32px;">
                    <div style="font-size:18px;font-weight:700;color:var(--text-1);margin-bottom:6px;">Searching for
                        nearest
                        shop</div>
                    <div style="font-size:13px;color:var(--text-2);line-height:1.6;">Finding the closest available shop in
                        your
                        area</div>
                </div>
            </div>

            {{-- â•â• RESCUE PANEL â•â• --}}
            <div id="rescuePanel" class="panel" style="display:none;">

                <div class="ph">
                    <button class="ph-back" onclick="closePanel('rescuePanel')">
                        <i class="fa-arrow-left fa-solid"></i>
                    </button>
                    <div>
                        <div class="ph-title">Request Rescue</div>
                        <div class="ph-subtitle">Nearest available shop will be dispatched</div>
                    </div>
                </div>

                <div class="rescue-form">

                    {{-- Identity --}}
                    <div class="resc-section">
                        <div class="resc-label">Rescuing</div>
                        <div class="resc-identity-card">
                            <div class="resc-avatar" id="rescueAvatar">?</div>
                            <div id="identityBody" style="flex:1;min-width:0;"></div>
                            <button onclick="showTab('profile')" class="resc-edit-btn" title="Edit profile">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Issue type --}}
                    <div class="resc-section">
                        <div class="resc-label">What's the problem?</div>
                        <div class="issue-grid">
                            <button class="issue-tile" onclick="selectIssue(this,'Flat Tire')">
                                <span class="t-icon-wrap"><i class="fa-solid fa-wrench"></i></span>
                                <span class="t-label">Flat Tire</span>
                            </button>
                            <button class="issue-tile" onclick="selectIssue(this,'Engine Stall')">
                                <span class="t-icon-wrap"><i class="fa-solid fa-gear"></i></span>
                                <span class="t-label">Engine Stall</span>
                            </button>
                            <button class="issue-tile" onclick="selectIssue(this,'Battery')">
                                <span class="t-icon-wrap"><i class="fa-solid fa-battery-half"></i></span>
                                <span class="t-label">Battery</span>
                            </button>
                            <button class="issue-tile" onclick="selectIssue(this,'Brake Problem')">
                                <span class="t-icon-wrap"><i class="fa-solid fa-circle-stop"></i></span>
                                <span class="t-label">Brake</span>
                            </button>
                            <button class="issue-tile" onclick="selectIssue(this,'Chain Problem')">
                                <span class="t-icon-wrap"><i class="fa-solid fa-link"></i></span>
                                <span class="t-label">Chain</span>
                            </button>
                            <button class="issue-tile" onclick="selectIssue(this,'Other')">
                                <span class="t-icon-wrap"><i class="fa-solid fa-circle-question"></i></span>
                                <span class="t-label">Other</span>
                            </button>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="resc-section">
                        <div class="resc-label">Notes <span class="resc-label-opt"> optional</span></div>
                        <textarea id="dispatchDesc" class="mf-input" rows="3" placeholder="Describe your situationâ€¦"
                            style="resize:none;"></textarea>
                    </div>

                    {{-- GPS --}}
                    <div class="resc-gps">
                        <i class="fa-solid fa-location-dot" style="color:var(--brand);"></i>
                        GPS location shared with the dispatched shop
                    </div>

                    {{-- Submit --}}
                    <button id="rescueBtn" onclick="submitDispatch()" class="rescue-submit-btn" disabled>
                        <i class="fa-solid fa-paper-plane"></i> Send Rescue Request
                    </button>

                    <p id="noShopWarning"
                        style="display:none;text-align:center;font-size:11px;color:var(--red);margin-top:-8px;">
                        <i class="fa-solid fa-circle-exclamation"></i> No open shops found nearby. Your request has been
                        saved.
                    </p>

                </div>
            </div>

            {{-- â•â• PROFILE PANEL (overview) â•â• --}}
            <div id="profilePanel" class="panel" style="display:none;">

                {{-- Hero --}}
                <div class="prof-hero">
                    <div class="prof-avatar">{{ strtoupper(substr(optional(Auth::user())->name ?? 'U', 0, 1)) }}</div>
                    <div>
                        <div class="prof-hero-name">{{ optional(Auth::user())->name ?? 'Motorist' }}</div>
                        <div class="prof-hero-email">{{ optional(Auth::user())->email ?? '' }}</div>
                    </div>
                    <div class="prof-hero-badge"><i class="fa-solid fa-shield-halved"
                            style="margin-right:4px;"></i>Motorist
                        Account</div>
                </div>

                <div style="padding:20px 14px 48px;display:flex;flex-direction:column;gap:22px;overflow-y:auto;">

                    {{-- Ride info --}}
                    <div>
                        <p class="prof-section-label">Ride Information</p>
                        <div class="prof-group">
                            <button class="prof-row" onclick="openSubPanel('editMotoPanel')">
                                <div class="prof-row-icon"><i class="fa-solid fa-motorcycle"></i></div>
                                <div class="prof-row-body">
                                    <div class="prof-row-title">My Motorcycle</div>
                                    <div id="profMotoSub" class="prof-row-sub empty">Tap to add â€” make, model, plate
                                    </div>
                                </div>
                                <i class="fa-chevron-right fa-solid prof-row-chevron"></i>
                            </button>
                            <button class="prof-row" onclick="openSubPanel('editContactPanel')">
                                <div class="prof-row-icon"><i class="fa-solid fa-phone"></i></div>
                                <div class="prof-row-body">
                                    <div class="prof-row-title">Dispatch Contact</div>
                                    <div id="profContactSub" class="prof-row-sub empty">Tap to add â€” name &amp; phone
                                        number
                                    </div>
                                </div>
                                <i class="fa-chevron-right fa-solid prof-row-chevron"></i>
                            </button>
                        </div>
                        <p style="font-size:10px;color:var(--text-3);margin-top:6px;padding:0 4px;line-height:1.5;">
                            <i class="fa-solid fa-circle-info" style="margin-right:3px;"></i>
                            This info is shared with the mechanic when you request rescue.
                        </p>
                    </div>

                    {{-- Account --}}
                    <div>
                        <p class="prof-section-label">Account &amp; Security</p>
                        <div class="prof-group">
                            <button class="prof-row" onclick="openSubPanel('changePasswordPanel')">
                                <div class="prof-row-icon"><i class="fa-solid fa-lock"></i></div>
                                <div class="prof-row-body">
                                    <div class="prof-row-title">Change Password</div>
                                    <div class="prof-row-sub">Update your login password</div>
                                </div>
                                <i class="fa-chevron-right fa-solid prof-row-chevron"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout-btn">
                            <i class="fa-right-from-bracket fa-solid"></i> Log Out
                        </button>
                    </form>

                </div>
            </div>

            {{-- â•â• EDIT MOTORCYCLE SUB-PANEL â•â• --}}
            <div id="editMotoPanel" class="panel" style="display:none;">
                <div class="ph">
                    <button class="ph-back" onclick="closeSubPanel('editMotoPanel')">
                        <i class="fa-arrow-left fa-solid"></i>
                    </button>
                    <div style="flex:1;">
                        <div class="ph-title">My Motorcycle</div>
                    </div>
                    <button onclick="saveMoto()" class="save-panel-btn">Save</button>
                </div>
                <div style="padding:16px 14px 40px;display:flex;flex-direction:column;gap:16px;">

                    <div class="info-banner">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>This info is shared with the mechanic when you request a rescue. Keep it accurate so they can
                            identify your bike.</span>
                    </div>

                    <div class="field-group">
                        <label class="field-label" for="pMakeModel">Make &amp; Model <span
                                style="color:var(--red);">*</span></label>
                        <input id="pMakeModel" class="mf-input" placeholder="e.g. Honda Wave 110, Yamaha Mio">
                        <span class="field-hint">The brand and model of your motorcycle</span>
                    </div>

                    <div class="field-group">
                        <label class="field-label" for="pColor">Color &amp; Variant</label>
                        <input id="pColor" class="mf-input" placeholder="e.g. Black Alpha, Red Sports">
                        <span class="field-hint">Color and edition â€” helps the mechanic spot your bike</span>
                    </div>

                    <div class="field-group">
                        <label class="field-label" for="pPlate">Plate / Conduction Number</label>
                        <input id="pPlate" class="mf-input" placeholder="e.g. ABC 1234 or conduction sticker">
                        <span class="field-hint">Official plate or temporary conduction number</span>
                    </div>

                </div>
            </div>

            {{-- â•â• EDIT CONTACT SUB-PANEL â•â• --}}
            <div id="editContactPanel" class="panel" style="display:none;">
                <div class="ph">
                    <button class="ph-back" onclick="closeSubPanel('editContactPanel')">
                        <i class="fa-arrow-left fa-solid"></i>
                    </button>
                    <div style="flex:1;">
                        <div class="ph-title">Dispatch Contact</div>
                    </div>
                    <button onclick="saveContact()" class="save-panel-btn">Save</button>
                </div>
                <div style="padding:16px 14px 40px;display:flex;flex-direction:column;gap:16px;">

                    <div class="info-banner">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>The mechanic uses this to contact you during a rescue. Make sure the number can receive calls
                            and
                            SMS.</span>
                    </div>

                    <div class="field-group">
                        <label class="field-label" for="pName">Your Name <span
                                style="color:var(--red);">*</span></label>
                        <input id="pName" class="mf-input" placeholder="e.g. Juan Dela Cruz">
                        <span class="field-hint">Your name as it appears to the mechanic</span>
                    </div>

                    <div class="field-group">
                        <label class="field-label" for="pContact">Mobile Number <span
                                style="color:var(--red);">*</span></label>
                        <input id="pContact" class="mf-input" type="tel" placeholder="e.g. 09171234567">
                        <span class="field-hint">Philippine mobile number â€” must be reachable for rescue
                            coordination</span>
                    </div>

                </div>
            </div>

            {{-- â•â• CHANGE PASSWORD SUB-PANEL â•â• --}}
            <div id="changePasswordPanel" class="panel" style="display:none;">
                <div class="ph">
                    <button class="ph-back" onclick="closeSubPanel('changePasswordPanel')">
                        <i class="fa-arrow-left fa-solid"></i>
                    </button>
                    <div>
                        <div class="ph-title">Change Password</div>
                    </div>
                </div>
                <div style="padding:16px 14px 40px;display:flex;flex-direction:column;gap:16px;">

                    <div class="info-banner">
                        <i class="fa-solid fa-lock"></i>
                        <span>For your security, enter your current password first. Your new password must be at least 6
                            characters.</span>
                    </div>

                    @if ($errors->has('current_password'))
                        <div
                            style="background:rgba(239,68,68,.1);border:1px solid var(--red);border-radius:var(--r2);padding:11px 13px;font-size:12px;color:var(--red);display:flex;gap:8px;align-items:flex-start;">
                            <i class="fa-solid fa-circle-exclamation" style="margin-top:1px;flex-shrink:0;"></i>
                            <span>{{ $errors->first('current_password') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('motorist.profile.password') }}"
                        style="display:flex;flex-direction:column;gap:16px;">
                        @csrf

                        <div class="field-group">
                            <label class="field-label" for="cur_pw">Current Password</label>
                            <input id="cur_pw" name="current_password" class="mf-input" type="password"
                                placeholder="Your existing password" required autocomplete="current-password">
                            <span class="field-hint">Enter the password you currently use to log in</span>
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="new_pw">New Password</label>
                            <input id="new_pw" name="password" class="mf-input" type="password"
                                placeholder="At least 6 characters" required autocomplete="new-password">
                            <span class="field-hint">Choose a strong password you haven't used before</span>
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="new_pw_conf">Confirm New Password</label>
                            <input id="new_pw_conf" name="password_confirmation" class="mf-input" type="password"
                                placeholder="Re-enter your new password" required autocomplete="new-password">
                            <span class="field-hint">Must match the new password above</span>
                        </div>

                        <button type="submit" class="btn-primary" style="margin-top:4px;">
                            <i class="fa-solid fa-lock"></i> Update Password
                        </button>
                    </form>
                </div>
            </div>

            {{-- â•â• REQUESTS PANEL â•â• --}}
            <div id="requestsPanel" class="panel" style="display:none;">

                <div class="ph">

                    <div>
                        <div class="ph-title">My Requests</div>
                        <div class="ph-subtitle">Recent rescue history from this device</div>
                    </div>
                </div>

                <div style="padding:12px 14px;" id="requestsList">
                    <div style="text-align:center;padding:48px 0;color:var(--text-3);font-size:13px;line-height:1.8;">
                        No requests yet.<br>
                        <span style="font-size:11px;color:var(--text-3);">Use the Map tab to request rescue.</span>
                    </div>
                </div>

            </div>

            {{-- TOAST --}}
            <div id="mfToast"></div>

        </div>{{-- #mapArea --}}

        {{-- BOTTOM NAV --}}
        <nav id="bottomNav">
            <button class="nav-btn active" id="navMap" onclick="showTab('map')">
                <span class="n-icon"><i class="fa-solid fa-map"></i></span>
                <span class="n-label">Map</span>
            </button>
            <button class="nav-btn" id="navRequests" onclick="showTab('requests')">
                <span class="n-icon"><i class="fa-solid fa-list"></i></span>
                <span class="n-label">Requests</span>
                <span class="nav-badge" id="reqBadge"></span>
            </button>
            <button class="nav-btn" id="navProfile" onclick="showTab('profile')">
                <span class="n-icon"><i class="fa-solid fa-user"></i></span>
                <span class="n-label">Profile</span>
            </button>
        </nav>

    </div>{{-- #mfApp --}}
@endsection

@section('scripts')
    @include('motorist.partials._scripts')
@endsection
