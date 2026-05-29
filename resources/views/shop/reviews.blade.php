@extends('layouts.shop')

@section('content')

<div class="page-header">
    <div class="page-pretitle">Shop</div>
    <h1 class="page-title">Reviews</h1>
</div>

{{-- Summary Cards --}}
<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(200px,1fr)); gap:16px; margin-bottom:24px;">
    <div class="t-card" style="padding:20px 24px;">
        <p style="font-size:11px; font-weight:600; color:#667382; text-transform:uppercase; margin:0 0 8px;">Average Rating</p>
        <div style="display:flex; align-items:baseline; gap:6px;">
            <span style="font-size:32px; font-weight:700; color:#1d273b;">{{ number_format($averageRating, 1) }}</span>
            <span style="font-size:14px; color:#f76707; font-weight:600;">/ 5.0</span>
        </div>
        <div style="margin-top:6px; font-size:18px; color:#f76707;">
            @for($s=1;$s<=5;$s++)
                @if($s <= round($averageRating))★@else☆@endif
            @endfor
        </div>
    </div>
    <div class="t-card" style="padding:20px 24px;">
        <p style="font-size:11px; font-weight:600; color:#667382; text-transform:uppercase; margin:0 0 8px;">Total Reviews</p>
        <p style="font-size:32px; font-weight:700; color:#1d273b; margin:0;">{{ $totalReviews }}</p>
        <p style="font-size:13px; color:#a0a8b1; margin:6px 0 0;">motorist reviews</p>
    </div>
    <div class="t-card" style="padding:20px 24px;">
        <p style="font-size:11px; font-weight:600; color:#667382; text-transform:uppercase; margin:0 0 8px;">Positive Reviews</p>
        <p style="font-size:32px; font-weight:700; color:#2fb344; margin:0;">{{ $positivePercentage }}%</p>
        <p style="font-size:13px; color:#a0a8b1; margin:6px 0 0;">4★ and 5★ combined</p>
    </div>
</div>

<div style="display:grid; grid-template-columns:260px 1fr; gap:20px; align-items:start;">

    {{-- Rating Breakdown --}}
    <div class="t-card" style="padding:20px 24px;">
        <h3 style="font-size:14px; font-weight:700; color:#1d273b; margin:0 0 16px;">Breakdown</h3>
        <div style="display:flex; flex-direction:column; gap:10px;">
            @for($star=5; $star>=1; $star--)
                @php $cnt = $ratingBreakdown[$star] ?? 0; $pct = $totalReviews > 0 ? round(($cnt/$totalReviews)*100) : 0; @endphp
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="font-size:13px; color:#f76707; width:24px; font-weight:600;">{{ $star }}★</span>
                    <div style="flex:1; background:#f0f2f5; border-radius:20px; height:8px; overflow:hidden;">
                        <div style="background:#f76707; width:{{ $pct }}%; height:100%; border-radius:20px; transition:width .3s;"></div>
                    </div>
                    <span style="font-size:12px; color:#667382; width:28px; text-align:right;">{{ $cnt }}</span>
                </div>
            @endfor
        </div>
    </div>

    {{-- Reviews List --}}
    <div style="display:flex; flex-direction:column; gap:12px;">
        @forelse($reviews ?? [] as $review)
        @php
            $rName = $review->motorist->name ?? $review->guest_name ?? 'Anonymous';
        @endphp
        <div class="t-card" style="padding:18px 20px;">
            <div style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:8px; margin-bottom:10px;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="width:36px; height:36px; background:#206bc4; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <span style="font-size:13px; font-weight:700; color:#fff;">{{ strtoupper(substr($rName, 0, 1)) }}</span>
                    </div>
                    <div>
                        <p style="font-size:14px; font-weight:600; color:#1d273b; margin:0;">{{ $rName }}</p>
                        <p style="font-size:11px; color:#a0a8b1; margin:2px 0 0;">{{ \Carbon\Carbon::parse($review->created_at)->format('M d, Y') }}</p>
                    </div>
                </div>
                <div style="display:flex; gap:2px; font-size:16px; color:#f76707;">
                    @for($s=1;$s<=5;$s++)@if($s<=$review->rating)★@else<span style="color:#e6e7eb;">★</span>@endif@endfor
                </div>
            </div>
            @if(!empty($review->comment))
            <p style="font-size:13px; color:#1d273b; line-height:1.6; margin:0;">{{ $review->comment }}</p>
            @endif
        </div>
        @empty
        <div class="t-card" style="padding:56px 24px; text-align:center;">
            <i class="fas fa-star" style="font-size:32px; color:#c8ccd0; margin-bottom:12px; display:block;"></i>
            <p style="font-size:15px; font-weight:600; color:#667382; margin:0 0 4px;">No reviews yet</p>
            <p style="font-size:13px; color:#a0a8b1; margin:0;">Reviews will appear here after motorists complete service.</p>
        </div>
        @endforelse
    </div>
</div>

@endsection
