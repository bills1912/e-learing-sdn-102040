<?php

namespace App\Livewire\Siswa;

use App\Livewire\BaseComponent;
use App\Models\Pengumuman;
use App\Models\PengumumanRead;
use App\Models\Siswa;

class PengumumanList extends BaseComponent
{
    public string $filterDari = '';
    public string $filterSampai = '';

    protected function siswa(): ?Siswa
    {
        return Siswa::where('user_id', (string) auth()->id())->first();
    }

    public function mount(): void
    {
        // Opening the full list also marks everything as read.
        $siswa = $this->siswa();
        if (! $siswa) {
            return;
        }

        $siswaId = (string) $siswa->_id;
        foreach (Pengumuman::all() as $p) {
            PengumumanRead::updateOrCreate(
                ['siswa_id' => $siswaId, 'pengumuman_id' => (string) $p->_id],
                ['read_at' => now()]
            );
        }
    }

    public function resetFilter(): void
    {
        $this->reset('filterDari', 'filterSampai');
    }

    public function render()
    {
        $query = Pengumuman::with('guru')->orderByDesc('created_at');

        if ($this->filterDari) {
            $query->where('created_at', '>=', \Carbon\Carbon::parse($this->filterDari)->startOfDay());
        }
        if ($this->filterSampai) {
            $query->where('created_at', '<=', \Carbon\Carbon::parse($this->filterSampai)->endOfDay());
        }

        return $this->view('livewire.siswa.pengumuman-list', [
            'items' => $query->get(),
        ], 'Pengumuman', 'Semua pengumuman dari sekolah');
    }
}