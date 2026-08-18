<div class="space-y-6 max-w-2xl mx-auto">
    <a href="{{ route('siswa.modul.index') }}" wire:navigate class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-primary-600 hover:underline">
        ← Kembali ke daftar modul
    </a>

    <div class="surface-card rounded-lg p-5 sm:p-6">
        <div class="flex items-center gap-2 flex-wrap mb-2">
            <span class="badge-pill bg-primary-50 text-primary-600 whitespace-nowrap">{{ $modul->mapel->nama_mapel ?? '-' }}</span>
            <span class="badge-pill bg-orange-50 text-accent-600 whitespace-nowrap">Kelas {{ $modul->kelas->nama_kelas ?? '-' }}</span>
        </div>
        <h2 class="font-display font-bold text-xl text-ink">{{ $modul->judul_modul }}</h2>
        @if($modul->deskripsi)
            <p class="text-[13.5px] text-ink-soft/70 mt-1">{{ $modul->deskripsi }}</p>
        @endif

        @if($progress['modul_selesai'])
            <div class="mt-4 px-4 py-2.5 rounded-lg bg-mint-50 text-mint-700 text-[13px] font-semibold flex items-center gap-2">
                {!! \App\Support\Icons::svg('trophy', 'w-4 h-4') !!} Modul ini sudah Anda selesaikan! 🎉
            </div>
        @endif
    </div>

    {{-- Vertical stepper: Pre-Test -> Materi -> Post-Test --}}
    <div class="surface-card rounded-lg p-5 sm:p-6">
        <div class="relative">

            {{-- Step 1: Pre-Test --}}
            <div class="flex gap-4">
                <div class="flex flex-col items-center shrink-0">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-display font-bold text-[13px] {{ $progress['pretest_selesai'] ? 'bg-mint-500 text-white' : 'bg-primary-600 text-white' }}">
                        @if($progress['pretest_selesai'])
                            {!! \App\Support\Icons::svg('check-badge', 'w-5 h-5') !!}
                        @else
                            1
                        @endif
                    </div>
                    <div class="w-0.5 flex-1 my-1 {{ $progress['pretest_selesai'] ? 'bg-mint-300' : 'bg-gray-200' }}" style="min-height: 2.5rem;"></div>
                </div>
                <div class="pb-8 min-w-0 flex-1">
                    <p class="font-display font-semibold text-ink">Pre-Test</p>
                    <p class="text-[12.5px] text-ink-soft/60 mt-0.5">Uji pemahaman awal Anda sebelum belajar materi.</p>
                    <div class="mt-3">
                        @if($progress['pretest_selesai'])
                            <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-mint-50 text-mint-700 text-[12.5px] font-semibold">
                                {!! \App\Support\Icons::svg('check-badge', 'w-4 h-4') !!} Selesai · Skor {{ $progress['pretest_skor'] }}
                            </div>
                        @else
                            <a href="{{ route('siswa.kuis.kerjakan', $modul->pretest_kuis_id) }}" wire:navigate class="btn-primary">
                                Kerjakan Pre-Test
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Step 2: Materi --}}
            <div class="flex gap-4">
                <div class="flex flex-col items-center shrink-0">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-display font-bold text-[13px]
                                {{ $progress['materi_dibaca'] ? 'bg-mint-500 text-white' : ($progress['materi_terkunci'] ? 'bg-gray-200 text-gray-400' : 'bg-primary-600 text-white') }}">
                        @if($progress['materi_dibaca'])
                            {!! \App\Support\Icons::svg('check-badge', 'w-5 h-5') !!}
                        @elseif($progress['materi_terkunci'])
                            {!! \App\Support\Icons::svg('lock', 'w-4 h-4') !!}
                        @else
                            2
                        @endif
                    </div>
                    <div class="w-0.5 flex-1 my-1 {{ $progress['materi_dibaca'] ? 'bg-mint-300' : 'bg-gray-200' }}" style="min-height: 2.5rem;"></div>
                </div>
                <div class="pb-8 min-w-0 flex-1">
                    <p class="font-display font-semibold {{ $progress['materi_terkunci'] ? 'text-gray-400' : 'text-ink' }}">Materi</p>
                    <p class="text-[12.5px] {{ $progress['materi_terkunci'] ? 'text-gray-400' : 'text-ink-soft/60' }} mt-0.5">
                        {{ $progress['materi_terkunci'] ? 'Selesaikan Pre-Test terlebih dahulu untuk membuka materi.' : 'Baca dan pahami materi pembelajaran.' }}
                    </p>
                    <div class="mt-3">
                        @if($progress['materi_terkunci'])
                            <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-gray-100 text-gray-400 text-[12.5px] font-semibold cursor-not-allowed">
                                {!! \App\Support\Icons::svg('lock', 'w-3.5 h-3.5') !!} Terkunci
                            </div>
                        @elseif($progress['materi_dibaca'])
                            <a href="{{ route('siswa.materi.show', $modul->materi_id) }}" wire:navigate class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-mint-50 text-mint-700 text-[12.5px] font-semibold hover:bg-mint-100 transition-colors">
                                {!! \App\Support\Icons::svg('check-badge', 'w-4 h-4') !!} Sudah dibaca · Baca lagi
                            </a>
                        @else
                            <a href="{{ route('siswa.materi.show', $modul->materi_id) }}" wire:navigate class="btn-primary">
                                Baca Materi
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Step 3: Post-Test --}}
            <div class="flex gap-4">
                <div class="flex flex-col items-center shrink-0">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-display font-bold text-[13px]
                                {{ $progress['posttest_selesai'] ? 'bg-mint-500 text-white' : ($progress['posttest_terkunci'] ? 'bg-gray-200 text-gray-400' : 'bg-primary-600 text-white') }}">
                        @if($progress['posttest_selesai'])
                            {!! \App\Support\Icons::svg('check-badge', 'w-5 h-5') !!}
                        @elseif($progress['posttest_terkunci'])
                            {!! \App\Support\Icons::svg('lock', 'w-4 h-4') !!}
                        @else
                            3
                        @endif
                    </div>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-display font-semibold {{ $progress['posttest_terkunci'] ? 'text-gray-400' : 'text-ink' }}">Post-Test</p>
                    <p class="text-[12.5px] {{ $progress['posttest_terkunci'] ? 'text-gray-400' : 'text-ink-soft/60' }} mt-0.5">
                        {{ $progress['posttest_terkunci'] ? 'Baca materi terlebih dahulu untuk membuka post-test.' : 'Uji seberapa jauh pemahaman Anda setelah belajar.' }}
                    </p>
                    <div class="mt-3">
                        @if($progress['posttest_terkunci'])
                            <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-gray-100 text-gray-400 text-[12.5px] font-semibold cursor-not-allowed">
                                {!! \App\Support\Icons::svg('lock', 'w-3.5 h-3.5') !!} Terkunci
                            </div>
                        @elseif($progress['posttest_selesai'])
                            <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-mint-50 text-mint-700 text-[12.5px] font-semibold">
                                {!! \App\Support\Icons::svg('check-badge', 'w-4 h-4') !!} Selesai · Skor {{ $progress['posttest_skor'] }}
                            </div>
                        @else
                            <a href="{{ route('siswa.kuis.kerjakan', $modul->posttest_kuis_id) }}" wire:navigate class="btn-primary">
                                Kerjakan Post-Test
                            </a>
                        @endif
                    </div>

                    @if($progress['posttest_selesai'] && $progress['pretest_skor'] !== null)
                        @php $peningkatan = $progress['posttest_skor'] - $progress['pretest_skor']; @endphp
                        <div class="mt-3 p-3 rounded-lg bg-primary-50/60 text-[12.5px] text-ink">
                            @if($peningkatan > 0)
                                📈 Skor Anda naik <span class="font-bold text-mint-700">+{{ $peningkatan }}</span> poin dari pre-test ke post-test. Kerja bagus!
                            @elseif($peningkatan == 0 & $progress['posttest_skor'] < 100 & $progress['pretest_skor'] < 100)
                                Skor pre-test dan post-test Anda sama ({{ $progress['posttest_skor'] }}).
                            @elseif($progress['posttest_skor'] == 100 & $progress['pretest_skor'] == 100)
                                Skor pre-test dan post-test Anda sempurna 🎉. Terus dipertahankan dan ditingkatkan 🎖️.
                            @else
                                Skor post-test Anda {{ abs($peningkatan) }} poin lebih rendah dari pre-test. Coba baca ulang materi ya.
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>