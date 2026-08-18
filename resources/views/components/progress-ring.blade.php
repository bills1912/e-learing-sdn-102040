@props(['value' => 0, 'size' => 84, 'stroke' => 8, 'color' => 'stroke-primary-500', 'label' => null])
@php
    $radius = ($size - $stroke) / 2;
    $circumference = 2 * M_PI * $radius;
    $value = max(0, min(100, (float) $value));
@endphp
<div class="relative inline-flex items-center justify-center" style="width:{{ $size }}px;height:{{ $size }}px"
     x-data="{ offset: {{ $circumference }} }"
     x-init="setTimeout(() => offset = {{ $circumference }} - ({{ $value }} / 100) * {{ $circumference }}, 100)">
    <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 {{ $size }} {{ $size }}" class="-rotate-90">
        <circle cx="{{ $size / 2 }}" cy="{{ $size / 2 }}" r="{{ $radius }}" fill="none" stroke-width="{{ $stroke }}" class="progress-ring-track"/>
        <circle cx="{{ $size / 2 }}" cy="{{ $size / 2 }}" r="{{ $radius }}" fill="none" stroke-width="{{ $stroke }}"
                class="progress-ring-value {{ $color }}"
                stroke-dasharray="{{ $circumference }}"
                :stroke-dashoffset="offset"/>
    </svg>
    <div class="absolute inset-0 flex flex-col items-center justify-center">
        <span class="font-display font-bold text-ink" style="font-size:{{ $size / 4.2 }}px">{{ round($value) }}%</span>
        @if($label)
            <span class="text-[10px] text-ink-soft -mt-0.5">{{ $label }}</span>
        @endif
    </div>
</div>