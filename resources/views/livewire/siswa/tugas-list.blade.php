<div>
    @if($items->isEmpty())
        <div class="surface-card rounded-lg"><x-empty-state icon="clipboard" title="Belum ada tugas" subtitle="Tugas dari guru akan muncul di sini."/></div>
    @else
        <div class="surface-card rounded-lg overflow-hidden">
            <ul role="list" class="divide-y divide-primary-50">
                @foreach($items as $i => $t)
                    @php
                        $sudahKumpul = (bool) $t->pengumpulanSaya;
                        $sudahDinilai = $sudahKumpul && $t->pengumpulanSaya->status === 'dinilai';
                        $telatWaktu = !$sudahKumpul && now()->gt($t->batas_waktu);
                        $accent = $sudahDinilai ? 'var(--color-mint-500)' : ($sudahKumpul ? 'var(--color-accent-500)' : ($telatWaktu ? 'var(--color-danger)' : 'var(--color-primary-400)'));
                    @endphp
                    <li wire:key="tugas-{{ $t->_id }}" class="row-card animate-fade-up hover:bg-primary-50/30 transition-colors" style="--stagger: {{ $i }}; --row-accent: {{ $accent }}">
                        <div class="flex items-center gap-4 p-5">
                            <div class="shrink-0">
                                <div class="w-11 h-11 rounded-full {{ $sudahKumpul ? 'bg-mint-50 text-mint-600' : 'bg-orange-50 text-accent-600' }} flex items-center justify-center ring-4 ring-white">
                                    {!! \App\Support\Icons::svg($sudahKumpul ? 'check-badge' : 'clipboard', 'w-5 h-5') !!}
                                </div>
                            </div>

                            <div class="flex-1 min-w-0">
                                <p class="font-display font-semibold text-ink truncate">{{ $t->judul_tugas }}</p>
                                <p class="text-[13px] text-ink-soft/60 mt-0.5 truncate">{{ $t->deskripsi }}</p>
                                <div class="flex items-center gap-2 flex-wrap mt-2">
                                    <span class="badge-pill bg-primary-50 text-primary-600">{{ $t->mapel->nama_mapel ?? '-' }}</span>
                                    <span class="text-[11.5px] text-ink-soft/50">Batas: {{ optional($t->batas_waktu)->translatedFormat('d M Y, H:i') }}</span>
                                </div>
                                @if($sudahDinilai && $t->pengumpulanSaya->feedback)
                                    <div class="mt-2 pl-3 border-l-2 border-mint-300 text-[12.5px] text-ink-soft/70">
                                        <span class="font-semibold text-mint-700">Umpan balik:</span> {{ $t->pengumpulanSaya->feedback }}
                                    </div>
                                @endif
                            </div>

                            {{-- Fixed-width trailing column: every row aligns regardless of status --}}
                            <div class="shrink-0 w-32 sm:w-36 flex flex-col items-end gap-1.5">
                                @if($sudahDinilai)
                                    <span class="badge-pill bg-mint-50 text-mint-700 whitespace-nowrap">
                                        <span class="font-display font-bold text-[13px]">{{ $t->pengumpulanSaya->nilai }}</span> · Dinilai
                                    </span>
                                @elseif($sudahKumpul)
                                    <span class="badge-pill bg-orange-50 text-accent-600 whitespace-nowrap">Menunggu nilai</span>
                                @elseif($telatWaktu)
                                    <span class="badge-pill bg-rose-50 text-danger whitespace-nowrap">Terlambat</span>
                                    <button wire:click="openSubmit('{{ $t->_id }}')" class="btn-primary !py-1.5 !px-3.5 !text-[12px] w-full">Kumpulkan</button>
                                @else
                                    <button wire:click="openSubmit('{{ $t->_id }}')" class="btn-primary !py-1.5 !px-3.5 !text-[12px] w-full">Kumpulkan</button>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <x-modal wire-show="submittingId" close="closeSubmit" title="Kumpulkan Tugas">
        <form wire:submit="submit" class="space-y-4">
            <x-textarea name="keterangan" label="Jawaban / Keterangan" :rows="6" placeholder="Tulis jawaban Anda di sini..."/>
            <p class="text-[12px] text-ink-soft/50">Tip: Anda bisa menuliskan jawaban langsung atau menjelaskan file yang sudah dikerjakan.</p>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" wire:click="closeSubmit" class="px-4 py-2.5 rounded-lg text-[13.5px] font-semibold text-ink-soft/70 hover:bg-primary-50 transition-colors">Batal</button>
                <button type="submit" class="btn-primary">Kumpulkan Tugas</button>
            </div>
        </form>
    </x-modal>
</div>