<div class="space-y-6">
    <div class="flex items-center justify-between gap-2 flex-wrap">
        <p class="text-[13px] text-ink-soft/60 shrink-0">{{ $items->count() }} pengumuman</p>
        <button wire:click="create" class="btn-primary">
            {!! \App\Support\Icons::svg('plus', 'w-4 h-4') !!} Buat Pengumuman
        </button>
    </div>

    <div class="surface-card rounded-lg p-4">
        <div class="flex items-end gap-3 flex-wrap">
            <div class="w-full sm:w-44">
                <x-date-picker name="filterDari" :live="true" label="Dari Tanggal" placeholder="Semua tanggal"/>
            </div>
            <div class="w-full sm:w-44">
                <x-date-picker name="filterSampai" :live="true" label="Sampai Tanggal" placeholder="Semua tanggal"/>
            </div>
            @if($filterDari || $filterSampai)
                <button wire:click="resetFilter" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-lg bg-rose-50 text-danger text-[13px] font-semibold hover:bg-rose-100 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    Reset Filter
                </button>
            @endif
        </div>
    </div>

    @if($items->isEmpty())
        <div class="surface-card rounded-lg">
            @if($filterDari || $filterSampai)
                <x-empty-state icon="bell" title="Tidak ada pengumuman di rentang ini" subtitle="Coba ubah atau reset filter tanggal."/>
            @else
                <x-empty-state icon="bell" title="Belum ada pengumuman" subtitle="Pengumuman yang Anda buat akan langsung tampil di dashboard seluruh siswa."/>
            @endif
        </div>
    @else
        <div class="space-y-3">
            @foreach($items as $i => $p)
                @php $isMine = (string) $p->guru_id === $myGuruId; @endphp
                <div wire:key="pengumuman-{{ $p->_id }}" class="surface-card surface-card-hover row-card rounded-lg p-4 sm:p-5 animate-fade-up" style="--stagger: {{ $i }}; --row-accent: var(--color-primary-400)">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center shrink-0 ring-4 ring-white">
                            {!! \App\Support\Icons::svg('bell', 'w-5 h-5') !!}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-display font-semibold text-ink">{{ $p->judul }}</p>
                            <p class="text-[13.5px] text-ink-soft/70 mt-1 whitespace-pre-line">{{ $p->isi }}</p>
                            <p class="text-[11.5px] text-ink-soft/50 mt-2">oleh {{ $p->guru->nama_guru ?? '-' }} · {{ $p->created_at?->diffForHumans() }}</p>
                        </div>
                        @if($isMine)
                            <div class="flex items-center gap-1 shrink-0">
                                <button wire:click="edit('{{ $p->_id }}')" class="w-7 h-7 rounded-lg hover:bg-primary-50 flex items-center justify-center text-ink-soft/50 hover:text-primary-600 transition-colors">{!! \App\Support\Icons::svg('pencil', 'w-3.5 h-3.5') !!}</button>
                                <button wire:click="confirmDelete('{{ $p->_id }}')" class="w-7 h-7 rounded-lg hover:bg-rose-50 flex items-center justify-center text-ink-soft/50 hover:text-danger transition-colors">{!! \App\Support\Icons::svg('trash', 'w-3.5 h-3.5') !!}</button>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <x-modal wire-show="showModal" :title="$editId ? 'Ubah Pengumuman' : 'Buat Pengumuman Baru'" maxWidth="max-w-xl">
        <form wire:submit="save" class="space-y-4">
            <x-input name="judul" label="Judul" placeholder="Contoh: Libur Sekolah Minggu Depan"/>
            <x-textarea name="isi" label="Isi Pengumuman" :rows="5" placeholder="Tulis pengumuman untuk seluruh siswa..."/>
            <p class="text-[12px] text-ink-soft/60 bg-primary-50/60 rounded-lg px-3 py-2">Pengumuman ini akan langsung tampil di dashboard dan notifikasi seluruh siswa.</p>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" wire:click="closeModal" class="px-4 py-2.5 rounded-lg text-[13.5px] font-semibold text-ink-soft/70 hover:bg-primary-50 transition-colors">Batal</button>
                <button type="submit" class="btn-primary">{{ $editId ? 'Simpan' : 'Broadcast Sekarang' }}</button>
            </div>
        </form>
    </x-modal>

    <x-confirm-delete wire-show="deleteId">Hapus pengumuman ini?</x-confirm-delete>
</div>