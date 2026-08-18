<?php

namespace App\Livewire\Admin;

use App\Livewire\BaseComponent;
use App\Models\Kelas;
use Livewire\Attributes\Validate;

class KelasManager extends BaseComponent
{
    public bool $showModal = false;
    public ?string $editId = null;

    #[Validate('required|string|max:100')]
    public string $nama_kelas = '';

    #[Validate('required|string|max:20')]
    public string $tingkat = '';

    public ?string $deleteId = null;

    public function create(): void
    {
        $this->reset('nama_kelas', 'tingkat', 'editId');
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $k = Kelas::findOrFail($id);
        $this->editId = $id;
        $this->nama_kelas = $k->nama_kelas;
        $this->tingkat = $k->tingkat;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        Kelas::updateOrCreate(['_id' => $this->editId], [
            'nama_kelas' => $this->nama_kelas,
            'tingkat' => $this->tingkat,
        ]);

        $this->showModal = false;
        session()->flash('success', $this->editId ? 'Kelas berhasil diperbarui.' : 'Kelas baru berhasil ditambahkan.');
        $this->reset('nama_kelas', 'tingkat', 'editId');
    }

    public function confirmDelete(string $id): void
    {
        $this->deleteId = $id;
    }

    public function delete(): void
    {
        Kelas::where('_id', $this->deleteId)->delete();
        $this->deleteId = null;
        session()->flash('success', 'Kelas berhasil dihapus.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function render()
    {
        return $this->view('livewire.admin.kelas-manager', [
            'items' => Kelas::orderBy('nama_kelas')->get()->each(function ($k) {
                $k->siswa_count = \App\Models\Siswa::where('kelas_id', (string) $k->_id)->count();
            }),
        ], 'Data Kelas', 'Kelola kelas yang tersedia di sekolah');
    }
}
