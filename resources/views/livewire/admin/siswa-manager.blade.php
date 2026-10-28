<div class="space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 flex-1 min-w-0">
            <div class="relative w-full sm:max-w-xs">
                <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);display:flex;align-items:center;pointer-events:none;" class="text-ink-soft/40">{!! \App\Support\Icons::svg('search', 'w-4 h-4') !!}</span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama, NIS, atau email…"
                       style="height:40px;padding:0 12px 0 38px;line-height:40px;"
                       class="w-full rounded-lg border border-gray-300 bg-white text-[13px] text-ink placeholder:text-ink-soft/40 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
            </div>
            <div class="w-full sm:w-44">
                <x-select name="filterKelas" :live="true" placeholder="Semua Kelas" class="!py-1.5 !text-[12.5px]">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->_id }}">Kelas {{ $k->nama_kelas }}</option>
                    @endforeach
                </x-select>
            </div>
            @if($search !== '' || $filterKelas !== '')
                <button type="button" wire:click="resetFilters" class="text-[12.5px] font-semibold text-primary-600 hover:text-primary-700 whitespace-nowrap self-start sm:self-center">Reset</button>
            @endif
            <p class="text-[13px] text-ink-soft/60 whitespace-nowrap">{{ $items->total() }} siswa</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap self-start lg:self-auto">
            <button type="button" wire:click="openImport"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-gray-300 bg-white text-[13.5px] font-semibold text-ink hover:bg-gray-50 transition-colors">
                {!! \App\Support\Icons::svg('upload', 'w-4 h-4') !!} Import
            </button>
            <button type="button" wire:click="export" wire:loading.attr="disabled" wire:target="export"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-gray-300 bg-white text-[13.5px] font-semibold text-ink hover:bg-gray-50 transition-colors disabled:opacity-60">
                {!! \App\Support\Icons::svg('download', 'w-4 h-4') !!}
                <span wire:loading.remove wire:target="export">Export</span>
                <span wire:loading wire:target="export">Menyiapkan…</span>
            </button>
            <button wire:click="create" class="btn-primary">
                {!! \App\Support\Icons::svg('plus', 'w-4 h-4') !!} Tambah Siswa
            </button>
        </div>
    </div>

    @if($items->isEmpty())
        <div class="surface-card rounded-lg">
            @if($search !== '' || $filterKelas !== '')
                <x-empty-state icon="search" title="Siswa tidak ditemukan" subtitle="Coba kata kunci lain atau reset filter."/>
            @else
                <x-empty-state icon="user-graduate" title="Belum ada data siswa" subtitle="Klik “Tambah Siswa” untuk mendaftarkan siswa baru."/>
            @endif
        </div>
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

        @if($items->hasPages())
            <div class="flex items-center justify-between gap-3 flex-wrap px-1">
                <p class="text-[12.5px] text-ink-soft/60">
                    Menampilkan {{ $items->firstItem() }}–{{ $items->lastItem() }} dari {{ $items->total() }} siswa
                </p>
                <div class="flex items-center gap-1">
                    <button wire:click="previousPage" @disabled($items->onFirstPage())
                            class="px-3 py-1.5 rounded-lg text-[12.5px] font-semibold text-ink-soft/70 hover:bg-primary-50 disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-transparent transition-colors">
                        ← Sebelumnya
                    </button>
                    <span class="px-3 py-1.5 rounded-lg bg-primary-50 text-primary-700 text-[12.5px] font-semibold">
                        {{ $items->currentPage() }} / {{ $items->lastPage() }}
                    </span>
                    <button wire:click="nextPage" @disabled(! $items->hasMorePages())
                            class="px-3 py-1.5 rounded-lg text-[12.5px] font-semibold text-ink-soft/70 hover:bg-primary-50 disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-transparent transition-colors">
                        Selanjutnya →
                    </button>
                </div>
            </div>
        @endif
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

    <x-modal wire-show="showImportModal" title="Import Data Siswa" maxWidth="max-w-xl">
        <div class="space-y-4">
            @if($importResult)
                <div class="rounded-lg p-4 {{ $importResult['created'] > 0 ? 'bg-mint-50' : 'bg-gray-50' }}">
                    <p class="text-[14px] font-semibold text-ink">{{ $importResult['created'] }} siswa berhasil ditambahkan</p>
                    @if(count($importResult['errors']))
                        <p class="text-[12.5px] text-ink-soft/70 mt-0.5">{{ count($importResult['errors']) }} baris dilewati karena bermasalah:</p>
                    @endif
                </div>
                @if(count($importResult['errors']))
                    <div class="max-h-60 overflow-y-auto scroll-thin rounded-lg border border-gray-200 divide-y divide-gray-100">
                        @foreach($importResult['errors'] as $err)
                            <div class="px-3 py-2 text-[12.5px]">
                                <span class="font-semibold text-ink">Baris {{ $err['baris'] }}</span>
                                <span class="text-ink-soft/60">({{ $err['nama'] }})</span>
                                <p class="text-danger">{{ $err['pesan'] }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" wire:click="openImport" class="px-4 py-2.5 rounded-lg text-[13.5px] font-semibold text-ink-soft/70 hover:bg-primary-50 transition-colors">Import lagi</button>
                    <button type="button" wire:click="closeImport" class="btn-primary">Selesai</button>
                </div>
            @else
                <ol class="text-[13px] text-ink-soft/80 space-y-1.5 list-decimal pl-5">
                    <li>Unduh template, lalu isi data siswa di Excel (satu siswa per baris, hapus baris contoh).</li>
                    <li>Simpan sebagai <span class="font-semibold">CSV</span> (File → Save As → CSV).</li>
                    <li>Unggah file di bawah, lalu klik <span class="font-semibold">Import</span>.</li>
                </ol>

                <button type="button" wire:click="downloadTemplate"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border border-dashed border-primary-300 bg-primary-50/50 text-[13.5px] font-semibold text-primary-700 hover:bg-primary-50 transition-colors">
                    {!! \App\Support\Icons::svg('download', 'w-4 h-4') !!} Unduh Template CSV
                </button>

                <div class="text-[12px] text-ink-soft/70 bg-gray-50 rounded-lg px-3 py-2.5 space-y-1">
                    <p><span class="font-semibold text-ink">Kolom:</span> nama_siswa, nis, email, jenis_kelamin, kelas, alamat (opsional)</p>
                    <p><span class="font-semibold text-ink">jenis_kelamin:</span> Laki-laki / Perempuan (boleh L / P)</p>
                    <p><span class="font-semibold text-ink">kelas:</span> {{ $kelasList->pluck('nama_kelas')->implode(', ') ?: '-' }}</p>
                    <p>Kata sandi semua akun baru: <span class="font-mono font-semibold">password</span>. NIS/email yang sudah terdaftar akan dilewati.</p>
                </div>

                <div>
                    <label class="block text-[13px] font-semibold text-ink mb-1.5">File CSV</label>
                    <input type="file" wire:model="importFile" accept=".csv,text/csv"
                           class="block w-full text-[13px] text-ink border border-gray-300 rounded-lg cursor-pointer bg-white file:mr-3 file:py-2 file:px-3 file:border-0 file:bg-primary-50 file:text-primary-700 file:font-semibold hover:file:bg-primary-100">
                    <p wire:loading wire:target="importFile" class="text-[12px] text-ink-soft/60 mt-1.5">Mengunggah file…</p>
                    @error('importFile') <p class="text-danger text-[12.5px] mt-1.5 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" wire:click="closeImport" class="px-4 py-2.5 rounded-lg text-[13.5px] font-semibold text-ink-soft/70 hover:bg-primary-50 transition-colors">Batal</button>
                    <button type="button" wire:click="import" wire:loading.attr="disabled" wire:target="import,importFile" class="btn-primary disabled:opacity-60">
                        <span wire:loading.remove wire:target="import">Import</span>
                        <span wire:loading wire:target="import">Memproses…</span>
                    </button>
                </div>
            @endif
        </div>
    </x-modal>

    <x-confirm-delete wire-show="deleteId">Hapus data siswa ini?</x-confirm-delete>
</div>