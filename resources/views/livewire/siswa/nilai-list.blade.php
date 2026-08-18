<div class="space-y-6">

    <div class="grid sm:grid-cols-3 gap-4">
        <div class="surface-card surface-card-hover rounded-lg p-6 sm:col-span-1 flex items-center gap-4 animate-fade-up" style="--stagger:0">
            <x-progress-ring :value="$rataKeseluruhan" :size="76" :stroke="7" color="stroke-primary-500"/>
            <div>
                <p class="text-[13px] text-ink-soft/70 font-medium">Rata-rata</p>
                <p class="font-display font-bold text-ink text-xl">{{ $rataKeseluruhan ?: '-' }}</p>
            </div>
        </div>
        <div class="sm:col-span-2 surface-card rounded-lg p-5 animate-fade-up" style="--stagger:1">
            <p class="text-[13px] font-semibold text-ink mb-3">Rata-rata per Mata Pelajaran</p>
            @if($perMapel->isEmpty())
                <p class="text-[12.5px] text-ink-soft/50">Belum ada nilai.</p>
            @else
                <div class="flex flex-wrap gap-2">
                    @foreach($perMapel as $mapel => $rata)
                        <div class="badge-pill bg-primary-50 text-primary-700">{{ $mapel }}: <span class="font-bold">{{ $rata }}</span></div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @if($items->isEmpty())
        <div class="surface-card rounded-lg"><x-empty-state icon="trophy" title="Belum ada nilai" subtitle="Nilai tugas dan kuis Anda akan muncul di sini."/></div>
    @else
        <div class="surface-card rounded-lg overflow-hidden">
            <div class="overflow-x-auto scroll-thin">
            <table class="w-full text-left table-modern">
                <thead>
                    <tr class="border-b border-primary-100/70 text-[12px] text-ink-soft/60 uppercase tracking-wide">
                        <th class="px-5 py-3.5 font-semibold">Keterangan</th>
                        <th class="px-5 py-3.5 font-semibold">Mapel</th>
                        <th class="px-5 py-3.5 font-semibold">Jenis</th>
                        <th class="px-5 py-3.5 font-semibold">Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $i => $n)
                        <tr wire:key="nilai-{{ $n->_id }}" class="border-b border-primary-50 last:border-0 hover:bg-primary-50/40 transition-colors animate-fade-up" style="--stagger: {{ $i }}">
                            <td class="px-5 py-3.5 text-[13.5px] font-medium text-ink">{{ $n->keterangan }}</td>
                            <td class="px-5 py-3.5"><span class="badge-pill bg-primary-50 text-primary-600">{{ $n->mapel->nama_mapel ?? '-' }}</span></td>
                            <td class="px-5 py-3.5">
                                <span class="badge-pill {{ $n->jenis === 'kuis' ? 'bg-orange-50 text-accent-600' : 'bg-mint-50 text-mint-700' }} capitalize">{{ $n->jenis }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-display font-bold {{ $n->nilai >= 75 ? 'text-mint-600' : ($n->nilai >= 60 ? 'text-accent-600' : 'text-danger') }}">{{ $n->nilai }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        </div>

        @if($items->hasPages())
            <div class="flex items-center justify-between gap-3 flex-wrap px-1">
                <p class="text-[12.5px] text-ink-soft/60">
                    Menampilkan {{ $items->firstItem() }}–{{ $items->lastItem() }} dari {{ $items->total() }} nilai
                </p>
                <div class="flex items-center gap-1">
                    <button wire:click="previousPage" @disabled($items->onFirstPage())
                            class="px-3 py-1.5 rounded-lg text-[12.5px] font-semibold text-ink-soft/70 hover:bg-primary-50 disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-transparent transition-colors">
                        ← Sebelumnya
                    </button>
                    <span class="px-3 py-1.5 rounded-lg bg-primary-50 text-primary-700 text-[12.5px] font-semibold">
                        {{ $items->currentPage() }} / {{ $items->lastPage() }}
                    </span>
                    <button wire:click="nextPage" @disabled(! $items->hasMorePages())
                            class="px-3 py-1.5 rounded-lg text-[12.5px] font-semibold text-ink-soft/70 hover:bg-primary-50 disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-transparent transition-colors">
                        Selanjutnya →
                    </button>
                </div>
            </div>
        @endif
    @endif
</div>