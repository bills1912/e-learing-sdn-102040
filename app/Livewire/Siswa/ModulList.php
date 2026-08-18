<?php

namespace App\Livewire\Siswa;

use App\Livewire\BaseComponent;
use App\Models\MataPelajaran;
use App\Models\Modul;
use App\Models\Siswa;

class ModulList extends BaseComponent
{
    public string $filterMapel = '';

    protected function siswa(): ?Siswa
    {
        return Siswa::where('user_id', (string) auth()->id())->first();
    }

    public function render()
    {
        $siswa = $this->siswa();
        $query = Modul::with('mapel')->where('kelas_id', $siswa?->kelas_id);

        if ($this->filterMapel) {
            $query->where('mapel_id', $this->filterMapel);
        }

        $modulList = $query->orderBy('mapel_id')->orderBy('urutan')->get();

        $siswaId = (string) $siswa?->_id;
        $modulList->each(function ($m) use ($siswaId) {
            $progress = $m->progressFor($siswaId);
            $m->progress = $progress;
            $m->langkahSelesai = collect([$progress['pretest_selesai'], $progress['materi_dibaca'], $progress['posttest_selesai']])->filter()->count();
        });

        $grouped = $modulList->groupBy(fn ($m) => $m->mapel->nama_mapel ?? 'Lainnya');

        // Also compute totals across ALL modules (not just the filtered view) for the stat cards.
        $allModul = Modul::where('kelas_id', $siswa?->kelas_id)->get();
        $totalSelesaiKeseluruhan = $allModul->filter(fn ($m) => $m->progressFor($siswaId)['modul_selesai'])->count();

        return $this->view('livewire.siswa.modul-list', [
            'grouped' => $grouped,
            'mapelList' => MataPelajaran::orderBy('nama_mapel')->get(),
            'totalModul' => $allModul->count(),
            'totalSelesai' => $totalSelesaiKeseluruhan,
        ], 'Modul Pembelajaran', 'Pre-test, materi, dan post-test tiap mata pelajaran');
    }
}