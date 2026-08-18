<div class="flex flex-col h-screen bg-white"
     x-data="{
        isFullscreen: false,
        showDesc: {{ $materi->hasFile() ? 'false' : 'true' }},
        toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen?.();
                this.isFullscreen = true;
            } else {
                document.exitFullscreen?.();
                this.isFullscreen = false;
            }
        }
     }"
     @fullscreenchange.window="isFullscreen = !!document.fullscreenElement">

    {{-- Top bar --}}
    <header class="shrink-0 border-b border-gray-200 bg-white px-3 sm:px-5 py-2.5 flex items-center gap-2 sm:gap-3">
        <a href="{{ route('siswa.materi.index') }}" wire:navigate
           class="p-2 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-gray-900 transition-colors shrink-0">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>

        <div class="min-w-0 flex-1">
            <p class="font-display font-semibold text-[14px] sm:text-[15px] text-ink truncate">{{ $materi->judul_materi }}</p>
            <p class="text-[11.5px] text-ink-soft truncate">{{ $materi->mapel->nama_mapel ?? '-' }} · oleh {{ $materi->guru->nama_guru ?? '-' }}</p>
        </div>

        <div class="flex items-center gap-1.5 shrink-0">
            @if($materi->hasFile())
                <button @click="showDesc = !showDesc"
                        class="inline-flex items-center gap-1.5 p-2 sm:px-3 sm:py-1.5 rounded-lg text-[12.5px] font-medium transition-colors"
                        :class="showDesc ? 'bg-primary-50 text-primary-700' : 'text-gray-500 hover:bg-gray-100'"
                        title="Lihat Deskripsi">
                    {!! \App\Support\Icons::svg('paperclip', 'w-4 h-4 sm:hidden') !!}
                    <span class="hidden sm:inline" x-text="showDesc ? 'Sembunyikan Deskripsi' : 'Lihat Deskripsi'"></span>
                </button>
                <a href="{{ $materi->file_url }}" target="_blank" download="{{ $materi->file_name }}"
                   class="p-2 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-gray-900 transition-colors" title="Unduh">
                    {!! \App\Support\Icons::svg('download', 'w-5 h-5') !!}
                </a>
            @endif
            <button @click="toggleFullscreen()"
                    class="p-2 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-gray-900 transition-colors" title="Layar Penuh">
                <svg x-show="!isFullscreen" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8V6a2 2 0 012-2h2M4 16v2a2 2 0 002 2h2m8-16h2a2 2 0 012 2v2m-4 12h2a2 2 0 002-2v-2"/></svg>
                <svg x-show="isFullscreen" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 9V5a1 1 0 00-1-1H6a1 1 0 00-1 1v2a1 1 0 001 1h2a1 1 0 001-1zm0 0h2M9 15v4a1 1 0 01-1 1H6a1 1 0 01-1-1v-2a1 1 0 011-1h2a1 1 0 011 1zm6-6V5a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 01-1 1h-2a1 1 0 01-1-1zm0 6v4a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 00-1-1h-2a1 1 0 00-1 1z"/></svg>
            </button>
        </div>
    </header>

    {{-- Content --}}
    <div class="flex-1 min-h-0 flex flex-col overflow-hidden">
        @if($materi->hasFile())
            {{-- Collapsible description --}}
            <div x-show="showDesc" x-collapse x-cloak class="shrink-0 border-b border-gray-100 bg-gray-50/60 px-4 sm:px-6 py-4 max-h-48 overflow-y-auto scroll-thin">
                <p class="text-[13.5px] text-ink leading-relaxed whitespace-pre-line max-w-3xl mx-auto">{{ $materi->isi_materi }}</p>
            </div>

            {{-- File preview fills remaining space --}}
            <div class="flex-1 min-h-0 bg-gray-100">
                @if($materi->file_kind === 'pdf')
                    <iframe src="{{ $materi->file_url }}" class="w-full h-full border-0" title="{{ $materi->file_name }}"></iframe>
                @elseif($materi->file_kind === 'image')
                    <div class="w-full h-full overflow-auto scroll-thin flex items-start sm:items-center justify-center p-2 sm:p-6">
                        <img src="{{ $materi->file_url }}" alt="{{ $materi->file_name }}" class="max-w-full sm:max-h-full object-contain rounded-lg shadow-sm">
                    </div>
                @else
                    <div class="w-full h-full flex items-center justify-center p-6">
                        <div class="text-center">
                            <div class="w-16 h-16 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center mx-auto mb-4">
                                {!! \App\Support\Icons::svg('file-pdf', 'w-8 h-8') !!}
                            </div>
                            <p class="font-display font-semibold text-ink">{{ $materi->file_name }}</p>
                            <p class="text-[12.5px] text-ink-soft uppercase mt-0.5 mb-4">{{ $materi->file_extension }} · tidak bisa dipratinjau di browser</p>
                            <a href="{{ $materi->file_url }}" target="_blank" download="{{ $materi->file_name }}" class="btn-primary inline-flex">
                                {!! \App\Support\Icons::svg('download', 'w-4 h-4') !!} Unduh File
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        @else
            {{-- Text-only material: comfortable full-height reading view --}}
            <div class="flex-1 overflow-y-auto scroll-thin">
                <div class="max-w-3xl mx-auto px-5 sm:px-8 py-8">
                    <p class="text-[15.5px] sm:text-[16px] text-ink leading-[1.85] whitespace-pre-line">{{ $materi->isi_materi }}</p>
                </div>
            </div>
        @endif
    </div>
</div>