<div class="space-y-6">
    <div class="flex items-center gap-3">
        <p class="text-[13px] text-ink-soft/60">Pilih kelas:</p>
        <div class="w-44">
            <x-select name="filterKelas" :live="true" class="!py-1.5 !text-[12.5px]">
                @foreach($kelasList as $k)
                    <option value="{{ $k->_id }}">Kelas {{ $k->nama_kelas }}</option>
                @endforeach
            </x-select>
        </div>
    </div>

    @if($rows->isEmpty())
        <div class="surface-card rounded-lg"><x-empty-state icon="chart" title="Belum ada data nilai" subtitle="Nilai akan tampil setelah tugas/kuis dinilai."/></div>
    @else
        <div class="surface-card rounded-lg overflow-hidden">
            <div class="overflow-x-auto scroll-thin">
            <table class="w-full text-left table-modern">
                <thead>
                    <tr class="border-b border-primary-100/70 text-[12px] text-ink-soft/60 uppercase tracking-wide">
                        <th class="px-5 py-3.5 font-semibold">Peringkat</th>
                        <th class="px-5 py-3.5 font-semibold">Siswa</th>
                        <th class="px-5 py-3.5 font-semibold">Rata Tugas</th>
                        <th class="px-5 py-3.5 font-semibold">Rata Kuis</th>
                        <th class="px-5 py-3.5 font-semibold">Rata Keseluruhan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $i => $row)
                        <tr wire:key="nilai-{{ $row->siswa->_id }}" class="border-b border-primary-50 last:border-0 hover:bg-primary-50/40 transition-colors animate-fade-up" style="--stagger: {{ $i }}">
                            <td class="px-5 py-3.5">
                                <span class="w-7 h-7 rounded-full flex items-center justify-center text-[12px] font-display font-bold {{ $i === 0 ? 'bg-accent-400 text-white' : 'bg-primary-50 text-primary-600' }}">{{ $i + 1 }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-mint-500 to-primary-500 flex items-center justify-center text-white text-[12px] font-display font-bold shrink-0">
                                        {{ strtoupper(substr($row->siswa->nama_siswa, 0, 1)) }}
                                    </div>
                                    <p class="font-semibold text-ink text-[13.5px]">{{ $row->siswa->nama_siswa }}</p>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-[13.5px] text-ink-soft/70 font-mono">{{ $row->rata_tugas ?: '-' }}</td>
                            <td class="px-5 py-3.5 text-[13.5px] text-ink-soft/70 font-mono">{{ $row->rata_kuis ?: '-' }}</td>
                            <td class="px-5 py-3.5"><span class="font-display font-bold text-ink">{{ $row->rata_keseluruhan ?: '-' }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        </div>
    @endif
</div>