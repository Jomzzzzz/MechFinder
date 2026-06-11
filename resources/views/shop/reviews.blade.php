@extends('layouts.shop')

@section('content')

<style>
    .reviews-container {
        padding: 0 0 80px 0;
    }
    .page-header {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 24px;
    }
    .page-title {
        font-size: 28px;
        font-weight: 800;
        color: #1d273b;
        margin: 0;
    }
    .page-subtitle {
        font-size: 14px;
        color: #667382;
        margin: 10px 0 0;
        max-width: 720px;
        line-height: 1.75;
    }
    .page-actions {
        display: flex;
        align-items: center;
    }
    .btn-outline {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 18px;
        border-radius: 999px;
        border: 1px solid #d9e2ec;
        background: #fff;
        color: #1d273b;
        text-decoration: none;
        font-size: 13px;
        transition: background 0.2s ease, transform 0.2s ease;
    }
    .btn-outline:hover {
        background: #f0f4f8;
        transform: translateY(-1px);
    }
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 22px;
    }
    .stat-card {
        background: #fff;
        border: 1px solid #e6e7eb;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(16, 24, 40, 0.05);
        padding: 22px;
        min-height: 130px;
    }
    .stat-card strong {
        display: block;
        font-size: 32px;
        color: #102a43;
        margin-bottom: 8px;
    }
    .stat-card .stat-label {
        font-size: 12px;
        font-weight: 600;
        color: #627d98;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin: 0;
    }
    .stat-stars {
        margin-top: 12px;
        display: inline-flex;
        gap: 4px;
        color: #206bc4;
        font-size: 15px;
    }
    .review-breakdown {
        display: grid;
        gap: 12px;
        padding: 20px;
        border-radius: 16px;
        background: #fff;
        border: 1px solid #e6e7eb;
        box-shadow: 0 1px 4px rgba(16, 24, 40, 0.04);
        margin-bottom: 24px;
    }
    .breakdown-row {
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 12px;
        align-items: center;
    }
    .breakdown-stars {
        display: inline-flex;
        gap: 2px;
        color: #206bc4;
        font-size: 13px;
        width: 92px;
    }
    .breakdown-bar {
        position: relative;
        height: 10px;
        border-radius: 999px;
        background: #f0f4f8;
        overflow: hidden;
    }
    .breakdown-fill {
        position: absolute;
        inset: 0;
        border-radius: 999px;
        background: linear-gradient(135deg, #206bc4 0%, #4094f7 100%);
    }
    .breakdown-count {
        font-size: 12px;
        color: #627d98;
        min-width: 28px;
        text-align: right;
    }
    .reviews-list {
        display: grid;
        gap: 16px;
    }
    .review-card {
        background: #fff;
        border: 1px solid #e6e7eb;
        border-radius: 16px;
        box-shadow: 0 1px 6px rgba(16, 24, 40, 0.05);
        padding: 20px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .review-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 24, 40, 0.08);
    }
    .review-header {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        margin-bottom: 14px;
    }
    .review-avatar {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: 800;
        color: #fff;
        background: linear-gradient(135deg, #206bc4 0%, #4094f7 100%);
        flex-shrink: 0;
    }
    .review-name {
        font-size: 15px;
        font-weight: 700;
        color: #102a43;
        margin: 0;
    }
    .review-meta {
        font-size: 12px;
        color: #627d98;
        margin: 6px 0 0;
    }
    .review-rating {
        display: inline-flex;
        gap: 3px;
        color: #206bc4;
        font-size: 14px;
        flex-shrink: 0;
    }
    .review-comment {
        font-size: 14px;
        color: #334e68;
        line-height: 1.8;
        margin: 0;
        word-break: break-word;
    }
    .review-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 16px;
    }
    .review-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #205081;
        background: rgba(32, 107, 196, 0.08);
        padding: 8px 12px;
        border-radius: 999px;
        border: 1px solid rgba(32, 107, 196, 0.18);
    }
    .empty-state {
        text-align: center;
        padding: 70px 24px;
        color: #627d98;
        background: #fff;
        border: 1px solid #e6e7eb;
        border-radius: 16px;
        box-shadow: 0 1px 6px rgba(16, 24, 40, 0.05);
    }
    .empty-state-icon {
        font-size: 46px;
        color: #a3b8d6;
        margin-bottom: 18px;
    }
    .empty-state-title {
        font-size: 20px;
        font-weight: 700;
        color: #102a43;
        margin: 0 0 10px;
    }
    .empty-state-text {
        font-size: 14px;
        max-width: 540px;
        margin: 0 auto;
        line-height: 1.75;
    }
</style>

<div class="reviews-container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Customer Reviews</h1>
            <p class="page-subtitle">A summary of feedback with our valued Customers.</p>
        </div>
       
    </div>

    <div class="stat-grid">
        <div class="stat-card">
            <strong>{{ number_format($averageRating, 1) }}/5</strong>
            <p class="stat-label">Average Rating</p>
            <div class="stat-stars">
                @for($s=1;$s<=5;$s++)
                    @if($s <= round($averageRating))
                        ★
                    @else
                        <span style="opacity:0.25;">★</span>
                    @endif
                @endfor
            </div>
        </div>
        <div class="stat-card">
            <strong>{{ $totalReviews }}</strong>
            <p class="stat-label">Total Reviews</p>
        </div>
        <div class="stat-card">
            <strong>{{ $positivePercentage }}%</strong>
            <p class="stat-label">Satisfied (4★+)</p>
        </div>
    </div>

    <div class="review-breakdown">
        @foreach($ratingBreakdown as $stars => $count)
            @php
                $ratio = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
            @endphp
            <div class="breakdown-row">
                <div class="breakdown-stars">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $stars)
                            ★
                        @else
                            <span style="opacity:0.25;">★</span>
                        @endif
                    @endfor
                </div>
                <div class="breakdown-bar">
                    <div class="breakdown-fill" style="width: {{ number_format($ratio, 2) }}%;"></div>
                </div>
                <div class="breakdown-count">{{ $count }}</div>
            </div>
        @endforeach
    </div>

    <div class="reviews-list">
        @forelse($reviews ?? [] as $review)
            @php
                $rName = $review->motorist_name ?? 'Guest Motorist';
                $services = !empty($review->services) ? explode(',', $review->services) : [];
                $relDate = \Carbon\Carbon::parse($review->created_at)->diffForHumans();
            @endphp
            <div class="review-card">
                <div class="review-header">
                    <div class="review-avatar">{{ strtoupper(substr($rName, 0, 1)) }}</div>
                    <div class="review-info">
                        <p class="review-name">{{ $rName }}</p>
                        <p class="review-meta">{{ $relDate }}</p>
                    </div>
                    <div class="review-rating">
                        @for($s=1;$s<=5;$s++)
                            @if($s <= $review->rating)
                                ★
                            @else
                                <span style="opacity:0.25;">★</span>
                            @endif
                        @endfor
                    </div>
                </div>
                @if(!empty($review->comment))
                    <p class="review-comment">{{ $review->comment }}</p>
                @endif
                @if(!empty($services))
                    <div class="review-tags">
                        @foreach($services as $service)
                            @if(trim($service))
                                <span class="review-tag"><i class="fa-solid fa-check"></i> {{ trim($service) }}</span>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-state-icon">⭐</div>
                <p class="empty-state-title">No reviews yet</p>
                <p class="empty-state-text">Motorist reviews will populate here as jobs complete, using the same minimalist dashboard style.</p>
            </div>
        @endforelse
    </div>
</div>

@endsection
