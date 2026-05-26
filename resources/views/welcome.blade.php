<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MechFinder | Fast Motorcycle Repair Dispatch</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'DM Sans', sans-serif; background: #FAFAF8; color: #0D0D0D; }
        h1, h2, h3 { font-family: 'Syne', sans-serif; }
        .orange { color: #F7941D; }
        .bg-orange { background: #F7941D; }
        .btn { transition: transform .15s, background .2s; }
        .btn:hover { transform: translateY(-1px); }
        .card { transition: transform .25s, box-shadow .25s; }
        .card:hover { transform: translateY(-4px); box-shadow: 0 20px 48px rgba(0,0,0,.07); }
        .hero-glow { background: radial-gradient(ellipse at 60% 50%, rgba(247,148,29,.12) 0%, transparent 70%); }
    </style>
</head>
<body class="overflow-x-hidden antialiased">

<!-- NAV -->
<nav class="sticky top-0 z-50 border-b border-[#E8E6E1] bg-[#FAFAF8]/85 backdrop-blur-xl">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 h-[60px] sm:h-[68px] flex items-center justify-between">
        <a href="/" class="flex items-center gap-2.5">
            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-[#0D0D0D] flex items-center justify-center text-lg">⚙️</div>
            <span class="font-bold text-base sm:text-lg tracking-tight" style="font-family:Syne,sans-serif">MechFinder</span>
        </a>
        <div class="hidden md:flex gap-6 lg:gap-8 text-xs sm:text-sm text-[#6B6963]">
            <a href="#features" class="hover:text-black transition">Features</a>
            <a href="{{ route('signup') }}" class="hover:text-black transition">For Motorists</a>
            <a href="{{ route('signup.shop') }}" class="hover:text-black transition">For Shops</a>
        </div>
        <a href="{{ route('login') }}" class="btn bg-orange text-white text-xs sm:text-sm font-medium px-4 sm:px-5 py-2 sm:py-2.5 rounded-full hover:bg-[#d97a0f]">Sign in &rarr;</a>
    </div>
</nav>

<!-- HERO -->
<section class="max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-12 md:py-16 lg:py-20 grid lg:grid-cols-2 gap-8 sm:gap-12 lg:gap-16 items-center min-h-[calc(100vh-60px)] sm:min-h-[calc(100vh-68px)]">

    <!-- LEFT CONTENT -->
    <div class="flex flex-col justify-center">
        <!-- Badge -->
        <div class="inline-flex items-center gap-2 bg-[#F5F4F1] border border-[#E8E6E1] rounded-full px-3 sm:px-3.5 py-1 sm:py-1.5 text-[10px] sm:text-xs font-medium text-[#6B6963] uppercase tracking-widest mb-4 sm:mb-6 w-fit">
            <span class="w-1.5 h-1.5 rounded-full bg-[#F7941D]"></span>
            <span class="hidden sm:inline">Motorcycle Repair Dispatch</span>
            <span class="sm:hidden">Repair Dispatch</span>
        </div>

        <!-- Headline -->
        <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold leading-[1.1] sm:leading-[1.08] lg:leading-[1.05] tracking-tight mb-4 sm:mb-6">
            Fast repair help,<br><span class="orange">anytime</span> you need it.
        </h1>

        <!-- Description -->
        <p class="text-base sm:text-lg text-[#6B6963] font-light leading-relaxed max-w-md mb-6 sm:mb-10">
            Connect instantly with nearby motorcycle repair shops. Emergency dispatch, live chat, and real-time tracking — all in one place.
        </p>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row flex-wrap gap-2.5 sm:gap-3 mb-8 sm:mb-10">
            <a href="{{ route('signup') }}" class="btn bg-orange text-white font-medium px-5 sm:px-7 py-3 sm:py-3.5 rounded-full hover:bg-[#d97a0f] text-center text-sm sm:text-base">I'm a Motorist &rarr;</a>
            <a href="{{ route('signup.shop') }}" class="btn border-[1.5px] border-[#E8E6E1] text-[#0D0D0D] font-medium px-5 sm:px-7 py-3 sm:py-3.5 rounded-full hover:bg-[#F5F4F1] hover:border-[#aaa] transition text-center text-sm sm:text-base">I'm a Shop Owner</a>
        </div>

        <!-- Social Proof -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4">
            <div class="flex">
                @foreach(['MR','JL','AP'] as $i)
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-[#E8E6E1] border-2 border-[#FAFAF8] -ml-2 first:ml-0 flex items-center justify-center text-[9px] sm:text-[10px] font-semibold text-[#6B6963]">{{ $i }}</span>
                @endforeach
            </div>
            <p class="text-xs sm:text-sm text-[#6B6963]"><strong class="text-[#0D0D0D] font-semibold">5,000+</strong> motorists already on MechFinder</p>
        </div>
    </div>

    <!-- RIGHT IMAGE SECTION -->
    <div class="hidden lg:flex justify-center relative h-[400px] md:h-[500px] lg:h-[600px]">
        <!-- Glow Background -->
        <div class="absolute inset-0 hero-glow rounded-3xl"></div>
        
        <!-- Main Image -->
        <img src="{{ asset('images/landingpage-image.jpg') }}" alt="Motorcycle Rider"
             class="w-full max-w-sm lg:max-w-md object-contain relative z-[1]"
             style="filter:drop-shadow(0 30px 60px rgba(0,0,0,.15))">
        
        <!-- Top Stat Card -->
        <div class="absolute top-[8%] -right-2 lg:-right-4 bg-white border border-[#E8E6E1] rounded-xl lg:rounded-2xl px-3 lg:px-4 py-2 lg:py-3 shadow-lg z-10 w-max">
            <p class="text-[8px] lg:text-[10px] uppercase tracking-widest text-[#6B6963] font-medium mb-0.5 lg:mb-1">Avg. Response</p>
            <p class="text-xl lg:text-2xl font-extrabold tracking-tight" style="font-family:Syne,sans-serif">4 min</p>
            <p class="text-[9px] lg:text-[11px] text-[#6B6963]">Shop dispatched near you</p>
        </div>
        
        <!-- Bottom Stat Card -->
        <div class="absolute bottom-[12%] -left-2 lg:-left-4 bg-white border border-[#E8E6E1] rounded-xl lg:rounded-2xl px-3 lg:px-4 py-2 lg:py-3 shadow-lg z-10 w-max">
            <p class="text-[8px] lg:text-[10px] uppercase tracking-widest text-[#6B6963] font-medium mb-0.5 lg:mb-1">Active Shops</p>
            <p class="text-xl lg:text-2xl font-extrabold tracking-tight" style="font-family:Syne,sans-serif">150+</p>
            <p class="text-[9px] lg:text-[11px] text-[#6B6963]">In Olongapo City</p>
        </div>
    </div>

    <!-- MOBILE IMAGE SECTION (Below text on mobile) -->
    <div class="lg:hidden flex justify-center relative h-[300px] sm:h-[400px] md:h-[450px] mt-6 sm:mt-8">
        <!-- Glow Background -->
        <div class="absolute inset-0 hero-glow rounded-3xl"></div>
        
        <!-- Main Image -->
        <img src="{{ asset('images/landingpage-image.jpg') }}" alt="Motorcycle Rider"
             class="w-full max-w-xs object-contain relative z-[1]"
             style="filter:drop-shadow(0 20px 40px rgba(0,0,0,.12))">
        
        <!-- Top Stat Card - Mobile -->
        <div class="absolute top-[5%] -right-1 sm:right-2 bg-white border border-[#E8E6E1] rounded-lg px-2.5 sm:px-3 py-2 shadow-md z-10 w-max">
            <p class="text-[7px] sm:text-[8px] uppercase tracking-widest text-[#6B6963] font-medium mb-0.5">Avg. Response</p>
            <p class="text-lg sm:text-xl font-extrabold tracking-tight" style="font-family:Syne,sans-serif">4 min</p>
            <p class="text-[7px] sm:text-[8px] text-[#6B6963]">Shop dispatched</p>
        </div>
        
        <!-- Bottom Stat Card - Mobile -->
        <div class="absolute bottom-[8%] -left-1 sm:left-2 bg-white border border-[#E8E6E1] rounded-lg px-2.5 sm:px-3 py-2 shadow-md z-10 w-max">
            <p class="text-[7px] sm:text-[8px] uppercase tracking-widest text-[#6B6963] font-medium mb-0.5">Active Shops</p>
            <p class="text-lg sm:text-xl font-extrabold tracking-tight" style="font-family:Syne,sans-serif">150+</p>
            <p class="text-[7px] sm:text-[8px] text-[#6B6963]">Olongapo City</p>
        </div>
    </div>
</section>

<!-- PROOF STRIP -->
<div class="bg-[#F5F4F1] border-y border-[#E8E6E1] py-4 sm:py-5 px-4 sm:px-6">
    <div class="max-w-6xl mx-auto flex flex-wrap justify-center gap-x-6 sm:gap-x-10 gap-y-2.5 sm:gap-y-3">
        @foreach(['Live Mechanic Tracking','Verified Repair Shops','Olongapo City Coverage'] as $item)
        <span class="flex items-center gap-2 text-xs sm:text-sm text-[#6B6963] font-medium">
            <svg class="text-[#F7941D] w-3.5 h-3.5 sm:w-[15px] sm:h-[15px]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
            {{ $item }}
        </span>
        @endforeach
    </div>
</div>

<!-- FEATURES -->
<section id="features" class="max-w-6xl mx-auto px-4 sm:px-6 py-12 sm:py-16 md:py-24">
    <div class="mb-10 sm:mb-14">
        <p class="flex items-center gap-2.5 text-xs font-semibold text-[#F7941D] uppercase tracking-widest mb-3 sm:mb-4">
            <span class="w-4 sm:w-6 h-0.5 bg-[#F7941D]"></span>How it works
        </p>
        <h2 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight leading-tight mb-3 sm:mb-4">Built for motorists<br>and shops alike</h2>
        <p class="text-sm sm:text-base text-[#6B6963] font-light leading-relaxed max-w-md">One unified platform to make motorcycle repair faster, simpler, and more reliable for everyone.</p>
    </div>

    <div class="grid md:grid-cols-2 gap-4 sm:gap-6 mb-6">
        @foreach([
            ['icon'=>'🏍','title'=>'For Motorists','sub'=>'Get back on the road fast','items'=>[
                ['📍','Find Nearby Shops','Discover verified repair shops closest to your exact location in seconds.'],
                ['🚨','Emergency Dispatch','Tap once to request urgent roadside assistance wherever you are.'],
                ['💬','Live Chat','Describe your problem and get real-time guidance before arrival.'],
                ['⭐','Ratings & Reviews','Rate services, read feedback, and make informed choices every time.'],
            ]],
            ['icon'=>'🛠','title'=>'For Shop Owners','sub'=>'Grow your customer base','items'=>[
                ['📩','Receive Requests','Get notified instantly for jobs near your shop — never miss a customer.'],
                ['🟢','Real-time Availability','Toggle open/closed status and manage your capacity on the fly.'],
                ['📊','Analytics Dashboard','Monitor jobs completed, average ratings, and revenue over time.'],
                ['👨‍🔧','Mechanic Management','Assign and dispatch your team to customer locations with one tap.'],
            ]],
        ] as $card)
        <div class="card bg-white border border-[#E8E6E1] rounded-xl sm:rounded-2xl p-5 sm:p-8">
            <div class="flex items-start gap-3 sm:gap-4 pb-4 sm:pb-6 mb-4 sm:mb-6 border-b border-[#F5F4F1]">
                <div class="w-11 h-11 sm:w-13 sm:h-13 bg-[#F5F4F1] rounded-lg sm:rounded-xl flex items-center justify-center text-xl sm:text-2xl flex-shrink-0">{{ $card['icon'] }}</div>
                <div>
                    <h3 class="text-base sm:text-xl font-bold">{{ $card['title'] }}</h3>
                    <p class="text-xs sm:text-sm text-[#6B6963]">{{ $card['sub'] }}</p>
                </div>
            </div>
            <ul class="space-y-3 sm:space-y-5">
                @foreach($card['items'] as [$icon, $title, $desc])
                <li class="flex gap-2 sm:gap-3">
                    <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-orange/10 flex items-center justify-center text-xs sm:text-sm flex-shrink-0 mt-0.5">{{ $icon }}</div>
                    <div>
                        <h4 class="text-xs sm:text-sm font-semibold mb-0.5">{{ $title }}</h4>
                        <p class="text-[11px] sm:text-xs text-[#6B6963] leading-relaxed">{{ $desc }}</p>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
        @endforeach
    </div>

    <!-- STATS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-[#E8E6E1] border border-[#E8E6E1] rounded-xl sm:rounded-2xl overflow-hidden">
        @foreach([['150+','Repair Shops'],['5K+','Motorists Served'],['24/7','Always Available']] as [$num,$label])
        <div class="bg-white py-6 sm:py-10 px-4 sm:px-6 text-center">
            <p class="text-4xl sm:text-5xl font-extrabold orange tracking-tight leading-none mb-2" style="font-family:Syne,sans-serif">{{ $num }}</p>
            <p class="text-xs sm:text-sm text-[#6B6963]">{{ $label }}</p>
        </div>
        @endforeach
    </div>
</section>

<!-- CTA -->
<div class="bg-[#0D0D0D] py-12 sm:py-16 md:py-24 px-4 sm:px-6">
    <div class="max-w-2xl mx-auto text-center">
        <h2 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-white tracking-tight leading-tight mb-4 sm:mb-5">
            Ready to get<br><span class="orange">back on the road?</span>
        </h2>
        <p class="text-sm sm:text-base text-white/50 font-light leading-relaxed mb-8 sm:mb-10">Join MechFinder today and experience seamless motorcycle repair dispatch built for Olongapo City.</p>
        <div class="flex flex-col sm:flex-row flex-wrap justify-center gap-2.5 sm:gap-3">
            <a href="{{ route('signup') }}" class="btn bg-white text-[#0D0D0D] font-medium px-6 sm:px-7 py-3 sm:py-3.5 rounded-full hover:bg-[#F5F4F1] transition text-sm sm:text-base">Sign Up as Motorist &rarr;</a>
            <a href="{{ route('signup.shop') }}" class="btn border-[1.5px] border-white/20 text-white/70 font-medium px-6 sm:px-7 py-3 sm:py-3.5 rounded-full hover:border-white/50 hover:text-white transition text-sm sm:text-base">Register Your Shop</a>
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer class="bg-[#F5F4F1] border-t border-[#E8E6E1] py-4 sm:py-6 px-4 sm:px-6">
    <div class="max-w-6xl mx-auto flex flex-col sm:flex-row flex-wrap items-center justify-between gap-3 sm:gap-4">
        <a href="/" class="flex items-center gap-2.5">
            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-[#0D0D0D] flex items-center justify-center text-xs sm:text-sm">⚙️</div>
            <span class="font-bold tracking-tight text-sm sm:text-base" style="font-family:Syne,sans-serif">MechFinder</span>
        </a>
        <p class="text-xs sm:text-sm text-[#6B6963] text-center sm:text-left">© 2026 MechFinder. Fast motorcycle repair dispatch for Olongapo City.</p>
    </div>
</footer>

</body>
</html>
