<div class="relative" wire:poll.20s>
    <button type="button" @click="$wire.toggle()"
            class="relative p-2 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-gray-900 transition-colors">
        <span class="sr-only">Notifikasi</span>
        {!! \App\Support\Icons::svg('bell', 'w-5 h-5') !!}
        @if($this->unreadCount > 0)
            <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 rounded-full bg-danger text-white text-[10px] font-bold flex items-center justify-center">
                {{ $this->unreadCount > 9 ? '9+' : $this->unreadCount }}
            </span>
        @endif
    </button>

    {{-- Teleported to <body> so it floats above everything, completely
         independent of the header's layout/positioning context.
         Fixed offset (not JS-calculated) — our topbar height is consistent
         across every page, so this is both simpler and more reliable. --}}
    <template x-teleport="body">
        <div x-show="$wire.open" x-cloak
             class="fixed inset-0 z-[100]" style="pointer-events: none;"
             x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            {{-- Invisible click-away backdrop --}}
            <div class="absolute inset-0" style="pointer-events: auto;" wire:click="close"></div>

            {{-- Panel: fixed just under the topbar, right-aligned.
                 Uses inline style (not Tailwind utility classes) for the
                 critical top/right offset, so it works immediately even if
                 the CSS bundle hasn't been rebuilt yet. --}}
            <div class="surface-card !bg-white rounded-lg overflow-hidden"
                 style="pointer-events: auto; position: fixed; top: 64px; right: 12px; width: 22rem; max-width: 90vw;">
                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <p class="font-display font-semibold text-ink text-[14px]">Pengumuman</p>
                    @if($this->pengumuman->isNotEmpty())
                        <a href="{{ route('siswa.pengumuman.index') }}" wire:navigate class="text-[12px] font-semibold text-primary-600 hover:underline">Lihat semua</a>
                    @endif
                </div>

                <div class="max-h-80 overflow-y-auto scroll-thin">
                    @forelse($this->pengumuman as $p)
                        @php $isUnread = ! in_array((string) $p->_id, $this->readIds); @endphp
                        <div class="px-4 py-3 border-b border-gray-50 last:border-0 {{ $isUnread ? 'bg-primary-50/30' : '' }}">
                            <div class="flex items-start gap-2">
                                <span class="w-1.5 h-1.5 rounded-full {{ $isUnread ? 'bg-primary-500' : 'bg-transparent' }} mt-1.5 shrink-0"></span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[13px] font-semibold text-ink">{{ $p->judul }}</p>
                                    <p class="text-[12.5px] text-ink-soft/70 mt-0.5 line-clamp-2">{{ $p->isi }}</p>
                                    <p class="text-[11px] text-ink-soft/50 mt-1">{{ $p->guru->nama_guru ?? '-' }} · {{ $p->created_at?->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-8 text-center">
                            <p class="text-[13px] text-ink-soft/50">Belum ada pengumuman</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </template>
</div>