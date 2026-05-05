<div class="flex justify-between p-3 bg-white/5 rounded-lg">
    <span>{{ $title }}</span>

    @if($status == 'active')
        <span class="text-xs text-[#F7941D] font-bold">ACTIVE</span>
    @else
        <span class="text-xs text-green-500 font-bold">DONE</span>
    @endif
</div>