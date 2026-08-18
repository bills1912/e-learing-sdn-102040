<?php

namespace App\Livewire\Siswa;

use App\Livewire\BaseComponent;
use App\Models\MataPelajaran;
use App\Models\Materi;
use App\Models\Modul;
use App\Models\Siswa;

class MateriList extends BaseComponent
{
    public string $filterMapel = '';
    public ?string $viewingId = null;

    protected function siswa(): ?Siswa
    {
        return Siswa::where('user_id', (string) auth()->id())->first();
    }

    public function openMateri(string $id): void
    {
        $this->viewingId = $id;
    }

    public function closeView(): void
    {
        $this->viewingId = null;
    }

    /**
     * Materi visible here must belong to a module the student has FULLY
     * completed (pretest -> materi -> posttest). Materi with no module at
     * all, or an incomplete module, do not appear here.
     */
    protected function completedModulIds(Siswa $siswa): array
    {
        $siswaId = (string) $siswa->_id;

        return Modul::where('kelas_id', $siswa->kelas_id)
            ->get()
            ->filter(fn ($m) => $m->progressFor($siswaId)['modul_selesai'])
            ->pluck('_id')
            ->map(fn ($id) => (string) $id)
            ->all();
    }

    public function render()
    {
        $siswa = $this->siswa();
        $completedModulIds = $siswa ? $this->completedModulIds($siswa) : [];

        $query = Materi::with('mapel', 'guru', 'modul')
            ->where('kelas_id', $siswa?->kelas_id)
            ->whereIn('modul_id', $completedModulIds)
            ->orderByDesc('created_at');

        if ($this->filterMapel) {
            $query->where('mapel_id', $this->filterMapel);
        }

        return $this->view('livewire.siswa.materi-list', [
            'items' => $completedModulIds ? $query->get() : collect(),
            'mapelList' => MataPelajaran::orderBy('nama_mapel')->get(),
            'viewing' => $this->viewingId ? Materi::with('mapel', 'guru')->find($this->viewingId) : null,
        ], 'Materi Pembelajaran', 'Materi dari modul yang telah Anda selesaikan');
    }
}