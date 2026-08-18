<div class="space-y-6">
    <div class="flex items-center justify-between gap-2 flex-wrap">
        <p class="text-[13px] text-ink-soft/60 shrink-0">{{ $items->count() }} guru terdaftar</p>
        <button wire:click="create" class="btn-primary">
            {!! \App\Support\Icons::svg('plus', 'w-4 h-4') !!} Tambah Guru
        </button>
    </div>

    @if($items->isEmpty())
        <div class="surface-card rounded-lg"><x-empty-state icon="briefcase" title="Belum ada data guru" subtitle="Klik “Tambah Guru” untuk mendaftarkan guru baru."/></div>
    @else
        <div class="surface-card rounded-lg overflow-hidden">
            <div class="overflow-x-auto scroll-thin">
            <table class="w-full text-left table-modern">
                <thead>
                    <tr class="border-b border-primary-100/70 text-[12px] text-ink-soft/60 uppercase tracking-wide">
                        <th class="px-5 py-3.5 font-semibold">Nama</th>
                        <th class="px-5 py-3.5 font-semibold hidden sm:table-cell">NIP</th>
                        <th class="px-5 py-3.5 font-semibold hidden md:table-cell">Email</th>
                        <th class="px-5 py-3.5 font-semibold hidden lg:table-cell">No. HP</th>
                        <th class="px-5 py-3.5 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $i => $g)
                        <tr wire:key="guru-{{ $g->_id }}" class="border-b border-primary-50 last:border-0 hover:bg-primary-50/40 transition-colors animate-fade-up" style="--stagger: {{ $i }}">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-accent-400 flex items-center justify-center text-white text-[12px] font-display font-bold shrink-0">
                                        {{ strtoupper(substr($g->nama_guru, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-ink text-[13.5px]">{{ $g->nama_guru }}</p>
                                        <p class="text-[11.5px] text-ink-soft/50">{{ $g->jenis_kelamin }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-[13px] text-ink-soft/70 font-mono hidden sm:table-cell">{{ $g->nip }}</td>
                            <td class="px-5 py-3.5 text-[13px] text-ink-soft/70 hidden md:table-cell">{{ $g->user->email ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-[13px] text-ink-soft/70 hidden lg:table-cell">{{ $g->no_hp ?: '-' }}</td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="edit('{{ $g->_id }}')" class="w-7 h-7 rounded-lg hover:bg-primary-50 flex items-center justify-center text-ink-soft/50 hover:text-primary-600 transition-colors">{!! \App\Support\Icons::svg('pencil', 'w-3.5 h-3.5') !!}</button>
                                    <button wire:click="confirmDelete('{{ $g->_id }}')" class="w-7 h-7 rounded-lg hover:bg-rose-50 flex items-center justify-center text-ink-soft/50 hover:text-danger transition-colors">{!! \App\Support\Icons::svg('trash', 'w-3.5 h-3.5') !!}</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        </div>
    @endif

    <x-modal wire-show="showModal" :title="$editId ? 'Ubah Data Guru' : 'Tambah Guru Baru'" maxWidth="max-w-xl">
        <form wire:submit="save" class="space-y-4">
            <div class="grid sm:grid-cols-2 gap-4">
                <x-input name="nama_guru" label="Nama Lengkap" placeholder="Nama guru"/>
                <x-input name="nip" label="NIP" placeholder="Nomor Induk Pegawai"/>
            </div>
            <x-input name="email" type="email" label="Email (untuk login)" placeholder="nama@sdn102040.sch.id"/>
            <div class="grid sm:grid-cols-2 gap-4">
                <x-select name="jenis_kelamin" label="Jenis Kelamin">
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </x-select>
                <x-input name="no_hp" label="No. HP (opsional)" placeholder="08xxxxxxxxxx"/>
            </div>
            <x-textarea name="alamat" label="Alamat (opsional)"/>
            @unless($editId)
                <p class="text-[12px] text-ink-soft/60 bg-primary-50/60 rounded-lg px-3 py-2">Kata sandi login default: <span class="font-mono font-semibold">password</span></p>
            @endunless
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" wire:click="closeModal" class="px-4 py-2.5 rounded-lg text-[13.5px] font-semibold text-ink-soft/70 hover:bg-primary-50 transition-colors">Batal</button>
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>
    </x-modal>

    <x-confirm-delete wire-show="deleteId">Hapus data guru ini?</x-confirm-delete>
</div>