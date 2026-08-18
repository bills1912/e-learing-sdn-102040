<div class="space-y-6 max-w-2xl">
    <a href="{{ route('guru.modul.index') }}" wire:navigate class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-primary-600 hover:underline">
        ← Kembali ke daftar modul
    </a>

    <div class="surface-card rounded-lg p-5 sm:p-6">
        <form wire:submit="save" class="space-y-4">
            <x-input name="judul_materi" label="Judul Materi"/>
            <x-textarea name="isi_materi" label="Isi Materi" :rows="8" placeholder="Tulis penjelasan materi di sini..."/>

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
                <a href="{{ route('guru.modul.index') }}" wire:navigate class="px-4 py-2.5 rounded-lg text-[13.5px] font-semibold text-ink-soft/70 hover:bg-primary-50 transition-colors">Batal</a>
                <button type="submit" class="btn-primary">Simpan Materi</button>
            </div>
        </form>
    </div>
</div>
