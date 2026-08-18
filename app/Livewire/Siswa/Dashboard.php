<?php

namespace App\Livewire\Siswa;

use App\Livewire\BaseComponent;
use App\Models\Kuis;
use App\Models\Materi;
use App\Models\Modul;
use App\Models\Nilai;
use App\Models\Pengumuman;
use App\Models\PengumpulanTugas;
use App\Models\Siswa;
use App\Models\Tugas;
use Livewire\Attributes\Computed;

class Dashboard extends BaseComponent
{
    #[Computed]
    public function siswa(): ?Siswa
    {
        return Siswa::where('user_id', (string) auth()->id())->first();
    }

    protected function completedModulIds(): array
    {
        $siswa = $this->siswa;
        if (! $siswa) {
            return [];
        }
        $siswaId = (string) $siswa->_id;

        return Modul::where('kelas_id', $siswa->kelas_id)
            ->get()
            ->filter(fn ($m) => $m->progressFor($siswaId)['modul_selesai'])
            ->pluck('_id')
            ->map(fn ($id) => (string) $id)
            ->all();
    }

    #[Computed]
    public function stats(): array
    {
        $kelasId = $this->siswa?->kelas_id;
        $siswaId = (string) $this->siswa?->_id;

        $totalTugas = Tugas::where('kelas_id', $kelasId)->count();
        $sudahKumpul = PengumpulanTugas::where('siswa_id', $siswaId)->count();

        $rataNilai = Nilai::where('siswa_id', $siswaId)->avg('nilai');

        return [
            'materi' => Materi::where('kelas_id', $kelasId)->whereIn('modul_id', $this->completedModulIds())->count(),
            'tugas_selesai' => $sudahKumpul,
            'tugas_total' => $totalTugas,
            'rata_nilai' => $rataNilai ? round($rataNilai) : 0,
        ];
    }

    #[Computed]
    public function pengumumanTerbaru()
    {
        return Pengumuman::with('guru')->orderByDesc('created_at')->limit(2)->get();
    }

    #[Computed]
    public function tugasMendatang()
    {
        $siswaId = (string) $this->siswa?->_id;
        $sudahKumpul = PengumpulanTugas::where('siswa_id', $siswaId)->pluck('tugas_id')->map(fn ($id) => (string) $id);

        return Tugas::with('mapel')
            ->where('kelas_id', $this->siswa?->kelas_id)
            ->whereNotIn('_id', $sudahKumpul->toArray())
            ->orderBy('batas_waktu')
            ->limit(4)
            ->get();
    }

    #[Computed]
    public function kuisAktif()
    {
        $now = now();

        return Kuis::with('mapel')
            ->where('kelas_id', $this->siswa?->kelas_id)
            ->where('waktu_mulai', '<=', $now)
            ->where('waktu_selesai', '>=', $now)
            ->get();
    }

    public function render()
    {
        return $this->view('livewire.siswa.dashboard', [], 'Dashboard Siswa', 'Semangat belajar, '.($this->siswa->nama_siswa ?? auth()->user()->name).'! 🎉');
    }
}