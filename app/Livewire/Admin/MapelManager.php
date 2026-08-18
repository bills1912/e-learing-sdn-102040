<?php

namespace App\Livewire\Admin;

use App\Livewire\BaseComponent;
use App\Models\MataPelajaran;
use Livewire\Attributes\Validate;

class MapelManager extends BaseComponent
{
    public bool $showModal = false;
    public ?string $editId = null;
    public ?string $deleteId = null;

    #[Validate('required|string|max:100')]
    public string $nama_mapel = '';

    #[Validate('nullable|string|max:500')]
    public string $deskripsi = '';

    protected array $iconPool = ['book', 'academic-cap', 'quiz', 'chart', 'grid', 'clipboard'];

    public function create(): void
    {
        $this->reset('nama_mapel', 'deskripsi', 'editId');
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $m = MataPelajaran::findOrFail($id);
        $this->editId = $id;
        $this->nama_mapel = $m->nama_mapel;
        $this->deskripsi = $m->deskripsi ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        MataPelajaran::updateOrCreate(['_id' => $this->editId], [
            'nama_mapel' => $this->nama_mapel,
            'deskripsi' => $this->deskripsi,
            'icon' => $this->iconPool[array_rand($this->iconPool)],
        ]);

        $this->showModal = false;
        session()->flash('success', $this->editId ? 'Mata pelajaran berhasil diperbarui.' : 'Mata pelajaran baru berhasil ditambahkan.');
        $this->reset('nama_mapel', 'deskripsi', 'editId');
    }

    public function confirmDelete(string $id): void
    {
        $this->deleteId = $id;
    }

    public function delete(): void
    {
        MataPelajaran::where('_id', $this->deleteId)->delete();
        $this->deleteId = null;
        session()->flash('success', 'Mata pelajaran berhasil dihapus.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function render()
    {
        return $this->view('livewire.admin.mapel-manager', [
            'items' => MataPelajaran::orderBy('nama_mapel')->get(),
        ], 'Mata Pelajaran', 'Kelola daftar mata pelajaran');
    }
}
