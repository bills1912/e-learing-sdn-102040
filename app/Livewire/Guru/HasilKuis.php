<?php

namespace App\Livewire\Guru;

use App\Livewire\BaseComponent;
use App\Models\JawabanKuis;
use App\Models\Kuis;
use App\Models\Siswa;
use App\Models\SoalKuis;

class HasilKuis extends BaseComponent
{
    public string $kuisId;

    public function mount(string $kuis): void
    {
        $this->kuisId = $kuis;
    }

    public function render()
    {
        $kuis = Kuis::with('mapel', 'kelas')->findOrFail($this->kuisId);
        $totalSoal = SoalKuis::where('kuis_id', $this->kuisId)->count();
        $siswaKelas = Siswa::where('kelas_id', (string) $kuis->kelas_id)->orderBy('nama_siswa')->get();

        $jawabanAll = JawabanKuis::where('kuis_id', $this->kuisId)->get()->groupBy(fn ($j) => (string) $j->siswa_id);

        $rows = $siswaKelas->map(function ($s) use ($jawabanAll, $totalSoal) {
            $jawaban = $jawabanAll->get((string) $s->_id, collect());
            $benar = $jawaban->where('benar', true)->count();
            $dijawab = $jawaban->count();

            return (object) [
                'siswa' => $s,
                'dijawab' => $dijawab,
                'benar' => $benar,
                'total' => $totalSoal,
                'skor' => $totalSoal > 0 ? round($benar / $totalSoal * 100) : 0,
                'selesai' => $dijawab >= $totalSoal && $totalSoal > 0,
            ];
        });

        return $this->view('livewire.guru.hasil-kuis', [
            'kuis' => $kuis,
            'rows' => $rows,
            'totalSoal' => $totalSoal,
            'rataRata' => $rows->where('selesai', true)->avg('skor'),
        ], 'Hasil Kuis', $kuis->judul_kuis);
    }
}
