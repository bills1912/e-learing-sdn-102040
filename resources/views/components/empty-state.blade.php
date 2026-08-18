@props(['icon' => 'grid', 'title' => 'Belum ada data', 'subtitle' => ''])
<div class="flex flex-col items-center justify-center text-center py-14 px-6">
    <div class="w-12 h-12 rounded-lg bg-gray-100 text-gray-400 flex items-center justify-center mb-3">
        {!! \App\Support\Icons::svg($icon, 'w-6 h-6') !!}
    </div>
    <p class="font-display font-semibold text-ink text-[14.5px]">{{ $title }}</p>
    @if($subtitle)
        <p class="text-[13px] text-ink-soft mt-1 max-w-xs">{{ $subtitle }}</p>
    @endif
</div>