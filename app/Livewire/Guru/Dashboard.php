<?php

namespace App\Livewire\Guru;

use App\Livewire\BaseComponent;
use App\Models\Guru;
use App\Models\Kuis;
use App\Models\Materi;
use App\Models\PengumpulanTugas;
use App\Models\Tugas;
use Livewire\Attributes\Computed;

class Dashboard extends BaseComponent
{
    #[Computed]
    public function guru(): ?Guru
    {
        return Guru::where('user_id', (string) auth()->id())->first();
    }

    #[Computed]
    public function stats(): array
    {
        $guruId = $this->guru?->_id;

        $tugasIds = Tugas::where('guru_id', $guruId)->pluck('_id')->map(fn ($id) => (string) $id);

        return [
            'materi' => Materi::where('guru_id', $guruId)->count(),
            'tugas' => Tugas::where('guru_id', $guruId)->count(),
            'kuis' => Kuis::where('guru_id', $guruId)->count(),
            'belum_dinilai' => PengumpulanTugas::whereIn('tugas_id', $tugasIds)
                ->where(function ($q) {
                    $q->whereNull('nilai')->orWhere('nilai', '');
                })->count(),
        ];
    }

    #[Computed]
    public function tugasTerbaru()
    {
        return Tugas::with('mapel', 'kelas')
            ->where('guru_id', $this->guru?->_id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
    }

    #[Computed]
    public function kuisAktif()
    {
        $now = now();

        return Kuis::with('mapel', 'kelas')
            ->where('guru_id', $this->guru?->_id)
            ->where('waktu_selesai', '>=', $now)
            ->orderBy('waktu_mulai')
            ->limit(4)
            ->get();
    }

    public function render()
    {
        return $this->view('livewire.guru.dashboard', [], 'Dashboard Guru', 'Selamat mengajar, '.($this->guru->nama_guru ?? auth()->user()->name).'!');
    }
}
