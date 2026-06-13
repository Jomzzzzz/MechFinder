@extends('layouts.admin')

@section('content')
    <div class="mb-8 text-[#F4B942] text-2xl heading-font">Admin Dashboard</div>

    <div class="gap-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 mb-10">
        <div class="bg-[#111113] p-5 border border-[#1E1E21] rounded-xl h-full">
            <p class="mb-1 text-[#888] text-xs">Total Shops</p>
            <p class="font-bold text-[#F4B942] text-3xl">{{ $totalShops }}</p>
        </div>
        <div class="bg-[#111113] p-5 border border-[#1E1E21] rounded-xl h-full">
            <p class="mb-1 text-[#888] text-xs">Total Mechanics</p>
            <p class="font-bold text-white text-3xl">{{ $totalMechanics }}</p>
        </div>
        <div class="bg-[#111113] p-5 border border-[#1E1E21] rounded-xl h-full">
            <p class="mb-1 text-[#888] text-xs">Motorists With Requests</p>
            <p class="font-bold text-white text-3xl">{{ $totalMotoristRequesters }}</p>
        </div>
        <div class="bg-[#111113] p-5 border border-[#1E1E21] rounded-xl h-full">
            <p class="mb-1 text-[#888] text-xs">Dispatch Requests</p>
            <p class="font-bold text-white text-3xl">{{ $totalRequests }}</p>
        </div>
        <div class="bg-[#111113] p-5 border border-[#1E1E21] rounded-xl h-full">
            <p class="mb-1 text-[#888] text-xs">Reviews</p>
            <p class="font-bold text-white text-3xl">{{ $totalReviews }}</p>
        </div>
    </div>

    <div class="grid md:grid-cols-4 gap-6">
        @if(!($shopId ?? false))
            <div class="md:col-span-4">
                <div class="mb-4 text-[#EEEEEE] text-lg heading-font">Shops</div>
                <div class="bg-[#111113] border border-[#1E1E21] rounded-xl">
                    <div class="overflow-y-auto" style="max-height:60vh;">
                        <ul class="divide-y divide-[#1E1E21]">
                            @foreach($shops as $shop)
                                <li class="px-4 py-3 flex items-center justify-between">
                                    <div>
                                        <a href="{{ route('admin.shops.edit', $shop->id) }}" class="text-sm text-[#EEE] hover:underline">{{ $shop->shop_name }}</a>
                                        <div class="text-xs text-[#888]">{{ $shop->owner_name ?? 'No owner' }}</div>
                                    </div>
                                    <div class="ml-4">
                                        <a href="{{ route('admin.dashboard', ['shop' => $shop->id]) }}" class="show-records inline-flex items-center rounded-lg bg-[#F4B942] px-3 py-1 text-sm font-semibold text-[#0A0A0B] hover:bg-[#e6b12a]" data-shop-id="{{ $shop->id }}">Show Records</a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @else
            <div class="md:col-span-4">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-[#EEEEEE] text-lg heading-font">Recent Requests for selected shop</div>
                    <a href="{{ route('admin.dashboard') }}" id="dashboard-back" class="inline-flex items-center rounded-lg border border-[#1E1E21] bg-[#0A0B0D] px-3 py-1 text-sm font-semibold text-[#EEE] hover:border-[#F4B942] hover:text-[#F4B942]">Back</a>
                </div>

                <div class="bg-[#111113] border border-[#1E1E21] rounded-xl">
                    <div class="overflow-y-auto" style="max-height:60vh;">
                        <table class="w-full text-sm">
                            <thead class="border-[#1E1E21] border-b">
                                <tr class="text-[#888] text-xs uppercase">
                                    <th class="px-4 py-3 text-left">ID</th>
                                    <th class="px-4 py-3 text-left">Shop</th>
                                    <th class="px-4 py-3 text-left">Motorist</th>
                                    <th class="px-4 py-3 text-left">Issue</th>
                                    <th class="px-4 py-3 text-left">Status</th>
                                    <th class="px-4 py-3 text-left">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentRequests as $req)
                                    <tr class="hover:bg-[#1A1A1D] border-[#1E1E21] border-b">
                                        <td class="px-4 py-3 text-[#888]">#{{ $req->id }}</td>
                                        <td class="px-4 py-3">{{ $req->shop_name ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ $req->motorist_name }}</td>
                                        <td class="px-4 py-3">{{ $req->issue_type ?? '-' }}</td>
                                        <td class="px-4 py-3">
                                            @php
                                                switch ($req->status) {
                                                    case 'pending':
                                                        $bgColor = '#78350f';
                                                        $textColor = '#fbbf24';
                                                        break;
                                                    case 'accepted':
                                                        $bgColor = '#1e3a8a';
                                                        $textColor = '#93c5fd';
                                                        break;
                                                    case 'completed':
                                                        $bgColor = '#14532d';
                                                        $textColor = '#86efac';
                                                        break;
                                                    case 'declined':
                                                        $bgColor = '#7f1d1d';
                                                        $textColor = '#fecaca';
                                                        break;
                                                    default:
                                                        $bgColor = '#111827';
                                                        $textColor = '#9ca3af';
                                                }
                                            @endphp
                                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold" style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
                                                {{ ucfirst($req->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-[#888]">{{ \Carbon\Carbon::parse($req->created_at)->format('M d, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-6 text-[#666] text-center">No requests found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="grid md:grid-cols-2 gap-6 mt-10 mb-10">
        <div class="bg-[#111113] border border-[#1E1E21] rounded-xl p-6" style="box-shadow: 0 6px 18px rgba(0,0,0,0.55);">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-[#888] text-xs uppercase tracking-[0.2em]">Monthly Requests</p>
                    <h2 class="text-[#EEE] text-lg font-semibold">Dispatch activity</h2>
                </div>
                <span class="text-[#AAA] text-xs">Last 6 months</span>
            </div>
            <div class="w-full" style="height:320px;">
                <canvas id="requestsChart" class="w-full h-full"></canvas>
            </div>
        </div>
        <div class="bg-[#111113] border border-[#1E1E21] rounded-xl p-6" style="box-shadow: 0 6px 18px rgba(0,0,0,0.55);">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-[#888] text-xs uppercase tracking-[0.2em]">New Shops</p>
                    <h2 class="text-[#EEE] text-lg font-semibold">Shop onboarding</h2>
                </div>
                <span class="text-[#AAA] text-xs">Last 6 months</span>
            </div>
            <div class="w-full" style="height:320px;">
                <canvas id="shopsChart" class="w-full h-full"></canvas>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const months = @json($months ?? []);
            const requestsData = @json($requestsMonthlyCounts ?? []);
            const shopsData = @json($shopsMonthlyCounts ?? []);

            const requestsCanvas = document.getElementById('requestsChart');
            if (requestsCanvas && months.length) {
                new Chart(requestsCanvas, {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: [{
                            label: 'Dispatch Requests',
                            data: requestsData,
                            borderColor: '#F4B942',
                            backgroundColor: 'rgba(244, 185, 66, 0.2)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 3,
                            pointBackgroundColor: '#F4B942',
                            borderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        scales: {
                            x: {
                                ticks: { color: '#AAA', font: { size: 12 } },
                                grid: { color: 'rgba(255,255,255,0.03)' }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { color: '#AAA', font: { size: 12 } },
                                grid: { color: 'rgba(255,255,255,0.03)' }
                            }
                        },
                        plugins: {
                            legend: { labels: { color: '#EEE' } },
                            tooltip: {
                                backgroundColor: '#0B0C10',
                                titleColor: '#FFF',
                                bodyColor: '#DDD',
                                borderColor: 'rgba(255,255,255,0.06)',
                                borderWidth: 1,
                                padding: 8
                            }
                        }
                    }
                });
            }

            const shopsCanvas = document.getElementById('shopsChart');
            if (shopsCanvas && months.length) {
                new Chart(shopsCanvas, {
                    type: 'bar',
                    data: {
                        labels: months,
                        datasets: [{
                            label: 'New Shops',
                            data: shopsData,
                            backgroundColor: 'rgba(59,130,246,0.7)',
                            borderColor: '#3B82F6',
                            borderWidth: 1,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        scales: {
                            x: {
                                ticks: { color: '#AAA', font: { size: 12 } },
                                grid: { color: 'rgba(255,255,255,0.03)' }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { color: '#AAA', font: { size: 12 } },
                                grid: { color: 'rgba(255,255,255,0.03)' }
                            }
                        },
                        plugins: {
                            legend: { labels: { color: '#EEE' } },
                            tooltip: {
                                backgroundColor: '#0B0C10',
                                titleColor: '#FFF',
                                bodyColor: '#DDD',
                                borderColor: 'rgba(255,255,255,0.06)',
                                borderWidth: 1,
                                padding: 8
                            }
                        }
                    }
                });
            }
        });
    </script>
    <script>
        // persist selected shop for dashboard so reload/Echo keeps the selected shop
        document.addEventListener('DOMContentLoaded', function () {
            try {
                // store when clicking Show Records
                document.querySelectorAll('.show-records').forEach(function(el) {
                    el.addEventListener('click', function() {
                        var id = this.dataset.shopId;
                        try { sessionStorage.setItem('admin_dashboard_shop', id); } catch(e){}
                    });
                });

                // clear when clicking Back
                var back = document.getElementById('dashboard-back');
                if (back) {
                    back.addEventListener('click', function() {
                        try { sessionStorage.removeItem('admin_dashboard_shop'); } catch(e){}
                    });
                }

                // restore if server didn't provide shop but we have one in storage
                var serverShop = '{{ $shopId ?? '' }}';
                if (!serverShop) {
                    var stored = null;
                    try { stored = sessionStorage.getItem('admin_dashboard_shop'); } catch(e){}
                    if (stored) {
                        var url = new URL(window.location.href);
                        url.searchParams.set('shop', stored);
                        // avoid infinite loop: only redirect if different
                        if (window.location.search.indexOf('shop=') === -1) {
                            window.location.href = url.toString();
                        }
                    }
                }
            } catch(e) {}
        });
    </script>
@endpush
