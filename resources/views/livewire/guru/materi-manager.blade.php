<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <p class="text-[13px] text-ink-soft/60">{{ $items->count() }} materi</p>
            <div class="w-44">
                <x-select name="filterKelas" :live="true" placeholder="Semua Kelas" class="!py-1.5 !text-[12.5px]">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->_id }}">Kelas {{ $k->nama_kelas }}</option>
                    @endforeach
                </x-select>
            </div>
        </div>
        <button wire:click="create" class="btn-primary self-start">
            {!! \App\Support\Icons::svg('plus', 'w-4 h-4') !!} Unggah Materi
        </button>
    </div>

    @if($items->isEmpty())
        <div class="surface-card rounded-lg"><x-empty-state icon="book" title="Belum ada materi" subtitle="Klik “Unggah Materi” untuk membagikan materi pertama Anda."/></div>
    @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($items as $i => $m)
                <div wire:key="materi-{{ $m->_id }}" class="surface-card surface-card-hover rounded-lg p-5 animate-fade-up flex flex-col" style="--stagger: {{ $i }}">
                    <div class="flex items-start justify-between">
                        <div class="w-11 h-11 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center ring-4 ring-white">{!! \App\Support\Icons::svg('book', 'w-5 h-5') !!}</div>
                        <div class="flex items-center gap-1">
                            <button wire:click="edit('{{ $m->_id }}')" class="w-7 h-7 rounded-lg hover:bg-primary-50 flex items-center justify-center text-ink-soft/50 hover:text-primary-600 transition-colors">{!! \App\Support\Icons::svg('pencil', 'w-3.5 h-3.5') !!}</button>
                            <button wire:click="confirmDelete('{{ $m->_id }}')" class="w-7 h-7 rounded-lg hover:bg-rose-50 flex items-center justify-center text-ink-soft/50 hover:text-danger transition-colors">{!! \App\Support\Icons::svg('trash', 'w-3.5 h-3.5') !!}</button>
                        </div>
                    </div>
                    <p class="mt-4 font-display font-semibold text-ink line-clamp-2">{{ $m->judul_materi }}</p>
                    <p class="text-[12.5px] text-ink-soft/60 mt-1 line-clamp-2 flex-1">{{ Str::limit(strip_tags($m->isi_materi), 90) }}</p>
                    <div class="mt-3 flex items-center gap-2 flex-wrap">
                        <span class="badge-pill bg-mint-50 text-mint-700">{{ $m->mapel->nama_mapel ?? '-' }}</span>
                        <span class="badge-pill bg-primary-50 text-primary-600">Kelas {{ $m->kelas->nama_kelas ?? '-' }}</span>
                        @if($m->hasFile())
                            <span class="badge-pill bg-amber-50 text-amber-700">
                                {!! \App\Support\Icons::svg('paperclip', 'w-3 h-3') !!} {{ strtoupper($m->file_extension) }}
                            </span>
                        @endif
                    </div>
                    @unless($m->modul_id)
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <a href="{{ route('guru.modul.index', ['materi_id' => $m->_id]) }}" wire:navigate
                           class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-primary-50 text-primary-700 text-[12.5px] font-semibold hover:bg-primary-100 transition-colors">
                            {!! \App\Support\Icons::svg('academic-cap', 'w-4 h-4') !!} Buat Modul dari Materi Ini
                        </a>
                    </div>
                    @endunless
                </div>
            @endforeach
        </div>
    @endif

    <x-modal wire-show="showModal" :title="$editId ? 'Ubah Materi' : 'Unggah Materi Baru'" maxWidth="max-w-2xl">
        <form wire:submit="save" class="space-y-4">
            <x-input name="judul_materi" label="Judul Materi" placeholder="Contoh: Pecahan Sederhana"/>
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
            <x-textarea name="isi_materi" label="Isi Materi" :rows="6" placeholder="Tulis penjelasan materi di sini..."/>

            <div>
                <label class="block text-[13px] font-semibold text-ink mb-1.5">Lampiran Bahan Ajar (opsional)</label>

                @if($existingFilePath && ! $file)
                    <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 bg-gray-50">
                        <div class="w-9 h-9 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center shrink-0">
                            {!! \App\Support\Icons::svg('file-pdf', 'w-4 h-4') !!}
                        </div>
                        <p class="text-[13px] font-medium text-ink truncate flex-1">{{ $existingFileName }}</p>
                        <button type="button" wire:click="removeExistingFile" class="text-danger text-[12px] font-semibold hover:underline shrink-0">Hapus</button>
                    </div>
                @else
                    <input type="file" wire:model="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png"
                           class="block w-full text-[13px] text-ink-soft file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-[13px] file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 border border-gray-300 rounded-lg cursor-pointer bg-white">

                    <div wire:loading wire:target="file" class="flex items-center gap-2 mt-2 text-[12.5px] text-primary-600">
                        <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Mengunggah...
                    </div>

                    @if($file)
                        <p class="text-[12.5px] text-mint-600 mt-2 flex items-center gap-1.5">
                            {!! \App\Support\Icons::svg('check-badge', 'w-3.5 h-3.5') !!} {{ $file->getClientOriginalName() }} siap disimpan
                        </p>
                    @endif
                @endif

                @error('file') <p class="text-danger text-[12px] mt-1.5 font-medium">{{ $message }}</p> @enderror
                <p class="text-[11.5px] text-ink-soft mt-1.5">Format: PDF, Word, PowerPoint, Excel, atau gambar. Maksimal 10MB.</p>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" wire:click="closeModal" class="px-4 py-2.5 rounded-lg text-[13.5px] font-semibold text-ink-soft/70 hover:bg-primary-50 transition-colors">Batal</button>
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>
    </x-modal>

    <x-confirm-delete wire-show="deleteId">Hapus materi ini?</x-confirm-delete>
</div>