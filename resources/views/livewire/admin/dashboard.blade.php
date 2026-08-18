<div wire:poll.15s="$refresh" class="space-y-6">

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card icon="briefcase" label="Total Guru" :value="$this->stats['guru']" color="primary" :index="0"/>
        <x-stat-card icon="user-graduate" label="Total Siswa" :value="$this->stats['siswa']" color="mint" :index="1"/>
        <x-stat-card icon="grid" label="Total Kelas" :value="$this->stats['kelas']" color="accent" :index="2"/>
        <x-stat-card icon="book" label="Mata Pelajaran" :value="$this->stats['mapel']" color="primary" :index="3"/>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 surface-card rounded-lg p-6 animate-fade-up" style="--stagger:4">
            <div class="flex items-center justify-between gap-2 mb-5">
                <h3 class="font-display font-bold text-ink truncate min-w-0">Siswa per Kelas</h3>
                <span class="badge-pill bg-primary-50 text-primary-600 shrink-0">{{ $this->siswaPerKelas->count() }} kelas</span>
            </div>

            @if($this->siswaPerKelas->isEmpty())
                <x-empty-state icon="grid" title="Belum ada kelas" subtitle="Tambahkan kelas terlebih dahulu di menu Kelas."/>
            @else
                <div class="space-y-4">
                    @php $max = max(1, $this->siswaPerKelas->max('siswa_count')); @endphp
                    @foreach($this->siswaPerKelas as $i => $k)
                        <div class="animate-fade-up" style="--stagger: {{ $i + 5 }}">
                            <div class="flex items-center justify-between text-[13px] mb-1.5">
                                <span class="font-semibold text-ink">{{ $k->nama_kelas }}</span>
                                <span class="text-ink-soft/60 font-mono">{{ $k->siswa_count }} siswa</span>
                            </div>
                            <div class="h-2.5 rounded-full bg-primary-50 overflow-hidden">
                                <div class="h-full rounded-full bg-gradient-to-r from-primary-500 to-accent-400 transition-all duration-700"
                                     style="width: {{ $k->siswa_count / $max * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="surface-card rounded-lg p-6 animate-fade-up" style="--stagger:5">
            <div class="flex items-center justify-between gap-2 mb-5">
                <h3 class="font-display font-bold text-ink truncate min-w-0">Aktivitas Terbaru</h3>
                <span class="w-2 h-2 rounded-full bg-mint-500 animate-pulse"></span>
            </div>

            @if($this->aktivitas->isEmpty())
                <x-empty-state icon="bell" title="Belum ada aktivitas" subtitle="Aktivitas guru akan muncul di sini."/>
            @else
                <div class="space-y-4">
                    @foreach($this->aktivitas as $i => $a)
                        <div class="flex gap-3 animate-fade-up" style="--stagger: {{ $i + 6 }}">
                            <div class="w-8 h-8 rounded-lg {{ $a['type'] === 'materi' ? 'bg-primary-50 text-primary-600' : 'bg-orange-50 text-accent-600' }} flex items-center justify-center shrink-0">
                                {!! \App\Support\Icons::svg($a['type'] === 'materi' ? 'book' : 'clipboard', 'w-4 h-4') !!}
                            </div>
                            <div class="min-w-0">
                                <p class="text-[13px] text-ink leading-snug">{{ $a['text'] }}</p>
                                <p class="text-[11px] text-ink-soft/50 mt-0.5">{{ $a['time']?->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>