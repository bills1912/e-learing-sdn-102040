@props(['name', 'label' => '', 'type' => 'text', 'placeholder' => ''])
<div>
    @if($label)
        <label class="block text-[13px] font-semibold text-ink mb-1.5">{{ $label }}</label>
    @endif
    <input type="{{ $type }}" wire:model="{{ $name }}" placeholder="{{ $placeholder }}"
           {{ $attributes->merge(['class' => 'w-full px-3.5 py-2.5 rounded-xl border border-primary-100 bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition-all text-[14px]']) }}>
    @error($name) <p class="text-danger text-[12px] mt-1 font-medium">{{ $message }}</p> @enderror
</div>
