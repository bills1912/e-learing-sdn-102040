@props(['name', 'label' => '', 'live' => false])
@php
    $entangle = "\$wire.entangle('{$name}')" . ($live ? '.live' : '');
@endphp
<div x-data="{
        value: {{ $entangle }},
        timeOpen: false,
        hour: '00',
        minute: '00',
        dp: null,
        observer: null,
        syncingFromWatch: false,
        init() {
            this.syncFromValue();
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

                // Keep the visible calendar/input/time in sync whenever
                // `value` changes from OUTSIDE this component too — e.g. a
                // server-side reset that changes the Livewire property
                // directly, bypassing our own recompute() calls.
                this.$watch('value', (newVal) => {
                    if (!this.dp || this.syncingFromWatch) return;
                    this.syncingFromWatch = true;
                    this.syncFromValue();
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
                // here instead: our own Today shortcut, and a
                // MutationObserver that re-marks today's cell every time the
                // calendar redraws (month navigation, opening, etc.).
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
            const now = new Date();
            this.dp.setDate(now);
            this.hour = this.pad(now.getHours());
            this.minute = this.pad(now.getMinutes());
            this.recompute(now);
        },
        parseValue(v) {
            const d = new Date(v);
            return isNaN(d) ? null : d;
        },
        syncFromValue() {
            const d = this.parseValue(this.value);
            if (!d) return;
            this.hour = String(d.getHours()).padStart(2, '0');
            this.minute = String(d.getMinutes()).padStart(2, '0');
        },
        pad(n) { return String(n).padStart(2, '0'); },
        get dateDisplay() {
            const d = this.parseValue(this.value);
            if (!d) return '';
            return `${this.pad(d.getDate())}/${this.pad(d.getMonth()+1)}/${d.getFullYear()}`;
        },
        pickHour(h) { this.hour = this.pad(h); this.recompute(); },
        pickMinute(m) { this.minute = this.pad(m); this.recompute(); this.timeOpen = false; },
        recompute(pickedDate) {
            const base = pickedDate || this.parseValue(this.value) || new Date();
            const yyyy = base.getFullYear();
            const mm = this.pad(base.getMonth() + 1);
            const dd = this.pad(base.getDate());
            this.value = `${yyyy}-${mm}-${dd}T${this.hour}:${this.minute}`;
        }
     }"
     x-init="init()" class="relative">
    @if($label)
        <label class="block text-[13px] font-semibold text-ink mb-1.5">{{ $label }}</label>
    @endif

    <div class="grid grid-cols-2 gap-2">
        {{-- Date: real Flowbite Datepicker --}}
        <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                {!! \App\Support\Icons::svg('calendar', 'w-4 h-4') !!}
            </span>
            <input type="text" x-ref="dateInput" readonly placeholder="dd/mm/yyyy"
                   {{ $attributes->merge(['class' => 'w-full pl-9 pr-3 py-2.5 rounded-lg border border-gray-300 bg-white text-[13.5px] text-ink focus:ring-4 focus:ring-primary-100 focus:border-primary-500 outline-none cursor-pointer']) }}>
        </div>

        {{-- Time: custom picker (Flowbite itself has no vanilla-JS time picker, only styles the native input) --}}
        <div class="relative">
            <button type="button" @click="timeOpen = !timeOpen" @keydown.escape.window="timeOpen = false"
                    class="w-full flex items-center gap-2 pl-9 pr-3 py-2.5 rounded-lg border border-gray-300 bg-white text-left text-[13.5px] text-ink focus:ring-4 focus:ring-primary-100 focus:border-primary-500 outline-none relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">{!! \App\Support\Icons::svg('clock', 'w-4 h-4') !!}</span>
                <span x-text="hour + ':' + minute"></span>
            </button>

            <div x-show="timeOpen" x-cloak @click.outside="timeOpen = false"
                 x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 class="absolute z-30 mt-1.5 right-0 w-40 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden flex divide-x divide-gray-100">
                <ul class="w-1/2 max-h-48 overflow-y-auto scroll-thin py-1">
                    <template x-for="h in Array.from({length: 24}, (_, i) => i)" :key="'h'+h">
                        <li @click="pickHour(h)"
                            class="px-3 py-1.5 text-[13px] text-center cursor-pointer hover:bg-primary-50"
                            :class="pad(h) === hour ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-ink'"
                            x-text="pad(h)"></li>
                    </template>
                </ul>
                <ul class="w-1/2 max-h-48 overflow-y-auto scroll-thin py-1">
                    <template x-for="m in [0,5,10,15,20,25,30,35,40,45,50,55]" :key="'m'+m">
                        <li @click="pickMinute(m)"
                            class="px-3 py-1.5 text-[13px] text-center cursor-pointer hover:bg-primary-50"
                            :class="pad(m) === minute ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-ink'"
                            x-text="pad(m)"></li>
                    </template>
                </ul>
            </div>
        </div>
    </div>
    <button type="button" @click="pickToday()"
            class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-primary-50 text-primary-700 text-[11.5px] font-semibold hover:bg-primary-100 transition-colors">
        {!! \App\Support\Icons::svg('clock', 'w-3 h-3') !!}
        Hari Ini, Sekarang
    </button>
    @error($name) <p class="text-danger text-[12px] mt-1 font-medium">{{ $message }}</p> @enderror
</div>