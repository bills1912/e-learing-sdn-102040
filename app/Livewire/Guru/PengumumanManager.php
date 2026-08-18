<?php

namespace App\Livewire\Guru;

use App\Livewire\BaseComponent;
use App\Models\Guru;
use App\Models\Pengumuman;
use App\Models\PengumumanRead;

class PengumumanManager extends BaseComponent
{
    public bool $showModal = false;
    public ?string $editId = null;
    public ?string $deleteId = null;

    public string $judul = '';
    public string $isi = '';

    public string $filterDari = '';
    public string $filterSampai = '';

    protected function guruId(): ?string
    {
        return Guru::where('user_id', (string) auth()->id())->value('_id');
    }

    public function create(): void
    {
        $this->reset('judul', 'isi', 'editId');
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $p = Pengumuman::findOrFail($id);

        if ((string) $p->guru_id !== (string) $this->guruId()) {
            abort(403, 'Anda hanya dapat mengubah pengumuman Anda sendiri.');
        }

        $this->editId = $id;
        $this->judul = $p->judul;
        $this->isi = $p->isi;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'judul' => 'required|string|max:150',
            'isi' => 'required|string|max:1000',
        ]);

        if ($this->editId) {
            Pengumuman::where('_id', $this->editId)->update([
                'judul' => $this->judul,
                'isi' => $this->isi,
            ]);
            session()->flash('success', 'Pengumuman berhasil diperbarui.');
        } else {
            Pengumuman::create([
                'guru_id' => $this->guruId(),
                'judul' => $this->judul,
                'isi' => $this->isi,
            ]);
            session()->flash('success', 'Pengumuman berhasil dibroadcast ke seluruh siswa.');
        }

        $this->showModal = false;
        $this->reset('judul', 'isi', 'editId');
    }

    public function confirmDelete(string $id): void
    {
        $p = Pengumuman::find($id);
        if ($p && (string) $p->guru_id !== (string) $this->guruId()) {
            abort(403, 'Anda hanya dapat menghapus pengumuman Anda sendiri.');
        }
        $this->deleteId = $id;
    }

    public function delete(): void
    {
        PengumumanRead::where('pengumuman_id', $this->deleteId)->delete();
        Pengumuman::where('_id', $this->deleteId)->delete();
        $this->deleteId = null;
        session()->flash('success', 'Pengumuman berhasil dihapus.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
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

        return $this->view('livewire.guru.pengumuman-manager', [
            'items' => $query->get(),
            'myGuruId' => (string) $this->guruId(),
        ], 'Pengumuman', 'Broadcast pesan ke seluruh siswa — tampil di dashboard mereka');
    }
}