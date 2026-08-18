<div wire:poll.8s class="space-y-6">
    <div class="flex items-center justify-between gap-2 flex-wrap">
        <a href="{{ route('guru.kuis.index') }}" wire:navigate class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-primary-600 hover:underline">
            ← Kembali ke daftar kuis
        </a>
        <div class="flex items-center gap-2 text-[12px] text-ink-soft/50 shrink-0 whitespace-nowrap">
            <span class="w-1.5 h-1.5 rounded-full bg-mint-500 animate-pulse"></span> Update otomatis
        </div>
    </div>

    <div class="grid sm:grid-cols-3 gap-4">
        <x-stat-card icon="quiz" label="Total Soal" :value="$totalSoal" color="primary" :index="0"/>
        <x-stat-card icon="check-badge" label="Siswa Selesai" :value="$rows->where('selesai', true)->count()" color="mint" :index="1"/>
        <div class="surface-card surface-card-hover rounded-lg p-5 animate-fade-up" style="--stagger:2">
            <div class="w-11 h-11 rounded-lg bg-orange-50 text-accent-600 flex items-center justify-center">{!! \App\Support\Icons::svg('trophy', 'w-5 h-5') !!}</div>
            <p class="mt-4 text-[13px] text-ink-soft/70 font-medium">Rata-rata Skor</p>
            <p class="mt-0.5 font-display font-bold text-[28px] text-ink leading-none">{{ $rataRata ? round($rataRata) : '-' }}</p>
        </div>
    </div>

    <div class="surface-card rounded-lg overflow-hidden">
        <div class="overflow-x-auto scroll-thin">
            <table class="w-full text-left table-modern">
            <thead>
                <tr class="border-b border-primary-100/70 text-[12px] text-ink-soft/60 uppercase tracking-wide">
                    <th class="px-5 py-3.5 font-semibold">Siswa</th>
                    <th class="px-5 py-3.5 font-semibold">Status</th>
                    <th class="px-5 py-3.5 font-semibold">Benar</th>
                    <th class="px-5 py-3.5 font-semibold">Skor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $i => $row)
                    <tr wire:key="hasil-{{ $row->siswa->_id }}" class="border-b border-primary-50 last:border-0 hover:bg-primary-50/40 transition-colors animate-fade-up" style="--stagger: {{ $i }}">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-mint-500 to-primary-500 flex items-center justify-center text-white text-[12px] font-display font-bold shrink-0">
                                    {{ strtoupper(substr($row->siswa->nama_siswa, 0, 1)) }}
                                </div>
                                <p class="font-semibold text-ink text-[13.5px]">{{ $row->siswa->nama_siswa }}</p>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            @if($row->selesai)
                                <span class="badge-pill bg-mint-50 text-mint-700">Selesai</span>
                            @elseif($row->dijawab > 0)
                                <span class="badge-pill bg-orange-50 text-accent-600">Mengerjakan ({{ $row->dijawab }}/{{ $row->total }})</span>
                            @else
                                <span class="badge-pill bg-rose-50 text-danger">Belum mulai</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-[13px] text-ink-soft/70 font-mono">{{ $row->benar }}/{{ $row->total }}</td>
                        <td class="px-5 py-3.5">
                            @if($row->selesai)
                                <span class="font-display font-bold text-ink">{{ $row->skor }}</span>
                            @else
                                <span class="text-ink-soft/40">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>