<div wire:poll.10s="$refresh" class="space-y-6">

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card icon="book" label="Materi Diunggah" :value="$this->stats['materi']" color="primary" :index="0"/>
        <x-stat-card icon="clipboard" label="Tugas Diberikan" :value="$this->stats['tugas']" color="mint" :index="1"/>
        <x-stat-card icon="quiz" label="Kuis Dibuat" :value="$this->stats['kuis']" color="accent" :index="2"/>
        <x-stat-card icon="pencil" label="Perlu Dinilai" :value="$this->stats['belum_dinilai']" color="danger" :index="3"/>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <div class="surface-card rounded-lg p-6 animate-fade-up" style="--stagger:4">
            <div class="flex items-center justify-between gap-2 mb-5">
                <h3 class="font-display font-bold text-ink truncate min-w-0">Tugas Terbaru</h3>
                <a href="{{ route('guru.tugas.index') }}" wire:navigate class="text-[12.5px] font-semibold text-primary-600 hover:underline">Lihat semua</a>
            </div>
            @if($this->tugasTerbaru->isEmpty())
                <x-empty-state icon="clipboard" title="Belum ada tugas" subtitle="Tugas yang Anda buat akan tampil di sini."/>
            @else
                <div class="space-y-3">
                    @foreach($this->tugasTerbaru as $i => $t)
                        <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-primary-50/50 transition-colors animate-fade-up" style="--stagger: {{ $i + 5 }}">
                            <div class="w-9 h-9 rounded-lg bg-mint-50 text-mint-600 flex items-center justify-center shrink-0">{!! \App\Support\Icons::svg('clipboard', 'w-4 h-4') !!}</div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[13.5px] font-semibold text-ink truncate">{{ $t->judul_tugas }}</p>
                                <p class="text-[11.5px] text-ink-soft/50">{{ $t->mapel->nama_mapel ?? '-' }} · Kelas {{ $t->kelas->nama_kelas ?? '-' }}</p>
                            </div>
                            <span class="badge-pill bg-primary-50 text-primary-600 shrink-0">{{ optional($t->batas_waktu)->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="surface-card rounded-lg p-6 animate-fade-up" style="--stagger:5">
            <div class="flex items-center justify-between gap-2 mb-5">
                <h3 class="font-display font-bold text-ink truncate min-w-0">Kuis Aktif</h3>
                <a href="{{ route('guru.kuis.index') }}" wire:navigate class="text-[12.5px] font-semibold text-primary-600 hover:underline">Lihat semua</a>
            </div>
            @if($this->kuisAktif->isEmpty())
                <x-empty-state icon="quiz" title="Tidak ada kuis aktif" subtitle="Buat kuis baru untuk siswa Anda."/>
            @else
                <div class="space-y-3">
                    @foreach($this->kuisAktif as $i => $k)
                        <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-primary-50/50 transition-colors animate-fade-up" style="--stagger: {{ $i + 6 }}">
                            <div class="w-9 h-9 rounded-lg bg-orange-50 text-accent-600 flex items-center justify-center shrink-0">{!! \App\Support\Icons::svg('quiz', 'w-4 h-4') !!}</div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[13.5px] font-semibold text-ink truncate">{{ $k->judul_kuis }}</p>
                                <p class="text-[11.5px] text-ink-soft/50">{{ $k->mapel->nama_mapel ?? '-' }} · Kelas {{ $k->kelas->nama_kelas ?? '-' }}</p>
                            </div>
                            <span class="w-2 h-2 rounded-full bg-mint-500 animate-pulse shrink-0"></span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>