<?php

namespace App\Livewire\Admin;

use App\Livewire\BaseComponent;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Materi;
use App\Models\Siswa;
use App\Models\Tugas;
use Livewire\Attributes\Computed;

class Dashboard extends BaseComponent
{
    #[Computed]
    public function stats(): array
    {
        return [
            'guru' => Guru::count(),
            'siswa' => Siswa::count(),
            'kelas' => Kelas::count(),
            'mapel' => MataPelajaran::count(),
        ];
    }

    #[Computed]
    public function aktivitas()
    {
        $materi = Materi::with('guru', 'mapel')->orderByDesc('created_at')->limit(4)->get()
            ->map(fn ($m) => [
                'type' => 'materi',
                'text' => ($m->guru->nama_guru ?? 'Guru').' menambahkan materi "'.$m->judul_materi.'"',
                'time' => $m->created_at,
            ]);

        $tugas = Tugas::with('guru')->orderByDesc('created_at')->limit(4)->get()
            ->map(fn ($t) => [
                'type' => 'tugas',
                'text' => ($t->guru->nama_guru ?? 'Guru').' memberikan tugas "'.$t->judul_tugas.'"',
                'time' => $t->created_at,
            ]);

        return $materi->concat($tugas)->sortByDesc('time')->take(6)->values();
    }

    #[Computed]
    public function siswaPerKelas()
    {
        return Kelas::orderBy('nama_kelas')->get()->each(function ($k) {
            $k->siswa_count = Siswa::where('kelas_id', (string) $k->_id)->count();
        });
    }

    public function render()
    {
        return $this->view('livewire.admin.dashboard', [], 'Dashboard Admin', 'Ringkasan data sekolah secara keseluruhan');
    }
}
