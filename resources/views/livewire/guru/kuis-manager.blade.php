<div class="space-y-6">
    <div class="flex items-center justify-between gap-2 flex-wrap">
        <p class="text-[13px] text-ink-soft/60 shrink-0">{{ $items->count() }} kuis</p>
        <button wire:click="create" class="btn-primary">
            {!! \App\Support\Icons::svg('plus', 'w-4 h-4') !!} Buat Kuis
        </button>
    </div>

    @if($items->isEmpty())
        <div class="surface-card rounded-lg"><x-empty-state icon="quiz" title="Belum ada kuis" subtitle="Klik “Buat Kuis” untuk membuat kuis pertama Anda."/></div>
    @else
        <div class="grid sm:grid-cols-2 gap-4">
            @foreach($items as $i => $k)
                @php
                    $now = now();
                    $status = $now->lt($k->waktu_mulai) ? 'terjadwal' : ($now->gt($k->waktu_selesai) ? 'selesai' : 'berlangsung');
                    $statusColor = ['terjadwal' => 'bg-primary-50 text-primary-600', 'berlangsung' => 'bg-mint-50 text-mint-700', 'selesai' => 'bg-slate-100 text-slate-500'][$status];
                    $accent = ['terjadwal' => 'var(--color-primary-400)', 'berlangsung' => 'var(--color-mint-500)', 'selesai' => '#CBD5E1'][$status];
                @endphp
                <div wire:key="kuis-{{ $k->_id }}" class="surface-card surface-card-hover row-card rounded-lg pr-5 pl-6 py-5 animate-fade-up flex flex-col" style="--stagger: {{ $i }}; --row-accent: {{ $accent }}">
                    <div class="flex items-start justify-between">
                        <div class="w-11 h-11 rounded-full bg-orange-50 text-accent-600 flex items-center justify-center ring-4 ring-white">{!! \App\Support\Icons::svg('quiz', 'w-5 h-5') !!}</div>
                        <span class="badge-pill {{ $statusColor }} capitalize">{{ $status }}</span>
                    </div>
                    <p class="mt-4 font-display font-semibold text-ink">{{ $k->judul_kuis }}</p>
                    <div class="flex items-center gap-2 flex-wrap mt-1.5">
                        <span class="badge-pill bg-primary-50 text-primary-600">{{ $k->mapel->nama_mapel ?? '-' }}</span>
                        <span class="badge-pill bg-orange-50 text-accent-600">Kelas {{ $k->kelas->nama_kelas ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-3 mt-3 text-[12.5px] text-ink-soft/60 flex-wrap">
                        <span>{{ $k->jumlah_soal }} soal</span>
                        <span class="w-1 h-1 rounded-full bg-ink-soft/30"></span>
                        <span>{{ $k->jumlah_selesai }} siswa selesai</span>
                        <span class="w-1 h-1 rounded-full bg-ink-soft/30"></span>
                        <span>{{ $k->durasi_menit }} menit</span>
                    </div>
                    <div class="flex items-center gap-2 mt-4 pt-4 border-t border-primary-50 mt-auto">
                        <a href="{{ route('guru.kuis.soal', $k->_id) }}" wire:navigate class="flex-1 text-center px-3 py-2 rounded-lg bg-primary-50 text-primary-700 text-[12.5px] font-semibold hover:bg-primary-100 transition-colors">Kelola Soal</a>
                        <a href="{{ route('guru.kuis.hasil', $k->_id) }}" wire:navigate class="flex-1 text-center px-3 py-2 rounded-lg bg-mint-50 text-mint-700 text-[12.5px] font-semibold hover:bg-mint-100 transition-colors">Lihat Hasil</a>
                        <button wire:click="edit('{{ $k->_id }}')" class="w-8 h-8 rounded-lg hover:bg-primary-50 flex items-center justify-center text-ink-soft/50 hover:text-primary-600 transition-colors shrink-0">{!! \App\Support\Icons::svg('pencil', 'w-4 h-4') !!}</button>
                        <button wire:click="confirmDelete('{{ $k->_id }}')" class="w-8 h-8 rounded-lg hover:bg-rose-50 flex items-center justify-center text-ink-soft/50 hover:text-danger transition-colors shrink-0">{!! \App\Support\Icons::svg('trash', 'w-4 h-4') !!}</button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <x-modal wire-show="showModal" :title="$editId ? 'Ubah Kuis' : 'Buat Kuis Baru'" maxWidth="max-w-2xl">
        <form wire:submit="save" class="space-y-4">
            <x-input name="judul_kuis" label="Judul Kuis" placeholder="Contoh: Kuis Bab 3 - Pecahan"/>
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
            <x-textarea name="deskripsi" label="Deskripsi (opsional)" :rows="2"/>
            <div class="grid sm:grid-cols-3 gap-4">
                <x-input name="waktu_mulai" type="datetime-local" label="Waktu Mulai"/>
                <x-input name="waktu_selesai" type="datetime-local" label="Waktu Selesai"/>
                <x-input name="durasi_menit" type="number" label="Durasi (menit)"/>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" wire:click="closeModal" class="px-4 py-2.5 rounded-lg text-[13.5px] font-semibold text-ink-soft/70 hover:bg-primary-50 transition-colors">Batal</button>
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>
    </x-modal>

    <x-confirm-delete wire-show="deleteId">Hapus kuis ini beserta semua soal &amp; jawaban?</x-confirm-delete>
</div>