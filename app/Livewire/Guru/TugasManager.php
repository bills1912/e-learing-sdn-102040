<?php

namespace App\Livewire\Guru;

use App\Livewire\BaseComponent;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\PengumpulanTugas;
use App\Models\Tugas;

class TugasManager extends BaseComponent
{
    public bool $showModal = false;
    public ?string $editId = null;
    public ?string $deleteId = null;

    public string $judul_tugas = '';
    public string $mapel_id = '';
    public string $kelas_id = '';
    public string $deskripsi = '';
    public string $batas_waktu = '';

    protected function guruId(): ?string
    {
        return Guru::where('user_id', (string) auth()->id())->value('_id');
    }

    public function create(): void
    {
        $this->reset('judul_tugas', 'deskripsi', 'editId');
        $this->mapel_id = MataPelajaran::first()?->_id ?? '';
        $this->kelas_id = Kelas::first()?->_id ?? '';
        $this->batas_waktu = now()->addWeek()->format('Y-m-d\TH:i');
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $t = Tugas::findOrFail($id);
        $this->editId = $id;
        $this->judul_tugas = $t->judul_tugas;
        $this->mapel_id = (string) $t->mapel_id;
        $this->kelas_id = (string) $t->kelas_id;
        $this->deskripsi = $t->deskripsi;
        $this->batas_waktu = optional($t->batas_waktu)->format('Y-m-d\TH:i');
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'judul_tugas' => 'required|string|max:150',
            'mapel_id' => 'required|string',
            'kelas_id' => 'required|string',
            'deskripsi' => 'required|string',
            'batas_waktu' => 'required|date',
        ]);

        Tugas::updateOrCreate(['_id' => $this->editId], [
            'guru_id' => $this->guruId(),
            'mapel_id' => $this->mapel_id,
            'kelas_id' => $this->kelas_id,
            'judul_tugas' => $this->judul_tugas,
            'deskripsi' => $this->deskripsi,
            'batas_waktu' => \Carbon\Carbon::parse($this->batas_waktu),
        ]);

        $this->showModal = false;
        session()->flash('success', $this->editId ? 'Tugas berhasil diperbarui.' : 'Tugas baru berhasil diberikan.');
        $this->reset('judul_tugas', 'deskripsi', 'editId');
    }

    public function confirmDelete(string $id): void
    {
        $this->deleteId = $id;
    }

    public function delete(): void
    {
        $id = $this->deleteId;
        PengumpulanTugas::where('tugas_id', $id)->delete();
        Tugas::where('_id', $id)->delete();
        $this->deleteId = null;
        session()->flash('success', 'Tugas berhasil dihapus.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function render()
    {
        $items = Tugas::with('mapel', 'kelas')->where('guru_id', $this->guruId())->orderByDesc('created_at')->get();

        $items->each(function ($t) {
            $t->jumlah_kumpul = PengumpulanTugas::where('tugas_id', (string) $t->_id)->count();
            $t->jumlah_siswa = \App\Models\Siswa::where('kelas_id', (string) $t->kelas_id)->count();
        });

        return $this->view('livewire.guru.tugas-manager', [
            'items' => $items,
            'mapelList' => MataPelajaran::orderBy('nama_mapel')->get(),
            'kelasList' => Kelas::orderBy('nama_kelas')->get(),
        ], 'Tugas', 'Kelola tugas dan pantau pengumpulan siswa');
    }
}
