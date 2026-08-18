@props(['icon' => 'grid', 'label' => '', 'value' => 0, 'color' => 'primary', 'index' => 0, 'suffix' => ''])
@php
    $themes = [
        'primary' => ['bg' => 'bg-primary-50', 'text' => 'text-primary-600'],
        'mint'    => ['bg' => 'bg-green-50', 'text' => 'text-green-600'],
        'accent'  => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600'],
        'danger'  => ['bg' => 'bg-red-50', 'text' => 'text-danger'],
    ];
    $t = $themes[$color] ?? $themes['primary'];
@endphp
<div class="surface-card surface-card-hover rounded-lg p-4 sm:p-5 animate-fade-up" style="--stagger: {{ $index }}">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg {{ $t['bg'] }} {{ $t['text'] }} flex items-center justify-center shrink-0">
            {!! \App\Support\Icons::svg($icon, 'w-5 h-5') !!}
        </div>
        <div class="min-w-0">
            <p class="text-[12.5px] text-ink-soft font-medium truncate">{{ $label }}</p>
            <p class="font-display font-bold text-[22px] text-ink leading-tight"
               x-data="{ display: 0, target: {{ (int) $value }} }"
               x-init="let start = null; const step = (ts) => { if(!start) start = ts; const p = Math.min((ts - start) / 700, 1); display = Math.floor(p * target); if(p < 1) requestAnimationFrame(step); else display = target; }; requestAnimationFrame(step);"
               x-text="display.toLocaleString('id-ID')">
            </p>
        </div>
    </div>
</div>