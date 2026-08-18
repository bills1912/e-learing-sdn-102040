<div wire:poll.8s class="space-y-6">
    <div class="flex items-center justify-between gap-2 flex-wrap">
        <a href="{{ route('guru.tugas.index') }}" wire:navigate class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-primary-600 hover:underline">
            ← Kembali ke daftar tugas
        </a>
        <div class="flex items-center gap-2 text-[12px] text-ink-soft/50 shrink-0 whitespace-nowrap">
            <span class="w-1.5 h-1.5 rounded-full bg-mint-500 animate-pulse"></span> Update otomatis
        </div>
    </div>

    <div class="surface-card rounded-lg p-5 flex flex-wrap items-center gap-4">
        <div class="badge-pill bg-primary-50 text-primary-600">{{ $tugas->mapel->nama_mapel ?? '-' }}</div>
        <div class="badge-pill bg-orange-50 text-accent-600">Kelas {{ $tugas->kelas->nama_kelas ?? '-' }}</div>
        <div class="text-[12.5px] text-ink-soft/60">Batas: {{ optional($tugas->batas_waktu)->translatedFormat('d M Y, H:i') }}</div>
    </div>

    <div class="surface-card rounded-lg overflow-hidden">
        <div class="overflow-x-auto scroll-thin">
            <table class="w-full text-left table-modern">
            <thead>
                <tr class="border-b border-primary-100/70 text-[12px] text-ink-soft/60 uppercase tracking-wide">
                    <th class="px-5 py-3.5 font-semibold">Siswa</th>
                    <th class="px-5 py-3.5 font-semibold">Status</th>
                    <th class="px-5 py-3.5 font-semibold hidden md:table-cell">Waktu Kumpul</th>
                    <th class="px-5 py-3.5 font-semibold hidden lg:table-cell">Jawaban</th>
                    <th class="px-5 py-3.5 font-semibold">Nilai</th>
                    <th class="px-5 py-3.5 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $i => $row)
                    <tr wire:key="row-{{ $row->siswa->_id }}" class="border-b border-primary-50 last:border-0 hover:bg-primary-50/40 transition-colors animate-fade-up" style="--stagger: {{ $i }}">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-mint-500 to-primary-500 flex items-center justify-center text-white text-[12px] font-display font-bold shrink-0">
                                    {{ strtoupper(substr($row->siswa->nama_siswa, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-ink text-[13.5px]">{{ $row->siswa->nama_siswa }}</p>
                                    <p class="text-[11px] text-ink-soft/50 font-mono">{{ $row->siswa->nis }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            @if(!$row->pengumpulan)
                                <span class="badge-pill bg-rose-50 text-danger">Belum kumpul</span>
                            @elseif($row->pengumpulan->status === 'dinilai')
                                <span class="badge-pill bg-mint-50 text-mint-700">Sudah dinilai</span>
                            @else
                                <span class="badge-pill bg-orange-50 text-accent-600">Menunggu penilaian</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-[13px] text-ink-soft/70 hidden md:table-cell">{{ $row->pengumpulan?->tanggal_kumpul?->translatedFormat('d M, H:i') ?? '-' }}</td>
                        <td class="px-5 py-3.5 text-[13px] text-ink-soft/70 hidden lg:table-cell max-w-xs truncate">{{ $row->pengumpulan?->keterangan ?: '-' }}</td>
                        <td class="px-5 py-3.5">
                            @if($row->pengumpulan?->nilai !== null && $row->pengumpulan?->nilai !== '')
                                <span class="font-display font-bold text-ink">{{ $row->pengumpulan->nilai }}</span>
                            @else
                                <span class="text-ink-soft/40">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            @if($row->pengumpulan)
                                <button wire:click="openGrade('{{ $row->pengumpulan->_id }}')" class="px-3.5 py-1.5 rounded-lg bg-primary-50 text-primary-700 text-[12.5px] font-semibold hover:bg-primary-100 transition-colors">Beri Nilai</button>
                            @else
                                <span class="text-[12px] text-ink-soft/30">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>

    <x-modal wire-show="gradingId" close="closeGrade" title="Beri Nilai Tugas">
        <form wire:submit="saveGrade" class="space-y-4">
            <x-input name="nilaiInput" type="number" label="Nilai (0-100)" placeholder="85"/>
            <x-textarea name="feedbackInput" label="Umpan Balik (opsional)" placeholder="Kerja bagus! Perhatikan lagi soal nomor 3."/>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" wire:click="closeGrade" class="px-4 py-2.5 rounded-lg text-[13.5px] font-semibold text-ink-soft/70 hover:bg-primary-50 transition-colors">Batal</button>
                <button type="submit" class="btn-primary">Simpan Nilai</button>
            </div>
        </form>
    </x-modal>
</div>