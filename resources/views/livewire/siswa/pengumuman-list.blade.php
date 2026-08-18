<div class="space-y-4">
    <div class="surface-card rounded-lg p-4">
        <div class="flex items-end gap-3 flex-wrap">
            <div class="w-full sm:w-44">
                <x-date-picker name="filterDari" :live="true" label="Dari Tanggal" placeholder="Semua tanggal"/>
            </div>
            <div class="w-full sm:w-44">
                <x-date-picker name="filterSampai" :live="true" label="Sampai Tanggal" placeholder="Semua tanggal"/>
            </div>
            @if($filterDari || $filterSampai)
                <button wire:click="resetFilter" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-lg bg-rose-50 text-danger text-[13px] font-semibold hover:bg-rose-100 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    Reset Filter
                </button>
            @endif
        </div>
    </div>

    @if($items->isEmpty())
        <div class="surface-card rounded-lg">
            @if($filterDari || $filterSampai)
                <x-empty-state icon="bell" title="Tidak ada pengumuman di rentang ini" subtitle="Coba ubah atau reset filter tanggal."/>
            @else
                <x-empty-state icon="bell" title="Belum ada pengumuman" subtitle="Pengumuman dari guru akan muncul di sini."/>
            @endif
        </div>
    @else
        @foreach($items as $i => $p)
            <div wire:key="pengumuman-{{ $p->_id }}" class="surface-card row-card rounded-lg p-5 animate-fade-up" style="--stagger: {{ $i }}; --row-accent: var(--color-primary-400)">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center shrink-0 ring-4 ring-white">
                        {!! \App\Support\Icons::svg('bell', 'w-5 h-5') !!}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-display font-semibold text-ink">{{ $p->judul }}</p>
                        <p class="text-[13.5px] text-ink-soft/70 mt-1.5 whitespace-pre-line leading-relaxed">{{ $p->isi }}</p>
                        <p class="text-[11.5px] text-ink-soft/50 mt-2">{{ $p->guru->nama_guru ?? '-' }} · {{ $p->created_at?->translatedFormat('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>