<?php

namespace App\Livewire\Siswa;

use App\Livewire\BaseComponent;
use App\Models\Modul;
use App\Models\Siswa;

class ModulShow extends BaseComponent
{
    public string $modulId;

    protected function siswa(): ?Siswa
    {
        return Siswa::where('user_id', (string) auth()->id())->first();
    }

    public function mount(string $modulId): void
    {
        $modul = Modul::findOrFail($modulId);
        $siswa = $this->siswa();

        if (! $siswa || (string) $modul->kelas_id !== (string) $siswa->kelas_id) {
            abort(403, 'Modul ini bukan untuk kelas Anda.');
        }

        $this->modulId = $modulId;
    }

    public function render()
    {
        $modul = Modul::with('mapel', 'kelas')->findOrFail($this->modulId);
        $siswaId = (string) $this->siswa()?->_id;
        $progress = $modul->progressFor($siswaId);

        return $this->view('livewire.siswa.modul-show', [
            'modul' => $modul,
            'progress' => $progress,
        ], $modul->judul_modul, $modul->mapel->nama_mapel ?? '');
    }
}
