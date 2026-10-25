<div class="space-y-6">
    <div class="grid grid-cols-2 gap-4">
        <x-stat-card icon="academic-cap" label="Total Modul" :value="$totalModul" color="primary" :index="0"/>
        <x-stat-card icon="trophy" label="Modul Selesai" :value="$totalSelesai" color="mint" :index="1"/>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="relative w-full sm:max-w-xs">
            <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);display:flex;align-items:center;pointer-events:none;" class="text-ink-soft/40">{!! \App\Support\Icons::svg('search', 'w-4 h-4') !!}</span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari judul modul…"
                       style="height:40px;padding:0 12px 0 38px;line-height:40px;"
                       class="w-full rounded-lg border border-gray-300 bg-white text-[13px] text-ink placeholder:text-ink-soft/40 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
        </div>
        <div class="w-full sm:w-44">
            <x-select name="filterMapel" :live="true" placeholder="Semua Mapel" class="!py-1.5 !text-[12.5px]">
                <option value="">Semua Mapel</option>
                @foreach($mapelList as $mp)
                    <option value="{{ $mp->_id }}">{{ $mp->nama_mapel }}</option>
                @endforeach
            </x-select>
        </div>
        @if($search !== '' || $filterMapel !== '')
            <button type="button" wire:click="resetFilters" class="text-[12.5px] font-semibold text-primary-600 hover:text-primary-700 whitespace-nowrap self-start sm:self-center">Reset</button>
        @endif
    </div>

    @if($grouped->isEmpty())
        <div class="surface-card rounded-lg">@if($search !== '' || $filterMapel !== '')
            <x-empty-state icon="search" title="Modul tidak ditemukan" subtitle="Coba kata kunci lain atau reset filter."/>
        @else
            <x-empty-state icon="academic-cap" title="Belum ada modul" subtitle="Modul pembelajaran dari guru Anda akan muncul di sini."/>
        @endif</div>
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