<?php

namespace App\Livewire\Guru;

use App\Livewire\BaseComponent;
use App\Models\Kuis;
use App\Models\Modul;
use App\Models\SoalKuis;
use Livewire\Attributes\Url;

class ModulSoalManager extends BaseComponent
{
    public string $modulId;

    #[Url(as: 'tab')]
    public string $activeTab = 'pretest'; // 'pretest' | 'posttest'

    public bool $showModal = false;
    public ?string $editId = null;
    public ?string $deleteId = null;
    public bool $showDurasiModal = false;

    public string $pertanyaan = '';
    public string $pilihan_a = '';
    public string $pilihan_b = '';
    public string $pilihan_c = '';
    public string $pilihan_d = '';
    public string $jawaban_benar = 'A';
    public int $durasi_menit = 15;

    public function mount(string $modulId): void
    {
        $this->modulId = $modulId;

        if (! in_array($this->activeTab, ['pretest', 'posttest'], true)) {
            $this->activeTab = 'pretest';
        }
    }

    protected function modul(): Modul
    {
        return Modul::with('mapel', 'kelas')->findOrFail($this->modulId);
    }

    protected function currentKuisId(): string
    {
        $modul = $this->modul();

        return $this->activeTab === 'posttest' ? $modul->posttest_kuis_id : $modul->pretest_kuis_id;
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->closeModal();
    }

    public function editDurasi(): void
    {
        $this->durasi_menit = Kuis::find($this->currentKuisId())?->durasi_menit ?? 15;
        $this->showDurasiModal = true;
    }

    public function saveDurasi(): void
    {
        $this->validate(['durasi_menit' => 'required|integer|min:1|max:180']);

        Kuis::where('_id', $this->currentKuisId())->update(['durasi_menit' => $this->durasi_menit]);

        $this->showDurasiModal = false;
        session()->flash('success', 'Durasi waktu pengerjaan berhasil diperbarui.');
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
            'kuis_id' => $this->currentKuisId(),
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
        $this->showDurasiModal = false;
    }

    public function render()
    {
        $modul = $this->modul();
        $currentKuis = Kuis::find($this->currentKuisId());

        return $this->view('livewire.guru.modul-soal-manager', [
            'modul' => $modul,
            'currentKuis' => $currentKuis,
            'items' => SoalKuis::where('kuis_id', $this->currentKuisId())->orderBy('created_at')->get(),
            'pretestCount' => SoalKuis::where('kuis_id', $modul->pretest_kuis_id)->count(),
            'posttestCount' => SoalKuis::where('kuis_id', $modul->posttest_kuis_id)->count(),
        ], 'Kelola Soal Modul', $modul->judul_modul);
    }
}