<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <p class="text-[13px] text-ink-soft/60">{{ $items->count() }} siswa</p>
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
            {!! \App\Support\Icons::svg('plus', 'w-4 h-4') !!} Tambah Siswa
        </button>
    </div>

    @if($items->isEmpty())
        <div class="surface-card rounded-lg"><x-empty-state icon="user-graduate" title="Belum ada data siswa" subtitle="Klik “Tambah Siswa” untuk mendaftarkan siswa baru."/></div>
    @else
        <div class="surface-card rounded-lg overflow-hidden">
            <div class="overflow-x-auto scroll-thin">
            <table class="w-full text-left table-modern">
                <thead>
                    <tr class="border-b border-primary-100/70 text-[12px] text-ink-soft/60 uppercase tracking-wide">
                        <th class="px-5 py-3.5 font-semibold">Nama</th>
                        <th class="px-5 py-3.5 font-semibold hidden sm:table-cell">NIS</th>
                        <th class="px-5 py-3.5 font-semibold">Kelas</th>
                        <th class="px-5 py-3.5 font-semibold hidden md:table-cell">Email</th>
                        <th class="px-5 py-3.5 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $i => $s)
                        <tr wire:key="siswa-{{ $s->_id }}" class="border-b border-primary-50 last:border-0 hover:bg-primary-50/40 transition-colors animate-fade-up" style="--stagger: {{ $i }}">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-mint-500 to-primary-500 flex items-center justify-center text-white text-[12px] font-display font-bold shrink-0">
                                        {{ strtoupper(substr($s->nama_siswa, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-ink text-[13.5px]">{{ $s->nama_siswa }}</p>
                                        <p class="text-[11.5px] text-ink-soft/50">{{ $s->jenis_kelamin }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-[13px] text-ink-soft/70 font-mono hidden sm:table-cell">{{ $s->nis }}</td>
                            <td class="px-5 py-3.5"><span class="badge-pill bg-primary-50 text-primary-600">{{ $s->kelas->nama_kelas ?? '-' }}</span></td>
                            <td class="px-5 py-3.5 text-[13px] text-ink-soft/70 hidden md:table-cell">{{ $s->user->email ?? '-' }}</td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="edit('{{ $s->_id }}')" class="w-7 h-7 rounded-lg hover:bg-primary-50 flex items-center justify-center text-ink-soft/50 hover:text-primary-600 transition-colors">{!! \App\Support\Icons::svg('pencil', 'w-3.5 h-3.5') !!}</button>
                                    <button wire:click="confirmDelete('{{ $s->_id }}')" class="w-7 h-7 rounded-lg hover:bg-rose-50 flex items-center justify-center text-ink-soft/50 hover:text-danger transition-colors">{!! \App\Support\Icons::svg('trash', 'w-3.5 h-3.5') !!}</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        </div>
    @endif

    <x-modal wire-show="showModal" :title="$editId ? 'Ubah Data Siswa' : 'Tambah Siswa Baru'" maxWidth="max-w-xl">
        <form wire:submit="save" class="space-y-4">
            <div class="grid sm:grid-cols-2 gap-4">
                <x-input name="nama_siswa" label="Nama Lengkap" placeholder="Nama siswa"/>
                <x-input name="nis" label="NIS" placeholder="Nomor Induk Siswa"/>
            </div>
            <x-input name="email" type="email" label="Email (untuk login)" placeholder="nama@siswa.sdn102040.sch.id"/>
            <div class="grid sm:grid-cols-2 gap-4">
                <x-select name="jenis_kelamin" label="Jenis Kelamin">
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </x-select>
                <x-select name="kelas_id" label="Kelas">
                    @foreach($kelasList as $k)
                        <option value="{{ $k->_id }}">Kelas {{ $k->nama_kelas }}</option>
                    @endforeach
                </x-select>
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

    <x-confirm-delete wire-show="deleteId">Hapus data siswa ini?</x-confirm-delete>
</div>