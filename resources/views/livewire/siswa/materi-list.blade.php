<div class="space-y-6">
    <div class="flex items-center gap-3">
        <p class="text-[13px] text-ink-soft/60">{{ $items->count() }} materi</p>
        <div class="w-44">
            <x-select name="filterMapel" :live="true" placeholder="Semua Mapel" class="!py-1.5 !text-[12.5px]">
                <option value="">Semua Mapel</option>
                @foreach($mapelList as $mp)
                    <option value="{{ $mp->_id }}">{{ $mp->nama_mapel }}</option>
                @endforeach
            </x-select>
        </div>
    </div>

    @if($items->isEmpty())
        <div class="surface-card rounded-lg"><x-empty-state icon="book" title="Belum ada materi" subtitle="Materi akan muncul di sini setelah Anda menyelesaikan Pre-Test, Materi, dan Post-Test di menu Modul."/></div>
    @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($items as $i => $m)
                <button wire:click="openMateri('{{ $m->_id }}')" wire:key="materi-{{ $m->_id }}"
                        class="surface-card surface-card-hover rounded-lg p-5 text-left animate-fade-up" style="--stagger: {{ $i }}">
                    <div class="w-11 h-11 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center ring-4 ring-white">{!! \App\Support\Icons::svg('book', 'w-5 h-5') !!}</div>
                    <p class="mt-4 font-display font-semibold text-ink line-clamp-2">{{ $m->judul_materi }}</p>
                    <p class="text-[12.5px] text-ink-soft/60 mt-1 line-clamp-2">{{ Str::limit(strip_tags($m->isi_materi), 90) }}</p>
                    <div class="mt-3 flex items-center gap-2 flex-wrap">
                        <span class="badge-pill bg-mint-50 text-mint-700">{{ $m->mapel->nama_mapel ?? '-' }}</span>
                        @if($m->modul)
                            <span class="badge-pill bg-primary-50 text-primary-600 truncate max-w-[140px]">
                                {!! \App\Support\Icons::svg('academic-cap', 'w-3 h-3 shrink-0') !!} <span class="truncate">{{ $m->modul->judul_modul }}</span>
                            </span>
                        @endif
                        @if($m->hasFile())
                            <span class="badge-pill bg-amber-50 text-amber-700 ml-auto">
                                {!! \App\Support\Icons::svg('paperclip', 'w-3 h-3') !!} {{ strtoupper($m->file_extension) }}
                            </span>
                        @endif
                    </div>
                    <p class="text-[11px] text-ink-soft/50 mt-2">oleh {{ $m->guru->nama_guru ?? '-' }}</p>
                </button>
            @endforeach
        </div>
    @endif

    <template x-teleport="body">
    <div x-show="$wire.viewingId" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-gray-900/50" wire:click="closeView"></div>
        <div class="relative surface-card !bg-white w-full {{ 'max-w-3xl' }} rounded-lg overflow-hidden animate-pop-in max-h-[88vh] flex flex-col">
            @if($viewing)
                <div class="px-6 py-4 border-b border-primary-100/70 flex items-start justify-between shrink-0">
                    <div>
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="badge-pill bg-mint-50 text-mint-700">{{ $viewing->mapel->nama_mapel ?? '-' }}</span>
                        </div>
                        <h3 class="font-display font-bold text-lg text-ink">{{ $viewing->judul_materi }}</h3>
                        <p class="text-[12px] text-ink-soft/50 mt-0.5">oleh {{ $viewing->guru->nama_guru ?? '-' }} · {{ optional($viewing->tanggal_upload)->translatedFormat('d M Y') }}</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('siswa.materi.show', $viewing->_id) }}" wire:navigate
                           class="btn-primary !py-2 !px-2.5 sm:!px-3.5 !text-[12.5px]" title="Baca Layar Penuh">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8V6a2 2 0 012-2h2M4 16v2a2 2 0 002 2h2m8-16h2a2 2 0 012 2v2m-4 12h2a2 2 0 002-2v-2"/></svg>
                            <span class="hidden sm:inline">Baca Layar Penuh</span>
                        </a>
                        <button wire:click="closeView" class="w-8 h-8 rounded-full hover:bg-primary-50 flex items-center justify-center text-ink-soft/60 shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
                <div class="overflow-y-auto scroll-thin flex-1">
                    <div class="p-6 text-[14.5px] text-ink leading-relaxed whitespace-pre-line">
                        {{ $viewing->isi_materi }}
                    </div>

                    @if($viewing->hasFile())
                        <div class="border-t border-gray-100 p-6 pt-5">
                            <p class="text-[12px] font-semibold text-ink-soft uppercase tracking-wide mb-3 flex items-center gap-1.5">
                                {!! \App\Support\Icons::svg('paperclip', 'w-3.5 h-3.5') !!} Bahan Ajar Terlampir
                            </p>

                            @if($viewing->file_kind === 'pdf')
                                <div class="rounded-lg border border-gray-200 overflow-hidden">
                                    <iframe src="{{ $viewing->file_url }}" class="w-full" style="height: 65vh;" title="{{ $viewing->file_name }}"></iframe>
                                </div>
                                <a href="{{ $viewing->file_url }}" target="_blank" download="{{ $viewing->file_name }}" class="btn-primary !bg-transparent !text-primary-600 !shadow-none hover:!bg-primary-50 mt-3 !px-0">
                                    {!! \App\Support\Icons::svg('download', 'w-4 h-4') !!} Unduh {{ $viewing->file_name }}
                                </a>
                            @elseif($viewing->file_kind === 'image')
                                <div class="rounded-lg border border-gray-200 overflow-hidden">
                                    <img src="{{ $viewing->file_url }}" alt="{{ $viewing->file_name }}" class="w-full object-contain max-h-[65vh] bg-gray-50">
                                </div>
                            @else
                                <div class="flex items-center gap-4 p-4 rounded-lg border border-gray-200 bg-gray-50">
                                    <div class="w-12 h-12 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center shrink-0">
                                        {!! \App\Support\Icons::svg('file-pdf', 'w-6 h-6') !!}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[13.5px] font-medium text-ink truncate">{{ $viewing->file_name }}</p>
                                        <p class="text-[12px] text-ink-soft uppercase">{{ $viewing->file_extension }}</p>
                                    </div>
                                    <a href="{{ $viewing->file_url }}" target="_blank" download="{{ $viewing->file_name }}" class="btn-primary !py-2 !px-4 !text-[12.5px] shrink-0">
                                        {!! \App\Support\Icons::svg('download', 'w-4 h-4') !!} Unduh
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
    </template>
</div>