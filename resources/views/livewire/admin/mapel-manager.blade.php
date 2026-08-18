<div class="space-y-6">
    <div class="flex items-center justify-between gap-2 flex-wrap">
        <p class="text-[13px] text-ink-soft/60 shrink-0">{{ $items->count() }} mata pelajaran</p>
        <button wire:click="create" class="btn-primary">
            {!! \App\Support\Icons::svg('plus', 'w-4 h-4') !!} Tambah Mapel
        </button>
    </div>

    @if($items->isEmpty())
        <div class="surface-card rounded-lg"><x-empty-state icon="book" title="Belum ada mata pelajaran" subtitle="Klik “Tambah Mapel” untuk mulai menambahkan data."/></div>
    @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($items as $i => $m)
                <div wire:key="mapel-{{ $m->_id }}" class="surface-card surface-card-hover rounded-lg p-5 animate-fade-up" style="--stagger: {{ $i }}">
                    <div class="flex items-start justify-between">
                        <div class="w-11 h-11 rounded-full bg-mint-50 text-mint-600 flex items-center justify-center ring-4 ring-white">
                            {!! \App\Support\Icons::svg($m->icon ?? 'book', 'w-5 h-5') !!}
                        </div>
                        <div class="flex items-center gap-1">
                            <button wire:click="edit('{{ $m->_id }}')" class="w-7 h-7 rounded-lg hover:bg-primary-50 flex items-center justify-center text-ink-soft/50 hover:text-primary-600 transition-colors">{!! \App\Support\Icons::svg('pencil', 'w-3.5 h-3.5') !!}</button>
                            <button wire:click="confirmDelete('{{ $m->_id }}')" class="w-7 h-7 rounded-lg hover:bg-rose-50 flex items-center justify-center text-ink-soft/50 hover:text-danger transition-colors">{!! \App\Support\Icons::svg('trash', 'w-3.5 h-3.5') !!}</button>
                        </div>
                    </div>
                    <p class="mt-4 font-display font-semibold text-ink">{{ $m->nama_mapel }}</p>
                    <p class="text-[12.5px] text-ink-soft/60 mt-0.5 line-clamp-2">{{ $m->deskripsi ?: 'Tidak ada deskripsi.' }}</p>
                </div>
            @endforeach
        </div>
    @endif

    <x-modal wire-show="showModal" :title="$editId ? 'Ubah Mata Pelajaran' : 'Tambah Mata Pelajaran'">
        <form wire:submit="save" class="space-y-4">
            <x-input name="nama_mapel" label="Nama Mata Pelajaran" placeholder="Contoh: Matematika"/>
            <x-textarea name="deskripsi" label="Deskripsi (opsional)" placeholder="Deskripsi singkat mata pelajaran"/>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" wire:click="closeModal" class="px-4 py-2.5 rounded-lg text-[13.5px] font-semibold text-ink-soft/70 hover:bg-primary-50 transition-colors">Batal</button>
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>
    </x-modal>

    <x-confirm-delete wire-show="deleteId">Hapus mata pelajaran ini?</x-confirm-delete>
</div>