<div class="flex justify-between items-start p-4 bg-white/5 rounded-xl hover:border-[#F7941D]/30 border border-transparent transition">

    <div class="flex items-start gap-4 flex-1">
        <div class="w-12 h-12 bg-gradient-to-br from-[#F7941D] to-[#FF6B35] rounded-full flex items-center justify-center text-white font-bold shrink-0">{{ strtoupper(substr($issue, 0, 1)) }}</div>

        <div class="flex-1">
            <h4 class="text-sm font-semibold">{{ $issue }}</h4>
            <p class="text-xs text-gray-400 mt-1">👤 {{ $motorist_name }}</p>
            
            @if($motor_name || $motor_brand || $motor_color)
                <div class="text-xs text-gray-400 mt-2 space-y-0.5">
                    @if($motor_name)
                        <p>🏍️ Model: {{ $motor_name }}</p>
                    @endif
                    @if($motor_brand)
                        <p>🔧 Brand: {{ $motor_brand }}</p>
                    @endif
                    @if($motor_color)
                        <p>🎨 Color: {{ $motor_color }}</p>
                    @endif
                </div>
            @endif
            
            <p class="text-xs text-gray-500 mt-2">
                {{ \Carbon\Carbon::parse($created_at)->diffForHumans() }}
            </p>
        </div>
    </div>

    <div class="flex gap-3 shrink-0">
        <button onclick="acceptRequest({{ $id }})" class="bg-[#F7941D] text-black px-4 py-2 text-xs font-bold rounded hover:bg-[#FF6B35] transition">
            Accept
        </button>

        <button onclick="declineRequest({{ $id }})" class="border border-white/10 px-4 py-2 text-xs text-gray-400 rounded hover:bg-white/5 transition">
            Decline
        </button>
    </div>

</div>

<script>
function getCsrfToken() {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    return token || document.body.getAttribute('data-csrf-token') || '';
}

async function acceptRequest(id) {
    if (!confirm('Accept this request?')) return;
    
    try {
        const response = await fetch(`/shop/accept/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Content-Type': 'application/json'
            }
        });
        
        if (response.ok) {
            location.reload();
        } else {
            alert('Failed to accept request');
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

async function declineRequest(id) {
    if (!confirm('Decline this request?')) return;
    
    try {
        const response = await fetch(`/shop/decline/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Content-Type': 'application/json'
            }
        });
        
        if (response.ok) {
            location.reload();
        } else {
            alert('Failed to decline request');
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}
</script>