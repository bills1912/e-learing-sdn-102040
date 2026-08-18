<div class="space-y-6">
    <div class="flex items-center justify-between gap-2 flex-wrap">
        <a href="{{ route('guru.modul.index') }}" wire:navigate class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-primary-600 hover:underline">
            ← Kembali ke daftar modul
        </a>
        <button wire:click="create" class="btn-primary">
            {!! \App\Support\Icons::svg('plus', 'w-4 h-4') !!} Tambah Soal
        </button>
    </div>

    <div class="surface-card rounded-lg p-4 sm:p-5 flex flex-wrap items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">{!! \App\Support\Icons::svg('academic-cap', 'w-5 h-5') !!}</div>
        <div class="min-w-0">
            <p class="font-display font-semibold text-ink truncate">{{ $modul->judul_modul }}</p>
            <p class="text-[12.5px] text-ink-soft/60">{{ $modul->mapel->nama_mapel ?? '-' }} · Kelas {{ $modul->kelas->nama_kelas ?? '-' }}</p>
        </div>
    </div>

    {{-- Tabs: Pre-Test / Post-Test managed on the same page --}}
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div class="surface-card rounded-lg p-1.5 inline-flex gap-1">
            <button wire:click="switchTab('pretest')"
                    class="px-4 py-2 rounded-lg text-[13.5px] font-semibold transition-colors {{ $activeTab === 'pretest' ? 'bg-primary-600 text-white' : 'text-ink-soft/70 hover:bg-primary-50' }}">
                Pre-Test <span class="opacity-70">({{ $pretestCount }})</span>
            </button>
            <button wire:click="switchTab('posttest')"
                    class="px-4 py-2 rounded-lg text-[13.5px] font-semibold transition-colors {{ $activeTab === 'posttest' ? 'bg-primary-600 text-white' : 'text-ink-soft/70 hover:bg-primary-50' }}">
                Post-Test <span class="opacity-70">({{ $posttestCount }})</span>
            </button>
        </div>

        <button wire:click="editDurasi" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-white border border-gray-200 hover:border-primary-300 hover:bg-primary-50/40 transition-colors text-[13px] font-medium text-ink">
            {!! \App\Support\Icons::svg('clock', 'w-4 h-4 text-primary-500') !!}
            Durasi: <span class="font-semibold">{{ $currentKuis?->durasi_menit ?? 15 }} menit</span>
            {!! \App\Support\Icons::svg('pencil', 'w-3.5 h-3.5 text-ink-soft/50') !!}
        </button>
    </div>

    @if($items->isEmpty())
        <div class="surface-card rounded-lg">
            <x-empty-state icon="quiz" :title="'Belum ada soal '.($activeTab === 'pretest' ? 'Pre-Test' : 'Post-Test')" subtitle="Tambahkan soal pilihan ganda pertama untuk tahap ini."/>
        </div>
    @else
        <div class="space-y-3">
            @foreach($items as $i => $s)
                <div wire:key="soal-{{ $activeTab }}-{{ $s->_id }}" class="surface-card surface-card-hover rounded-lg p-5 animate-fade-up" style="--stagger: {{ $i }}">
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center font-display font-bold text-[13px] shrink-0">{{ $i + 1 }}</div>
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-ink text-[14.5px]">{{ $s->pertanyaan }}</p>
                            <div class="grid sm:grid-cols-2 gap-2 mt-3">
                                @foreach(['A' => $s->pilihan_a, 'B' => $s->pilihan_b, 'C' => $s->pilihan_c, 'D' => $s->pilihan_d] as $opt => $text)
                                    <div class="flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] {{ $s->jawaban_benar === $opt ? 'bg-mint-50 text-mint-700 font-semibold' : 'bg-primary-50/40 text-ink-soft/70' }}">
                                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[11px] font-bold shrink-0 {{ $s->jawaban_benar === $opt ? 'bg-mint-500 text-white' : 'bg-white text-ink-soft/50' }}">{{ $opt }}</span>
                                        {{ $text }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <button wire:click="edit('{{ $s->_id }}')" class="w-7 h-7 rounded-lg hover:bg-primary-50 flex items-center justify-center text-ink-soft/50 hover:text-primary-600 transition-colors">{!! \App\Support\Icons::svg('pencil', 'w-3.5 h-3.5') !!}</button>
                            <button wire:click="confirmDelete('{{ $s->_id }}')" class="w-7 h-7 rounded-lg hover:bg-rose-50 flex items-center justify-center text-ink-soft/50 hover:text-danger transition-colors">{!! \App\Support\Icons::svg('trash', 'w-3.5 h-3.5') !!}</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <x-modal wire-show="showModal" :title="($editId ? 'Ubah Soal' : 'Tambah Soal').' — '.($activeTab === 'pretest' ? 'Pre-Test' : 'Post-Test')" maxWidth="max-w-2xl">
        <form wire:submit="save" class="space-y-4">
            <x-textarea name="pertanyaan" label="Pertanyaan" :rows="3" placeholder="Tulis pertanyaan di sini..."/>
            <div class="grid sm:grid-cols-2 gap-4">
                <x-input name="pilihan_a" label="Pilihan A"/>
                <x-input name="pilihan_b" label="Pilihan B"/>
                <x-input name="pilihan_c" label="Pilihan C"/>
                <x-input name="pilihan_d" label="Pilihan D"/>
            </div>
            <x-select name="jawaban_benar" label="Jawaban Benar">
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
            </x-select>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" wire:click="closeModal" class="px-4 py-2.5 rounded-lg text-[13.5px] font-semibold text-ink-soft/70 hover:bg-primary-50 transition-colors">Batal</button>
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>
    </x-modal>

    <x-modal wire-show="showDurasiModal" close="closeModal" title="Ubah Durasi Waktu Pengerjaan">
        <form wire:submit="saveDurasi" class="space-y-4">
            <p class="text-[13px] text-ink-soft/60">Durasi untuk <span class="font-semibold text-ink">{{ $activeTab === 'pretest' ? 'Pre-Test' : 'Post-Test' }}</span> modul ini.</p>
            <x-input name="durasi_menit" type="number" label="Durasi (menit)" min="1" max="180"/>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" wire:click="closeModal" class="px-4 py-2.5 rounded-lg text-[13.5px] font-semibold text-ink-soft/70 hover:bg-primary-50 transition-colors">Batal</button>
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>
    </x-modal>

    <x-confirm-delete wire-show="deleteId">Hapus soal ini?</x-confirm-delete>
</div>