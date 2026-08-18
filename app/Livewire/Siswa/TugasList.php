<?php

namespace App\Livewire\Siswa;

use App\Livewire\BaseComponent;
use App\Models\PengumpulanTugas;
use App\Models\Siswa;
use App\Models\Tugas;

class TugasList extends BaseComponent
{
    public ?string $submittingId = null;
    public string $keterangan = '';

    protected function siswa(): ?Siswa
    {
        return Siswa::where('user_id', (string) auth()->id())->first();
    }

    public function openSubmit(string $tugasId): void
    {
        $this->submittingId = $tugasId;
        $this->keterangan = '';
    }

    public function closeSubmit(): void
    {
        $this->submittingId = null;
    }

    public function submit(): void
    {
        $this->validate([
            'keterangan' => 'required|string|min:3|max:2000',
        ], [
            'keterangan.required' => 'Silakan tulis jawaban Anda sebelum mengumpulkan.',
        ]);

        $siswa = $this->siswa();

        PengumpulanTugas::updateOrCreate(
            ['tugas_id' => $this->submittingId, 'siswa_id' => (string) $siswa->_id],
            [
                'keterangan' => $this->keterangan,
                'tanggal_kumpul' => now(),
                'status' => 'menunggu',
            ]
        );

        $this->submittingId = null;
        session()->flash('success', 'Tugas berhasil dikumpulkan!');
    }

    public function render()
    {
        $siswa = $this->siswa();
        $items = Tugas::with('mapel', 'guru')->where('kelas_id', $siswa?->kelas_id)->orderBy('batas_waktu')->get();
        $pengumpulan = PengumpulanTugas::where('siswa_id', (string) $siswa?->_id)->get()->keyBy(fn ($p) => (string) $p->tugas_id);

        $items->each(function ($t) use ($pengumpulan) {
            $t->pengumpulanSaya = $pengumpulan->get((string) $t->_id);
        });

        return $this->view('livewire.siswa.tugas-list', [
            'items' => $items,
        ], 'Tugas Saya', 'Kerjakan dan kumpulkan tugas tepat waktu');
    }
}
