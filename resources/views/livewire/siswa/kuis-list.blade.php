<div class="space-y-4">
    @if($items->isEmpty())
        <div class="surface-card rounded-lg"><x-empty-state icon="quiz" title="Belum ada kuis" subtitle="Kuis dari guru Anda akan muncul di sini."/></div>
    @else
        <div class="grid sm:grid-cols-2 gap-4">
            @foreach($items as $i => $k)
                @php
                    $statusColor = ['terjadwal' => 'bg-primary-50 text-primary-600', 'berlangsung' => 'bg-mint-50 text-mint-700', 'selesai' => 'bg-slate-100 text-slate-500'][$k->status];
                    $accent = ['terjadwal' => 'var(--color-primary-400)', 'berlangsung' => 'var(--color-mint-500)', 'selesai' => '#CBD5E1'][$k->status];
                @endphp
                <div wire:key="kuis-{{ $k->_id }}" class="surface-card surface-card-hover row-card rounded-lg pr-5 pl-6 py-5 animate-fade-up flex flex-col" style="--stagger: {{ $i }}; --row-accent: {{ $accent }}">
                    <div class="flex items-start justify-between">
                        <div class="w-11 h-11 rounded-full bg-orange-50 text-accent-600 flex items-center justify-center ring-4 ring-white">{!! \App\Support\Icons::svg('quiz', 'w-5 h-5') !!}</div>
                        <span class="badge-pill {{ $statusColor }} capitalize">{{ $k->status }}</span>
                    </div>
                    <p class="mt-4 font-display font-semibold text-ink">{{ $k->judul_kuis }}</p>
                    <p class="text-[12.5px] text-ink-soft/60 mt-1 line-clamp-2">{{ $k->deskripsi }}</p>
                    <div class="flex items-center gap-2 flex-wrap mt-3">
                        <span class="badge-pill bg-primary-50 text-primary-600">{{ $k->mapel->nama_mapel ?? '-' }}</span>
                        <span class="text-[11.5px] text-ink-soft/50">{{ $k->totalSoal }} soal · {{ $k->durasi_menit }} menit</span>
                    </div>

                    <div class="mt-4 pt-4 border-t border-primary-50 mt-auto">
                        @if($k->sudahSelesai)
                            <div class="px-4 py-2.5 rounded-lg bg-mint-50 text-mint-700 text-[13px] font-semibold text-center flex items-center justify-center gap-2">
                                {!! \App\Support\Icons::svg('check-badge', 'w-4 h-4') !!} Sudah Dikerjakan
                            </div>
                        @elseif($k->status === 'berlangsung')
                            <a href="{{ route('siswa.kuis.kerjakan', $k->_id) }}" wire:navigate class="btn-primary w-full text-[13px]">Kerjakan Sekarang</a>
                        @elseif($k->status === 'terjadwal')
                            <div class="px-4 py-2.5 rounded-lg bg-primary-50 text-primary-600 text-[13px] font-semibold text-center">Mulai {{ $k->waktu_mulai->translatedFormat('d M, H:i') }}</div>
                        @else
                            <div class="px-4 py-2.5 rounded-lg bg-slate-100 text-slate-400 text-[13px] font-semibold text-center">Waktu Habis</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>