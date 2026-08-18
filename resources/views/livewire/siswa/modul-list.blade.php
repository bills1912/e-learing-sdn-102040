<div class="space-y-6">
    <div class="grid grid-cols-2 gap-4">
        <x-stat-card icon="academic-cap" label="Total Modul" :value="$totalModul" color="primary" :index="0"/>
        <x-stat-card icon="trophy" label="Modul Selesai" :value="$totalSelesai" color="mint" :index="1"/>
    </div>

    <div class="flex items-center gap-3">
        <p class="text-[13px] text-ink-soft/60">Filter:</p>
        <div class="w-44">
            <x-select name="filterMapel" :live="true" placeholder="Semua Mapel" class="!py-1.5 !text-[12.5px]">
                <option value="">Semua Mapel</option>
                @foreach($mapelList as $mp)
                    <option value="{{ $mp->_id }}">{{ $mp->nama_mapel }}</option>
                @endforeach
            </x-select>
        </div>
    </div>

    @if($grouped->isEmpty())
        <div class="surface-card rounded-lg"><x-empty-state icon="academic-cap" title="Belum ada modul" subtitle="Modul pembelajaran dari guru Anda akan muncul di sini."/></div>
    @else
        @foreach($grouped as $mapelName => $modulItems)
            <div class="surface-card rounded-lg p-4 sm:p-6">
                <h3 class="font-display font-bold text-ink mb-4 flex items-center gap-2">
                    {!! \App\Support\Icons::svg('book', 'w-4 h-4 text-primary-500 shrink-0') !!}
                    <span class="truncate">{{ $mapelName }}</span>
                </h3>

                <div class="grid sm:grid-cols-2 gap-3">
                    @foreach($modulItems as $m)
                        @php
                            $pct = round($m->langkahSelesai / 3 * 100);
                            $statusLabel = $m->progress['modul_selesai'] ? 'Selesai' : ($m->langkahSelesai > 0 ? 'Sedang berjalan' : 'Belum mulai');
                            $statusColor = $m->progress['modul_selesai'] ? 'bg-mint-50 text-mint-700' : ($m->langkahSelesai > 0 ? 'bg-amber-50 text-amber-700' : 'bg-gray-100 text-gray-500');
                        @endphp
                        <a href="{{ route('siswa.modul.show', $m->_id) }}" wire:navigate
                           class="row-card flex items-center gap-3 p-3.5 rounded-lg border border-gray-200 hover:border-primary-300 hover:bg-primary-50/30 transition-colors"
                           style="--row-accent: {{ $m->progress['modul_selesai'] ? 'var(--color-mint-500)' : ($m->langkahSelesai > 0 ? 'var(--color-accent-500)' : 'var(--color-primary-300)') }}">
                            <x-progress-ring :value="$pct" :size="44" :stroke="4" :color="$m->progress['modul_selesai'] ? 'stroke-mint-500' : 'stroke-primary-500'"/>
                            <div class="min-w-0 flex-1">
                                <p class="text-[13.5px] font-semibold text-ink truncate">{{ $m->judul_modul }}</p>
                                <span class="badge-pill {{ $statusColor }} mt-1 whitespace-nowrap">{{ $statusLabel }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif
</div>