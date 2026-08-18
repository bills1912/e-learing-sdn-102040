@props(['name', 'label' => '', 'live' => false, 'placeholder' => 'dd/mm/yyyy'])
@php
    $entangle = "\$wire.entangle('{$name}')" . ($live ? '.live' : '');
@endphp
<div x-data="{
        value: {{ $entangle }},
        dp: null,
        observer: null,
        syncingFromWatch: false,
        init() {
            this.$nextTick(() => {
                this.dp = new Datepicker(this.$refs.dateInput, {
                    format: 'dd/mm/yyyy',
                    autohide: true,
                    orientation: 'bottom auto',
                    weekStart: 1,
                    language: 'id',
                });
                if (this.value) {
                    const d = this.parseValue(this.value);
                    if (d) this.dp.setDate(d);
                }
                this.$refs.dateInput.addEventListener('changeDate', (e) => this.recompute(e.detail.date));

                // Keep the visible calendar/input in sync whenever `value`
                // changes from OUTSIDE this component too — e.g. a server-side
                // 'Reset Filter' action that resets the Livewire property
                // directly, bypassing our own clear()/recompute() calls.
                this.$watch('value', (newVal) => {
                    if (!this.dp || this.syncingFromWatch) return;
                    this.syncingFromWatch = true;
                    if (!newVal) {
                        this.dp.setDate({ clear: true });
                    } else {
                        const d = this.parseValue(newVal);
                        if (d) this.dp.setDate(d);
                    }
                    this.$nextTick(() => { this.syncingFromWatch = false; });
                });

                // The library's own todayBtn/todayHighlight options don't
                // reliably take effect in this version, so both are built
                // here instead: our own Today button, and a MutationObserver
                // that re-marks today's cell every time the calendar redraws
                // (month navigation, opening, etc.) — independent of the
                // library's internal option handling.
                const mainEl = this.dp.picker.element.querySelector('.datepicker-main');
                if (mainEl) {
                    this.observer = new MutationObserver(() => this.markToday());
                    this.observer.observe(mainEl, { childList: true, subtree: true, attributes: true });
                }
                this.markToday();
            });
        },
        markToday() {
            const picker = this.dp?.picker?.element;
            if (!picker) return;
            const now = new Date();
            picker.querySelectorAll('.datepicker-cell[data-date]').forEach((cell) => {
                const d = new Date(Number(cell.dataset.date));
                const isToday = d.getFullYear() === now.getFullYear() && d.getMonth() === now.getMonth() && d.getDate() === now.getDate();
                cell.classList.toggle('elrn-today-cell', isToday);
            });
        },
        pickToday() {
            this.dp.setDate(new Date());
        },
        parseValue(v) {
            const d = new Date(v);
            return isNaN(d) ? null : d;
        },
        pad(n) { return String(n).padStart(2, '0'); },
        recompute(pickedDate) {
            if (!pickedDate) {
                this.value = '';
                return;
            }
            const yyyy = pickedDate.getFullYear();
            const mm = this.pad(pickedDate.getMonth() + 1);
            const dd = this.pad(pickedDate.getDate());
            this.value = `${yyyy}-${mm}-${dd}`;
        },
        clear() {
            this.value = '';
            this.dp?.setDate({ clear: true });
        }
     }"
     x-init="init()" class="relative">
    @if($label)
        <label class="block text-[13px] font-semibold text-ink mb-1.5">{{ $label }}</label>
    @endif

    <div class="relative">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
            {!! \App\Support\Icons::svg('calendar', 'w-4 h-4') !!}
        </span>
        <input type="text" x-ref="dateInput" readonly placeholder="{{ $placeholder }}"
               {{ $attributes->merge(['class' => 'w-full pl-9 pr-8 py-2.5 rounded-lg border border-gray-300 bg-white text-[13.5px] text-ink focus:ring-4 focus:ring-primary-100 focus:border-primary-500 outline-none cursor-pointer']) }}>
        <button type="button" x-show="value" x-cloak @click="clear()"
                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Our own "Hari Ini" shortcut, since the library's built-in today
         button doesn't render reliably. --}}
    <button type="button" @click="pickToday()"
            class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-primary-50 text-primary-700 text-[11.5px] font-semibold hover:bg-primary-100 transition-colors">
        {!! \App\Support\Icons::svg('calendar', 'w-3 h-3') !!}
        Hari Ini
    </button>

    @error($name) <p class="text-danger text-[12px] mt-1 font-medium">{{ $message }}</p> @enderror
</div>