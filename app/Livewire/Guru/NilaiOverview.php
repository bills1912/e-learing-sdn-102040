<?php

namespace App\Livewire\Guru;

use App\Livewire\BaseComponent;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Nilai;
use App\Models\Siswa;

class NilaiOverview extends BaseComponent
{
    public string $filterKelas = '';

    protected function guruId(): ?string
    {
        return Guru::where('user_id', (string) auth()->id())->value('_id');
    }

    public function mount(): void
    {
        $this->filterKelas = Kelas::first()?->_id ?? '';
    }

    public function render()
    {
        $siswaKelas = Siswa::where('kelas_id', $this->filterKelas)->orderBy('nama_siswa')->get();
        $nilaiAll = Nilai::whereIn('siswa_id', $siswaKelas->pluck('_id')->map(fn ($id) => (string) $id))->get()
            ->groupBy(fn ($n) => (string) $n->siswa_id);

        $rows = $siswaKelas->map(function ($s) use ($nilaiAll) {
            $nilai = $nilaiAll->get((string) $s->_id, collect());

            return (object) [
                'siswa' => $s,
                'rata_tugas' => round($nilai->where('jenis', 'tugas')->avg('nilai') ?? 0, 1),
                'rata_kuis' => round($nilai->where('jenis', 'kuis')->avg('nilai') ?? 0, 1),
                'jumlah_nilai' => $nilai->count(),
                'rata_keseluruhan' => round($nilai->avg('nilai') ?? 0, 1),
            ];
        });

        return $this->view('livewire.guru.nilai-overview', [
            'rows' => $rows->sortByDesc('rata_keseluruhan')->values(),
            'kelasList' => Kelas::orderBy('nama_kelas')->get(),
        ], 'Rekap Nilai', 'Ringkasan nilai tugas dan kuis siswa');
    }
}
