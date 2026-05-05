@extends('layouts.shop')

@section('content')

<div class="mb-8">
    <h2 class="heading-font text-3xl mb-2">Customer Reviews</h2>
    <p class="text-gray-400">Feedback from motorists who completed jobs with your shop.</p>
</div>

<!-- SUMMARY CARDS -->
<div class="grid md:grid-cols-3 gap-4 mb-8">

    <div class="bg-[#121214] border border-white/5 rounded-xl p-6">
        <p class="text-xs text-gray-500 font-bold uppercase">Average Rating</p>

        @if($totalReviews > 0)
            <p class="text-4xl font-black text-[#F7941D] mt-3">
                ⭐ {{ $averageRating }}
            </p>
            <p class="text-gray-400 text-sm mt-1">Out of 5 stars</p>
        @else
            <p class="text-3xl font-black text-gray-500 mt-3">0.0</p>
            <p class="text-gray-500 text-sm mt-1">No ratings yet</p>
        @endif
    </div>

    <div class="bg-[#121214] border border-white/5 rounded-xl p-6">
        <p class="text-xs text-gray-500 font-bold uppercase">Total Reviews</p>
        <p class="text-4xl font-black text-white mt-3">{{ $totalReviews }}</p>
        <p class="text-gray-400 text-sm mt-1">Customer feedback</p>
    </div>

    <div class="bg-[#121214] border border-white/5 rounded-xl p-6">
        <p class="text-xs text-gray-500 font-bold uppercase">Positive Feedback</p>

        @if($totalReviews > 0)
            <p class="text-4xl font-black text-green-400 mt-3">{{ $positivePercentage }}%</p>
            <p class="text-gray-400 text-sm mt-1">4 stars and above</p>
        @else
            <p class="text-4xl font-black text-gray-500 mt-3">0%</p>
            <p class="text-gray-500 text-sm mt-1">No feedback yet</p>
        @endif
    </div>

</div>

<!-- RATING BREAKDOWN -->
<div class="bg-[#121214] border border-white/5 rounded-xl p-6 mb-8">
    <h3 class="text-lg font-black text-white mb-5">Rating Breakdown</h3>

    @if($totalReviews > 0)
        @foreach($ratingBreakdown as $star => $count)
            @php
                $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
            @endphp

            <div class="flex items-center gap-3 mb-3">
                <span class="w-12 text-sm text-gray-300">{{ $star }} ⭐</span>

                <div class="flex-1 bg-white/10 h-3 rounded-full overflow-hidden">
                    <div
                        class="bg-[#F7941D] h-3 rounded-full"
                        style="width: {{ $percentage }}%">
                    </div>
                </div>

                <span class="text-xs text-gray-400 w-8 text-right">{{ $count }}</span>
            </div>
        @endforeach
    @else
        <div class="text-center py-8">
            <p class="text-gray-400">No rating data yet.</p>
            <p class="text-gray-500 text-sm mt-1">Rating breakdown will appear after customer reviews.</p>
        </div>
    @endif
</div>

<!-- REVIEWS LIST -->
<div class="space-y-4">

    @forelse($reviews as $review)

        <div class="bg-[#121214] border border-white/5 rounded-xl p-5 hover:border-white/10 transition">

            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3 mb-4">

                <div>
                    <h4 class="font-black text-white">
                        {{ $review->motorist_name ?? 'Guest Motorist' }}
                    </h4>

                    <p class="text-xs text-gray-500 mt-1">
                        {{ \Carbon\Carbon::parse($review->created_at)->format('M d, Y • h:i A') }}
                    </p>
                </div>

                <div class="text-[#F7941D] font-black text-lg">
                    ⭐ {{ $review->rating }}/5
                </div>

            </div>

            @if(!empty($review->issue_type))
                <div class="mb-3">
                    <span class="text-xs bg-white/5 border border-white/10 text-gray-300 px-3 py-1 rounded-full">
                        Job: {{ $review->issue_type }}
                    </span>
                </div>
            @endif

            @if(!empty($review->request_type))
                <div class="mb-3">
                    <span class="text-xs bg-blue-500/10 border border-blue-500/20 text-blue-300 px-3 py-1 rounded-full">
                        {{ strtoupper(str_replace('_', ' ', $review->request_type)) }}
                    </span>
                </div>
            @endif

            @if(!empty($review->comment))
                <p class="text-gray-300 text-sm leading-relaxed">
                    “{{ $review->comment }}”
                </p>
            @else
                <p class="text-gray-500 text-sm italic">
                    No written comment.
                </p>
            @endif

            <p class="text-xs text-gray-500 mt-4">
                {{ \Carbon\Carbon::parse($review->created_at)->diffForHumans() }}
            </p>

        </div>

    @empty

        <div class="text-center py-20 bg-[#121214] border border-white/5 rounded-xl">
            <div class="text-5xl mb-4">⭐</div>
            <p class="text-gray-400 text-lg font-bold">No reviews yet</p>
            <p class="text-gray-500 text-sm mt-1">
                New shops will show empty until motorists submit reviews.
            </p>
        </div>

    @endforelse

</div>

@endsection