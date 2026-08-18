<?php

namespace App\Livewire\Siswa;

use App\Livewire\BaseComponent;
use App\Models\JawabanKuis;
use App\Models\Kuis;
use App\Models\Siswa;
use App\Models\SoalKuis;

class KuisList extends BaseComponent
{
    protected function siswa(): ?Siswa
    {
        return Siswa::where('user_id', (string) auth()->id())->first();
    }

    public function render()
    {
        $siswa = $this->siswa();
        $now = now();

        $items = Kuis::with('mapel')->where('kelas_id', $siswa?->kelas_id)->whereNull('modul_id')->orderBy('waktu_mulai')->get();

        $items->each(function ($k) use ($siswa, $now) {
            $totalSoal = SoalKuis::where('kuis_id', (string) $k->_id)->count();
            $dijawab = JawabanKuis::where('kuis_id', (string) $k->_id)->where('siswa_id', (string) $siswa?->_id)->count();

            $k->totalSoal = $totalSoal;
            $k->sudahSelesai = $totalSoal > 0 && $dijawab >= $totalSoal;
            $k->status = $now->lt($k->waktu_mulai) ? 'terjadwal' : ($now->gt($k->waktu_selesai) ? 'selesai' : 'berlangsung');
        });

        return $this->view('livewire.siswa.kuis-list', [
            'items' => $items,
        ], 'Kuis', 'Uji pemahaman Anda dengan kuis interaktif');
    }
}