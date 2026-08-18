<div class="space-y-6">
    <div class="flex items-center justify-between gap-2 flex-wrap">
        <p class="text-[13px] text-ink-soft/60 shrink-0">{{ $items->count() }} tugas</p>
        <button wire:click="create" class="btn-primary">
            {!! \App\Support\Icons::svg('plus', 'w-4 h-4') !!} Beri Tugas
        </button>
    </div>

    @if($items->isEmpty())
        <div class="surface-card rounded-lg"><x-empty-state icon="clipboard" title="Belum ada tugas" subtitle="Klik “Beri Tugas” untuk membuat tugas pertama."/></div>
    @else
        <div class="surface-card rounded-lg overflow-hidden">
            <ul role="list" class="divide-y divide-primary-50">
                @foreach($items as $i => $t)
                    @php
                        $progress = $t->jumlah_siswa > 0 ? round($t->jumlah_kumpul / $t->jumlah_siswa * 100) : 0;
                        $accent = $progress >= 100 ? 'var(--color-mint-500)' : ($progress > 0 ? 'var(--color-accent-500)' : 'var(--color-primary-400)');
                    @endphp
                    <li wire:key="tugas-{{ $t->_id }}" class="row-card animate-fade-up hover:bg-primary-50/30 transition-colors" style="--stagger: {{ $i }}; --row-accent: {{ $accent }}">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4 p-5">
                            <div class="w-11 h-11 rounded-full bg-mint-50 text-mint-600 flex items-center justify-center shrink-0 ring-4 ring-white">{!! \App\Support\Icons::svg('clipboard', 'w-5 h-5') !!}</div>
                            <div class="min-w-0 flex-1">
                                <p class="font-display font-semibold text-ink">{{ $t->judul_tugas }}</p>
                                <div class="flex items-center gap-2 flex-wrap mt-1">
                                    <span class="badge-pill bg-primary-50 text-primary-600">{{ $t->mapel->nama_mapel ?? '-' }}</span>
                                    <span class="badge-pill bg-orange-50 text-accent-600">Kelas {{ $t->kelas->nama_kelas ?? '-' }}</span>
                                    <span class="text-[11.5px] text-ink-soft/50">Batas: {{ optional($t->batas_waktu)->translatedFormat('d M Y, H:i') }}</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between sm:justify-end gap-3 sm:gap-4 shrink-0 pt-3 sm:pt-0 border-t sm:border-t-0 border-primary-50">
                                <div class="flex items-center gap-3">
                                    <div class="text-right">
                                        <p class="text-[11px] text-ink-soft/50">Terkumpul</p>
                                        <p class="font-display font-bold text-ink text-[15px]">{{ $t->jumlah_kumpul }}/{{ $t->jumlah_siswa }}</p>
                                    </div>
                                    <x-progress-ring :value="$progress" :size="44" :stroke="5" color="stroke-mint-500"/>
                                </div>
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('guru.tugas.pengumpulan', $t->_id) }}" wire:navigate
                                       class="px-3.5 py-2 rounded-lg bg-primary-50 text-primary-700 text-[12.5px] font-semibold hover:bg-primary-100 transition-colors whitespace-nowrap">
                                       Lihat &amp; Nilai
                                    </a>
                                    <button wire:click="edit('{{ $t->_id }}')" class="w-8 h-8 rounded-lg hover:bg-primary-100 flex items-center justify-center text-ink-soft/50 hover:text-primary-600 transition-colors shrink-0">{!! \App\Support\Icons::svg('pencil', 'w-4 h-4') !!}</button>
                                    <button wire:click="confirmDelete('{{ $t->_id }}')" class="w-8 h-8 rounded-lg hover:bg-rose-50 flex items-center justify-center text-ink-soft/50 hover:text-danger transition-colors shrink-0">{!! \App\Support\Icons::svg('trash', 'w-4 h-4') !!}</button>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <x-modal wire-show="showModal" :title="$editId ? 'Ubah Tugas' : 'Beri Tugas Baru'" maxWidth="max-w-2xl">
        <form wire:submit="save" class="space-y-4">
            <x-input name="judul_tugas" label="Judul Tugas" placeholder="Contoh: Latihan Soal Pecahan"/>
            <div class="grid sm:grid-cols-2 gap-4">
                <x-select name="mapel_id" label="Mata Pelajaran">
                    @foreach($mapelList as $mp)
                        <option value="{{ $mp->_id }}">{{ $mp->nama_mapel }}</option>
                    @endforeach
                </x-select>
                <x-select name="kelas_id" label="Kelas">
                    @foreach($kelasList as $k)
                        <option value="{{ $k->_id }}">Kelas {{ $k->nama_kelas }}</option>
                    @endforeach
                </x-select>
            </div>
            <x-textarea name="deskripsi" label="Deskripsi Tugas" :rows="4"/>
            <x-input name="batas_waktu" type="datetime-local" label="Batas Waktu Pengumpulan"/>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" wire:click="closeModal" class="px-4 py-2.5 rounded-lg text-[13.5px] font-semibold text-ink-soft/70 hover:bg-primary-50 transition-colors">Batal</button>
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>
    </x-modal>

    <x-confirm-delete wire-show="deleteId">Hapus tugas ini beserta semua pengumpulannya?</x-confirm-delete>
</div>