<div>
    @if($finished && $hasil)
        <div class="max-w-lg mx-auto surface-card rounded-3xl p-8 text-center animate-pop-in">
            <div class="mb-2 text-5xl">{{ $hasil['skor'] >= 80 ? '🏆' : ($hasil['skor'] >= 60 ? '🎉' : '💪') }}</div>
            <h2 class="font-display font-bold text-xl text-ink">Kuis Selesai!</h2>
            <p class="text-[13px] text-ink-soft/60 mt-1">{{ $kuis->judul_kuis }}</p>

            <div class="flex justify-center my-8">
                <x-progress-ring :value="$hasil['skor']" :size="140" :stroke="12" color="stroke-primary-500" label="Skor Anda"/>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-6">
                <div class="p-4 rounded-lg bg-mint-50">
                    <p class="font-display font-bold text-xl text-mint-700">{{ $hasil['benar'] }}</p>
                    <p class="text-[12px] text-mint-700/70">Jawaban Benar</p>
                </div>
                <div class="p-4 rounded-lg bg-rose-50">
                    <p class="font-display font-bold text-xl text-danger">{{ $hasil['total'] - $hasil['benar'] }}</p>
                    <p class="text-[12px] text-danger/70">Jawaban Salah</p>
                </div>
            </div>

            <a href="{{ route('siswa.kuis.index') }}" wire:navigate class="btn-primary inline-flex">Kembali ke Daftar Kuis</a>
        </div>
    @elseif($currentSoal)
        <div class="max-w-2xl mx-auto space-y-5"
             x-data="{ secondsLeft: {{ $kuis->durasi_menit * 60 }} }"
             x-init="const t = setInterval(() => { if (secondsLeft > 0) secondsLeft--; else { clearInterval(t); $wire.finish(); } }, 1000)">

            <div class="flex items-center justify-between gap-2">
                <a href="{{ route('siswa.kuis.index') }}" wire:navigate class="text-[13px] font-semibold text-primary-600 hover:underline shrink-0 whitespace-nowrap">← Keluar</a>
                <div class="flex items-center gap-2 px-3.5 py-2 rounded-full bg-white shadow-sm font-mono text-[13px] font-semibold shrink-0"
                     :class="secondsLeft <= 30 ? 'text-danger' : 'text-ink'">
                    {!! \App\Support\Icons::svg('clock', 'w-4 h-4') !!}
                    <span x-text="String(Math.floor(secondsLeft/60)).padStart(2,'0') + ':' + String(secondsLeft%60).padStart(2,'0')"></span>
                </div>
            </div>

            <div class="surface-card rounded-lg p-4 sm:p-6">
                <div class="flex items-center justify-between gap-2 mb-4 flex-wrap">
                    <span class="badge-pill bg-primary-50 text-primary-600 whitespace-nowrap">Soal {{ $currentIndex + 1 }} / {{ $soalList->count() }}</span>
                    <span class="badge-pill bg-mint-50 text-mint-700 whitespace-nowrap">{{ count($jawaban) }} terjawab</span>
                </div>

                <div class="h-1.5 rounded-full bg-primary-50 overflow-hidden mb-6">
                    <div class="h-full bg-gradient-to-r from-primary-500 to-accent-400 transition-all duration-500" style="width: {{ ($currentIndex + 1) / max(1, $soalList->count()) * 100 }}%"></div>
                </div>

                <p class="font-display font-semibold text-ink text-[17px] leading-snug mb-5">{{ $currentSoal->pertanyaan }}</p>

                <div class="space-y-2.5">
                    @foreach(['A' => $currentSoal->pilihan_a, 'B' => $currentSoal->pilihan_b, 'C' => $currentSoal->pilihan_c, 'D' => $currentSoal->pilihan_d] as $opt => $text)
                        @php $selected = ($jawaban[(string) $currentSoal->_id] ?? null) === $opt; @endphp
                        <button wire:click="pilih('{{ $currentSoal->_id }}', '{{ $opt }}')"
                                class="w-full flex items-center gap-3 px-4 py-3 rounded-lg border-2 text-left transition-all duration-150
                                       {{ $selected ? 'border-primary-500 bg-primary-50' : 'border-primary-100 hover:border-primary-300 hover:bg-primary-50/40' }}">
                            <span class="w-7 h-7 rounded-full flex items-center justify-center text-[12px] font-bold shrink-0 {{ $selected ? 'bg-primary-600 text-white' : 'bg-primary-50 text-ink-soft/60' }}">{{ $opt }}</span>
                            <span class="text-[14px] {{ $selected ? 'text-primary-800 font-medium' : 'text-ink' }}">{{ $text }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                @foreach($soalList as $i => $s)
                    <button wire:click="goTo({{ $i }})"
                            class="w-8 h-8 rounded-lg text-[12px] font-semibold transition-colors
                                   {{ $i === $currentIndex ? 'bg-primary-600 text-white' : (isset($jawaban[(string) $s->_id]) ? 'bg-mint-100 text-mint-700' : 'bg-white text-ink-soft/50 border border-primary-100') }}">
                        {{ $i + 1 }}
                    </button>
                @endforeach
            </div>

            <div class="flex items-center justify-between gap-3 flex-wrap">
                <button wire:click="prev" @if($currentIndex === 0) disabled @endif
                        class="px-4 sm:px-5 py-2.5 rounded-lg text-[13.5px] font-semibold text-ink-soft/70 hover:bg-primary-50 transition-colors disabled:opacity-30 disabled:cursor-not-allowed whitespace-nowrap">
                    ← Sebelumnya
                </button>
                @if($currentIndex === $soalList->count() - 1)
                    <button wire:click="finish" class="px-4 sm:px-6 py-2.5 rounded-lg bg-mint-500 text-white text-[13.5px] font-semibold hover:bg-mint-600 transition-colors shadow-lg shadow-mint-500/25 whitespace-nowrap">
                        Selesai &amp; Kumpulkan
                    </button>
                @else
                    <button wire:click="next" class="btn-primary whitespace-nowrap">
                        Selanjutnya →
                    </button>
                @endif
            </div>
        </div>
    @else
        <x-empty-state icon="quiz" title="Kuis ini belum memiliki soal" subtitle="Silakan hubungi guru Anda."/>
    @endif
</div>