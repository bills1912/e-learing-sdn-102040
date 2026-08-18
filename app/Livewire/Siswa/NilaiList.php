<?php

namespace App\Livewire\Siswa;

use App\Livewire\BaseComponent;
use App\Models\Nilai;
use App\Models\Siswa;
use Livewire\WithPagination;

class NilaiList extends BaseComponent
{
    use WithPagination;

    protected function siswa(): ?Siswa
    {
        return Siswa::where('user_id', (string) auth()->id())->first();
    }

    public function render()
    {
        $siswaId = (string) $this->siswa()?->_id;

        // Aggregate stats are computed across ALL of the student's nilai,
        // independent of which page of the table is currently shown.
        $allItems = Nilai::where('siswa_id', $siswaId)->with('mapel')->get();
        $perMapel = $allItems->groupBy(fn ($n) => $n->mapel->nama_mapel ?? 'Lainnya')
            ->map(fn ($group) => round($group->avg('nilai'), 1));
        $rataKeseluruhan = $allItems->isNotEmpty() ? round($allItems->avg('nilai')) : 0;

        $items = Nilai::with('mapel')->where('siswa_id', $siswaId)->orderByDesc('created_at')->paginate(10);

        return $this->view('livewire.siswa.nilai-list', [
            'items' => $items,
            'perMapel' => $perMapel,
            'rataKeseluruhan' => $rataKeseluruhan,
        ], 'Nilai Saya', 'Pantau perkembangan hasil belajar Anda');
    }
}