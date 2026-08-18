<?php

namespace App\Livewire\Siswa;

use App\Livewire\BaseComponent;
use App\Models\JawabanKuis;
use App\Models\Kuis;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\SoalKuis;

class KerjakanKuis extends BaseComponent
{
    public string $kuisId;
    public array $jawaban = [];
    public int $currentIndex = 0;
    public bool $finished = false;
    public ?array $hasil = null;

    protected function siswa(): ?Siswa
    {
        return Siswa::where('user_id', (string) auth()->id())->first();
    }

    public function mount(string $kuis): void
    {
        $this->kuisId = $kuis;
        $siswaId = (string) $this->siswa()?->_id;

        $kuisModel = Kuis::findOrFail($kuis);

        // If this is a module's post-test, the materi must be read first.
        // Enforced here server-side, not just hidden in the UI.
        if ($kuisModel->peran === 'posttest' && $kuisModel->modul_id) {
            $modul = \App\Models\Modul::find($kuisModel->modul_id);
            if ($modul) {
                $progress = $modul->progressFor($siswaId);
                if ($progress['posttest_terkunci']) {
                    abort(403, 'Baca materi terlebih dahulu sebelum mengerjakan Post-Test ini.');
                }
            }
        }

        $existing = JawabanKuis::where('kuis_id', $kuis)->where('siswa_id', $siswaId)->get();
        $totalSoal = SoalKuis::where('kuis_id', $kuis)->count();

        foreach ($existing as $e) {
            $this->jawaban[(string) $e->soal_id] = $e->jawaban;
        }

        if ($totalSoal > 0 && $existing->count() >= $totalSoal) {
            $this->finished = true;
            $this->computeHasil();
        }
    }

    public function pilih(string $soalId, string $opsi): void
    {
        $this->jawaban[$soalId] = $opsi;

        $soal = SoalKuis::find($soalId);
        $benar = $soal && $soal->jawaban_benar === $opsi;

        JawabanKuis::updateOrCreate(
            ['kuis_id' => $this->kuisId, 'soal_id' => $soalId, 'siswa_id' => (string) $this->siswa()?->_id],
            ['jawaban' => $opsi, 'benar' => $benar]
        );
    }

    public function next(): void
    {
        $total = SoalKuis::where('kuis_id', $this->kuisId)->count();
        if ($this->currentIndex < $total - 1) {
            $this->currentIndex++;
        }
    }

    public function prev(): void
    {
        if ($this->currentIndex > 0) {
            $this->currentIndex--;
        }
    }

    public function goTo(int $index): void
    {
        $this->currentIndex = $index;
    }

    public function finish(): void
    {
        $this->finished = true;
        $this->computeHasil();

        $kuis = Kuis::find($this->kuisId);
        $hasil = $this->hasil;

        Nilai::updateOrCreate(
            ['siswa_id' => (string) $this->siswa()?->_id, 'kuis_id' => $this->kuisId],
            [
                'mapel_id' => $kuis?->mapel_id,
                'jenis' => 'kuis',
                'nilai' => $hasil['skor'],
                'keterangan' => $kuis?->judul_kuis,
            ]
        );
    }

    protected function computeHasil(): void
    {
        $siswaId = (string) $this->siswa()?->_id;
        $jawaban = JawabanKuis::where('kuis_id', $this->kuisId)->where('siswa_id', $siswaId)->get();
        $total = SoalKuis::where('kuis_id', $this->kuisId)->count();
        $benar = $jawaban->where('benar', true)->count();

        $this->hasil = [
            'benar' => $benar,
            'total' => $total,
            'skor' => $total > 0 ? round($benar / $total * 100) : 0,
        ];
    }

    public function render()
    {
        $kuis = Kuis::with('mapel', 'kelas')->findOrFail($this->kuisId);
        $soalList = SoalKuis::where('kuis_id', $this->kuisId)->orderBy('created_at')->get();

        return $this->view('livewire.siswa.kerjakan-kuis', [
            'kuis' => $kuis,
            'soalList' => $soalList,
            'currentSoal' => $soalList->get($this->currentIndex),
        ], 'Kerjakan Kuis', $kuis->judul_kuis);
    }
}