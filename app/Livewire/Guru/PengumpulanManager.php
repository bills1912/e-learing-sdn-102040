<?php

namespace App\Livewire\Guru;

use App\Livewire\BaseComponent;
use App\Models\Nilai;
use App\Models\PengumpulanTugas;
use App\Models\Siswa;
use App\Models\Tugas;

class PengumpulanManager extends BaseComponent
{
    public string $tugasId;
    public ?string $gradingId = null;
    public string $nilaiInput = '';
    public string $feedbackInput = '';

    public function mount(string $tugas): void
    {
        $this->tugasId = $tugas;
    }

    public function openGrade(string $pengumpulanId): void
    {
        $p = PengumpulanTugas::findOrFail($pengumpulanId);
        $this->gradingId = $pengumpulanId;
        $this->nilaiInput = (string) ($p->nilai ?? '');
        $this->feedbackInput = $p->feedback ?? '';
    }

    public function saveGrade(): void
    {
        $this->validate([
            'nilaiInput' => 'required|numeric|min:0|max:100',
            'feedbackInput' => 'nullable|string|max:500',
        ]);

        $p = PengumpulanTugas::findOrFail($this->gradingId);
        $p->update([
            'nilai' => (float) $this->nilaiInput,
            'feedback' => $this->feedbackInput,
            'status' => 'dinilai',
        ]);

        $tugas = Tugas::find($p->tugas_id);

        Nilai::updateOrCreate(
            ['siswa_id' => (string) $p->siswa_id, 'tugas_id' => (string) $p->tugas_id],
            [
                'mapel_id' => $tugas?->mapel_id,
                'jenis' => 'tugas',
                'nilai' => (float) $this->nilaiInput,
                'keterangan' => $tugas?->judul_tugas,
            ]
        );

        $this->gradingId = null;
        session()->flash('success', 'Nilai berhasil disimpan.');
    }

    public function closeGrade(): void
    {
        $this->gradingId = null;
    }

    public function render()
    {
        $tugas = Tugas::with('mapel', 'kelas')->findOrFail($this->tugasId);
        $siswaKelas = Siswa::where('kelas_id', (string) $tugas->kelas_id)->orderBy('nama_siswa')->get();
        $pengumpulan = PengumpulanTugas::where('tugas_id', $this->tugasId)->get()->keyBy(fn ($p) => (string) $p->siswa_id);

        $rows = $siswaKelas->map(function ($s) use ($pengumpulan) {
            $p = $pengumpulan->get((string) $s->_id);

            return (object) [
                'siswa' => $s,
                'pengumpulan' => $p,
            ];
        });

        return $this->view('livewire.guru.pengumpulan-manager', [
            'tugas' => $tugas,
            'rows' => $rows,
        ], 'Pengumpulan Tugas', $tugas->judul_tugas);
    }
}
