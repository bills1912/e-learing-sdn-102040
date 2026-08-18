<div class="space-y-6">
    <div class="flex items-center justify-between gap-2 flex-wrap">
        <p class="text-[13px] text-ink-soft/60 shrink-0">{{ $items->count() }} modul</p>
        <button wire:click="create" class="btn-primary">
            {!! \App\Support\Icons::svg('plus', 'w-4 h-4') !!} Buat Modul
        </button>
    </div>

    @if($items->isEmpty())
        <div class="surface-card rounded-lg"><x-empty-state icon="academic-cap" title="Belum ada modul" subtitle="Modul berisi pre-test, materi, dan post-test yang harus diselesaikan siswa secara berurutan."/></div>
    @else
        <div class="space-y-4">
            @foreach($items as $i => $m)
                <div wire:key="modul-{{ $m->_id }}" class="surface-card surface-card-hover rounded-lg p-4 sm:p-5 animate-fade-up" style="--stagger: {{ $i }}">
                    <div class="flex items-start justify-between gap-2 flex-wrap">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <span class="badge-pill bg-primary-50 text-primary-600 whitespace-nowrap">{{ $m->mapel->nama_mapel ?? '-' }}</span>
                                <span class="badge-pill bg-orange-50 text-accent-600 whitespace-nowrap">Kelas {{ $m->kelas->nama_kelas ?? '-' }}</span>
                            </div>
                            <p class="font-display font-semibold text-ink truncate">{{ $m->judul_modul }}</p>
                            @if($m->deskripsi)
                                <p class="text-[12.5px] text-ink-soft/60 mt-0.5 line-clamp-1">{{ $m->deskripsi }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <button wire:click="edit('{{ $m->_id }}')" class="w-8 h-8 rounded-lg hover:bg-primary-50 flex items-center justify-center text-ink-soft/50 hover:text-primary-600 transition-colors">{!! \App\Support\Icons::svg('pencil', 'w-4 h-4') !!}</button>
                            <button wire:click="confirmDelete('{{ $m->_id }}')" class="w-8 h-8 rounded-lg hover:bg-rose-50 flex items-center justify-center text-ink-soft/50 hover:text-danger transition-colors">{!! \App\Support\Icons::svg('trash', 'w-4 h-4') !!}</button>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-3 gap-3 mt-4">
                        <a href="{{ route('guru.modul.soal', ['modulId' => $m->_id, 'tab' => 'pretest']) }}" wire:navigate
                           class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-primary-300 hover:bg-primary-50/30 transition-colors">
                            <div class="w-9 h-9 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">{!! \App\Support\Icons::svg('quiz', 'w-4 h-4') !!}</div>
                            <div class="min-w-0">
                                <p class="text-[13px] font-semibold text-ink">Pre-Test</p>
                                <p class="text-[11.5px] text-ink-soft/60">{{ $m->jumlah_soal_pretest }} soal</p>
                            </div>
                        </a>
                        <a href="{{ route('guru.modul.materi', $m->materi_id) }}" wire:navigate
                           class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-mint-300 hover:bg-mint-50/30 transition-colors">
                            <div class="w-9 h-9 rounded-lg bg-mint-50 text-mint-600 flex items-center justify-center shrink-0">{!! \App\Support\Icons::svg('book', 'w-4 h-4') !!}</div>
                            <div class="min-w-0">
                                <p class="text-[13px] font-semibold text-ink">Materi</p>
                                <p class="text-[11.5px] text-ink-soft/60">{{ $m->materiObj && ($m->materiObj->isi_materi || $m->materiObj->hasFile()) ? 'Sudah diisi' : 'Belum diisi' }}</p>
                            </div>
                        </a>
                        <a href="{{ route('guru.modul.soal', ['modulId' => $m->_id, 'tab' => 'posttest']) }}" wire:navigate
                           class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-accent-300 hover:bg-orange-50/30 transition-colors">
                            <div class="w-9 h-9 rounded-lg bg-orange-50 text-accent-600 flex items-center justify-center shrink-0">{!! \App\Support\Icons::svg('quiz', 'w-4 h-4') !!}</div>
                            <div class="min-w-0">
                                <p class="text-[13px] font-semibold text-ink">Post-Test</p>
                                <p class="text-[11.5px] text-ink-soft/60">{{ $m->jumlah_soal_posttest }} soal</p>
                            </div>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <x-modal wire-show="showModal" :title="$editId ? 'Ubah Modul' : 'Buat Modul Baru'" maxWidth="max-w-xl">
        <form wire:submit="save" class="space-y-4">
            @if($editId)
                <div>
                    <label class="block text-[13px] font-semibold text-ink mb-1.5">Materi</label>
                    <div class="px-3.5 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-[14px] text-ink-soft">
                        {{ $editingMateriTitle }} <span class="text-[12px]">(tidak dapat diganti)</span>
                    </div>
                </div>
            @else
                @if($availableMateri->isEmpty())
                    <div class="p-4 rounded-lg bg-amber-50 text-amber-700 text-[13px]">
                        Semua materi Anda sudah menjadi bagian dari modul lain, atau Anda belum membuat materi sama sekali.
                        <a href="{{ route('guru.materi.index') }}" wire:navigate class="font-semibold underline">Buat materi baru dulu di menu Materi →</a>
                    </div>
                @else
                    <x-select name="materi_id" label="Pilih Materi">
                        @foreach($availableMateri as $mt)
                            <option value="{{ $mt->_id }}">{{ $mt->judul_materi }}</option>
                        @endforeach
                    </x-select>
                    <p class="text-[11.5px] text-ink-soft/60 -mt-2">Mata pelajaran dan kelas modul akan mengikuti materi yang dipilih.</p>
                @endif
            @endif

            <x-textarea name="deskripsi" label="Deskripsi Singkat (opsional)" :rows="2"/>

            @unless($editId)
                <p class="text-[12px] text-ink-soft/60 bg-primary-50/60 rounded-lg px-3 py-2">Setelah disimpan, Anda akan bisa menambahkan soal pre-test dan soal post-test dari card modul ini. Materi yang sudah ada akan dipakai langsung, tidak dibuat baru.</p>
            @endunless

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" wire:click="closeModal" class="px-4 py-2.5 rounded-lg text-[13.5px] font-semibold text-ink-soft/70 hover:bg-primary-50 transition-colors">Batal</button>
                @if($editId || $availableMateri->isNotEmpty())
                    <button type="submit" class="btn-primary">Simpan</button>
                @endif
            </div>
        </form>
    </x-modal>

    <x-confirm-delete wire-show="deleteId">Hapus modul ini beserta pre-test & post-test-nya? (Materi tetap tersimpan)</x-confirm-delete>
</div>