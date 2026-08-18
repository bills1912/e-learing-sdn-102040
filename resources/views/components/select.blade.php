@props(['name', 'label' => '', 'live' => false, 'placeholder' => 'Pilih...'])
@php
    $entangle = "\$wire.entangle('{$name}')" . ($live ? '.live' : '');
@endphp
<div x-data="{
        value: {{ $entangle }},
        open: false,
        query: '',
        options: [],
        get filtered() {
            if (!this.query) return this.options;
            const q = this.query.toLowerCase();
            return this.options.filter(o => o.label.toLowerCase().includes(q));
        },
        get selectedLabel() {
            const f = this.options.find(o => String(o.value) === String(this.value));
            return f ? f.label : @js($placeholder);
        },
        pick(opt) {
            this.value = opt.value;
            this.open = false;
            this.query = '';
        }
     }"
     x-init="options = Array.from($refs.optionSource.options).map(o => ({ value: o.value, label: o.text }))"
     class="relative">

    @if($label)
        <label class="block text-[13px] font-semibold text-ink mb-1.5">{{ $label }}</label>
    @endif

    {{-- Real <option> list, declared normally via slot; hidden, read once by Alpine --}}
    <select x-ref="optionSource" class="hidden" tabindex="-1" aria-hidden="true">{{ $slot }}</select>

    <button type="button" @click="open = !open; $nextTick(() => open && $refs.searchInput?.focus())" @keydown.escape.window="open = false"
            {{ $attributes->merge(['class' => 'w-full flex items-center justify-between gap-2 px-3.5 py-2.5 rounded-lg border border-gray-300 bg-white text-left text-[14px] text-ink focus:ring-4 focus:ring-primary-100 focus:border-primary-500 outline-none transition-colors']) }}>
        <span x-text="selectedLabel" class="truncate"></span>
        <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-150" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
    </button>

    <div x-show="open" x-cloak @click.outside="open = false"
         x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         class="absolute z-30 mt-1.5 w-full bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
        <div class="p-2 border-b border-gray-100">
            <div class="relative">
                <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                <input x-ref="searchInput" x-model="query" type="text" placeholder="Cari..."
                       class="w-full pl-8 pr-3 py-1.5 text-[13px] rounded-md border border-gray-200 focus:border-primary-400 outline-none">
            </div>
        </div>
        <ul class="max-h-56 overflow-y-auto scroll-thin py-1">
            <template x-for="opt in filtered" :key="opt.value">
                <li @click="pick(opt)"
                    class="px-3.5 py-2 text-[13.5px] cursor-pointer hover:bg-primary-50 flex items-center justify-between gap-2"
                    :class="String(opt.value) === String(value) ? 'bg-primary-50 text-primary-700 font-medium' : 'text-ink'">
                    <span x-text="opt.label" class="truncate"></span>
                    <svg x-show="String(opt.value) === String(value)" class="w-3.5 h-3.5 text-primary-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </li>
            </template>
            <li x-show="filtered.length === 0" class="px-3.5 py-3 text-[13px] text-ink-soft text-center">Tidak ditemukan</li>
        </ul>
    </div>
    @error($name) <p class="text-danger text-[12px] mt-1 font-medium">{{ $message }}</p> @enderror
</div>