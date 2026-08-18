<?php

namespace App\Livewire\Guru;

use App\Livewire\BaseComponent;
use App\Models\Kuis;
use App\Models\SoalKuis;

class SoalManager extends BaseComponent
{
    public string $kuisId;
    public bool $showModal = false;
    public ?string $editId = null;
    public ?string $deleteId = null;

    public string $pertanyaan = '';
    public string $pilihan_a = '';
    public string $pilihan_b = '';
    public string $pilihan_c = '';
    public string $pilihan_d = '';
    public string $jawaban_benar = 'A';

    public function mount(string $kuis): void
    {
        $this->kuisId = $kuis;
    }

    public function create(): void
    {
        $this->reset('pertanyaan', 'pilihan_a', 'pilihan_b', 'pilihan_c', 'pilihan_d', 'editId');
        $this->jawaban_benar = 'A';
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $s = SoalKuis::findOrFail($id);
        $this->editId = $id;
        $this->pertanyaan = $s->pertanyaan;
        $this->pilihan_a = $s->pilihan_a;
        $this->pilihan_b = $s->pilihan_b;
        $this->pilihan_c = $s->pilihan_c;
        $this->pilihan_d = $s->pilihan_d;
        $this->jawaban_benar = $s->jawaban_benar;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'pertanyaan' => 'required|string',
            'pilihan_a' => 'required|string|max:255',
            'pilihan_b' => 'required|string|max:255',
            'pilihan_c' => 'required|string|max:255',
            'pilihan_d' => 'required|string|max:255',
            'jawaban_benar' => 'required|in:A,B,C,D',
        ]);

        SoalKuis::updateOrCreate(['_id' => $this->editId], [
            'kuis_id' => $this->kuisId,
            'pertanyaan' => $this->pertanyaan,
            'pilihan_a' => $this->pilihan_a,
            'pilihan_b' => $this->pilihan_b,
            'pilihan_c' => $this->pilihan_c,
            'pilihan_d' => $this->pilihan_d,
            'jawaban_benar' => $this->jawaban_benar,
        ]);

        $this->showModal = false;
        session()->flash('success', $this->editId ? 'Soal berhasil diperbarui.' : 'Soal baru berhasil ditambahkan.');
        $this->reset('pertanyaan', 'pilihan_a', 'pilihan_b', 'pilihan_c', 'pilihan_d', 'editId');
    }

    public function confirmDelete(string $id): void
    {
        $this->deleteId = $id;
    }

    public function delete(): void
    {
        SoalKuis::where('_id', $this->deleteId)->delete();
        $this->deleteId = null;
        session()->flash('success', 'Soal berhasil dihapus.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function render()
    {
        return $this->view('livewire.guru.soal-manager', [
            'kuis' => Kuis::with('mapel', 'kelas')->findOrFail($this->kuisId),
            'items' => SoalKuis::where('kuis_id', $this->kuisId)->orderBy('created_at')->get(),
        ], 'Kelola Soal', 'Tambahkan soal pilihan ganda untuk kuis ini');
    }
}
