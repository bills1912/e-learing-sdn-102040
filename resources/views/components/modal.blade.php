@props(['wireShow' => 'showModal', 'title' => '', 'maxWidth' => 'max-w-lg'])
<template x-teleport="body">
<div x-show="$wire.{{ $wireShow }}" x-cloak
     class="fixed inset-0 z-[100] flex items-center justify-center p-4"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="absolute inset-0 bg-gray-900/50" wire:click="{{ $attributes->get('close', 'closeModal') }}"></div>
    <div class="relative surface-card !bg-white w-full {{ $maxWidth }} rounded-lg overflow-hidden animate-pop-in max-h-[90vh] flex flex-col"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
        <div class="px-6 py-4 border-b border-primary-100/70 flex items-center justify-between shrink-0">
            <h3 class="font-display font-bold text-lg text-ink">{{ $title }}</h3>
            <button wire:click="{{ $attributes->get('close', 'closeModal') }}" class="w-8 h-8 rounded-full hover:bg-primary-50 flex items-center justify-center text-ink-soft/60 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6 overflow-y-auto scroll-thin">
            {{ $slot }}
        </div>
    </div>
</div>
</template>