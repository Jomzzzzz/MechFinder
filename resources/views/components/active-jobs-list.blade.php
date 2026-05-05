@if($jobs->count())
    @foreach($jobs as $job)
        <div class="p-4 rounded-xl bg-white/5 border border-white/10">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="font-semibold text-white">
                        {{ $job->motorist_name ?? 'Unknown Motorist' }}
                    </p>
                    <p class="text-sm text-gray-300 mt-1">
                        {{ $job->issue_type ?? 'No issue specified' }}
                    </p>
                    <p class="text-xs text-gray-500 mt-2">
                        {{ ucfirst(str_replace('_', ' ', $job->status ?? 'unknown')) }}
                    </p>
                </div>
                <span class="text-xs px-2 py-1 rounded-full {{ $colors }}">
    {{ strtoupper(str_replace('_', ' ', $status)) }}
</span>
            </div>
        </div>
    @endforeach
@else
    <div class="p-4 rounded-xl bg-white/5 border border-white/10 text-sm text-gray-400">
        No active jobs yet.
    </div>
@endif