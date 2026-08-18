@props(['wireShow' => 'deleteId', 'delete' => 'delete', 'text' => 'Tindakan ini tidak dapat dibatalkan.'])
<template x-teleport="body">
<div x-show="$wire.{{ $wireShow }}" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="absolute inset-0 bg-gray-900/50" wire:click="$set('{{ $wireShow }}', null)"></div>
    <div class="relative surface-card !bg-white w-full max-w-sm rounded-lg p-6 animate-pop-in text-center">
        <div class="w-12 h-12 rounded-full bg-rose-50 text-danger flex items-center justify-center mx-auto mb-3">{!! \App\Support\Icons::svg('trash', 'w-5 h-5') !!}</div>
        <p class="font-display font-bold text-ink">{{ $slot->isEmpty() ? 'Hapus data ini?' : $slot }}</p>
        <p class="text-[13px] text-ink-soft/60 mt-1">{{ $text }}</p>
        <div class="flex gap-2 mt-5">
            <button wire:click="$set('{{ $wireShow }}', null)" class="flex-1 py-2.5 rounded-lg text-[13.5px] font-semibold text-ink-soft/70 hover:bg-primary-50 transition-colors">Batal</button>
            <button wire:click="{{ $delete }}" class="flex-1 py-2.5 rounded-lg bg-danger text-white text-[13.5px] font-semibold hover:opacity-90 transition-opacity">Hapus</button>
        </div>
    </div>
</div>
</template>