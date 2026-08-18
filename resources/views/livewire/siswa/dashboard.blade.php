<div wire:poll.10s="$refresh" class="space-y-6">

    @if($this->pengumumanTerbaru->isNotEmpty())
        <div class="space-y-2.5">
            @foreach($this->pengumumanTerbaru as $i => $p)
                <div wire:key="dash-pengumuman-{{ $p->_id }}" class="row-card surface-card rounded-lg p-4 flex items-start gap-3 animate-fade-up" style="--stagger: {{ $i }}; --row-accent: var(--color-primary-400)">
                    <div class="w-9 h-9 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
                        {!! \App\Support\Icons::svg('bell', 'w-4 h-4') !!}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="text-[13.5px] font-semibold text-ink">{{ $p->judul }}</p>
                            <span class="badge-pill bg-primary-50 text-primary-600 whitespace-nowrap">Pengumuman</span>
                        </div>
                        <p class="text-[13px] text-ink-soft/70 mt-1 line-clamp-2">{{ $p->isi }}</p>
                        <p class="text-[11px] text-ink-soft/50 mt-1.5">{{ $p->guru->nama_guru ?? '-' }} · {{ $p->created_at?->diffForHumans() }}</p>
                    </div>
                </div>
            @endforeach
            <div class="text-right">
                <a href="{{ route('siswa.pengumuman.index') }}" wire:navigate class="text-[12.5px] font-semibold text-primary-600 hover:underline">Lihat semua pengumuman →</a>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card icon="book" label="Materi Tersedia" :value="$this->stats['materi']" color="primary" :index="0"/>
        <x-stat-card icon="clipboard" label="Tugas Selesai" :value="$this->stats['tugas_selesai']" color="mint" :index="1"/>
        <x-stat-card icon="clock" label="Tugas Belum Dikumpulkan" :value="max(0, $this->stats['tugas_total'] - $this->stats['tugas_selesai'])" color="accent" :index="2"/>
        <div class="surface-card surface-card-hover rounded-lg p-4 sm:p-5 animate-fade-up flex items-center gap-3 sm:gap-4" style="--stagger:3">
            <x-progress-ring :value="$this->stats['rata_nilai']" :size="52" :stroke="5" color="stroke-primary-500"/>
            <div class="min-w-0">
                <p class="text-[12px] sm:text-[13px] text-ink-soft/70 font-medium truncate">Rata-rata Nilai</p>
                <p class="font-display font-bold text-ink text-lg">{{ $this->stats['rata_nilai'] ?: '-' }}</p>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <div class="surface-card rounded-lg p-4 sm:p-6 animate-fade-up" style="--stagger:4">
            <div class="flex items-center justify-between gap-2 mb-5">
                <h3 class="font-display font-bold text-ink truncate min-w-0">Tugas Mendatang</h3>
                <a href="{{ route('siswa.tugas.index') }}" wire:navigate class="text-[12.5px] font-semibold text-primary-600 hover:underline shrink-0 whitespace-nowrap">Lihat semua</a>
            </div>
            @if($this->tugasMendatang->isEmpty())
                <x-empty-state icon="check-badge" title="Semua tugas selesai! 🎉" subtitle="Kerja bagus, tidak ada tugas yang tertunda."/>
            @else
                <div class="space-y-3">
                    @foreach($this->tugasMendatang as $i => $t)
                        <div class="flex items-center gap-2 sm:gap-3 p-2.5 sm:p-3 rounded-lg hover:bg-primary-50/50 transition-colors animate-fade-up" style="--stagger: {{ $i + 5 }}">
                            <div class="w-9 h-9 rounded-lg bg-orange-50 text-accent-600 flex items-center justify-center shrink-0">{!! \App\Support\Icons::svg('clipboard', 'w-4 h-4') !!}</div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[13.5px] font-semibold text-ink truncate">{{ $t->judul_tugas }}</p>
                                <p class="text-[11.5px] text-ink-soft/50 truncate">{{ $t->mapel->nama_mapel ?? '-' }}</p>
                            </div>
                            <span class="badge-pill bg-rose-50 text-danger shrink-0 whitespace-nowrap">{{ optional($t->batas_waktu)->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="surface-card rounded-lg p-4 sm:p-6 animate-fade-up" style="--stagger:5">
            <div class="flex items-center justify-between gap-2 mb-5">
                <h3 class="font-display font-bold text-ink truncate min-w-0">Kuis Aktif Sekarang</h3>
                <span class="w-2 h-2 rounded-full bg-mint-500 animate-pulse shrink-0"></span>
            </div>
            @if($this->kuisAktif->isEmpty())
                <x-empty-state icon="quiz" title="Tidak ada kuis aktif" subtitle="Kuis baru akan muncul di sini saat tersedia."/>
            @else
                <div class="space-y-3">
                    @foreach($this->kuisAktif as $i => $k)
                        <div class="flex items-center gap-2 sm:gap-3 p-2.5 sm:p-3 rounded-lg bg-mint-50/50 animate-fade-up" style="--stagger: {{ $i + 6 }}">
                            <div class="w-9 h-9 rounded-lg bg-mint-100 text-mint-700 flex items-center justify-center shrink-0">{!! \App\Support\Icons::svg('quiz', 'w-4 h-4') !!}</div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[13.5px] font-semibold text-ink truncate">{{ $k->judul_kuis }}</p>
                                <p class="text-[11.5px] text-ink-soft/50 truncate">{{ $k->mapel->nama_mapel ?? '-' }} · {{ $k->durasi_menit }} menit</p>
                            </div>
                            <a href="{{ route('siswa.kuis.kerjakan', $k->_id) }}" wire:navigate class="px-3 py-1.5 rounded-lg bg-mint-500 text-white text-[12px] font-semibold hover:bg-mint-600 transition-colors shrink-0 whitespace-nowrap">Kerjakan</a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>