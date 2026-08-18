<div class="space-y-6">
    <div class="flex items-center justify-between gap-2 flex-wrap">
        <p class="text-[13px] text-ink-soft/60 shrink-0">{{ $items->count() }} kelas terdaftar</p>
        <button wire:click="create" class="btn-primary">
            {!! \App\Support\Icons::svg('plus', 'w-4 h-4') !!} Tambah Kelas
        </button>
    </div>

    @if($items->isEmpty())
        <div class="surface-card rounded-lg"><x-empty-state icon="grid" title="Belum ada kelas" subtitle="Klik “Tambah Kelas” untuk mulai menambahkan data kelas."/></div>
    @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($items as $i => $k)
                <div wire:key="kelas-{{ $k->_id }}" class="surface-card surface-card-hover rounded-lg p-5 animate-fade-up" style="--stagger: {{ $i }}">
                    <div class="flex items-start justify-between">
                        <div class="w-11 h-11 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center font-display font-bold text-sm ring-4 ring-white">
                            {{ $k->nama_kelas }}
                        </div>
                        <div class="flex items-center gap-1">
                            <button wire:click="edit('{{ $k->_id }}')" class="w-7 h-7 rounded-lg hover:bg-primary-50 flex items-center justify-center text-ink-soft/50 hover:text-primary-600 transition-colors">
                                {!! \App\Support\Icons::svg('pencil', 'w-3.5 h-3.5') !!}
                            </button>
                            <button wire:click="confirmDelete('{{ $k->_id }}')" class="w-7 h-7 rounded-lg hover:bg-rose-50 flex items-center justify-center text-ink-soft/50 hover:text-danger transition-colors">
                                {!! \App\Support\Icons::svg('trash', 'w-3.5 h-3.5') !!}
                            </button>
                        </div>
                    </div>
                    <p class="mt-4 font-display font-semibold text-ink">Kelas {{ $k->nama_kelas }}</p>
                    <p class="text-[12.5px] text-ink-soft/60">Tingkat {{ $k->tingkat }}</p>
                    <div class="mt-3 badge-pill bg-mint-50 text-mint-700">{{ $k->siswa_count }} siswa</div>
                </div>
            @endforeach
        </div>
    @endif

    <x-modal wire-show="showModal" :title="$editId ? 'Ubah Kelas' : 'Tambah Kelas'">
        <form wire:submit="save" class="space-y-4">
            <x-input name="nama_kelas" label="Nama Kelas" placeholder="Contoh: V atau 5A"/>
            <x-input name="tingkat" label="Tingkat" placeholder="Contoh: 5"/>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" wire:click="closeModal" class="px-4 py-2.5 rounded-lg text-[13.5px] font-semibold text-ink-soft/70 hover:bg-primary-50 transition-colors">Batal</button>
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>
    </x-modal>

    <x-confirm-delete wire-show="deleteId">Hapus kelas ini?</x-confirm-delete>
</div>