<?php

namespace App\Livewire\Guru;

use App\Livewire\BaseComponent;
use App\Models\Guru;
use App\Models\JawabanKuis;
use App\Models\Kelas;
use App\Models\Kuis;
use App\Models\MataPelajaran;
use App\Models\SoalKuis;

class KuisManager extends BaseComponent
{
    public bool $showModal = false;
    public ?string $editId = null;
    public ?string $deleteId = null;

    public string $judul_kuis = '';
    public string $mapel_id = '';
    public string $kelas_id = '';
    public string $deskripsi = '';
    public string $waktu_mulai = '';
    public string $waktu_selesai = '';
    public int $durasi_menit = 20;

    protected function guruId(): ?string
    {
        return Guru::where('user_id', (string) auth()->id())->value('_id');
    }

    public function create(): void
    {
        $this->reset('judul_kuis', 'deskripsi', 'editId');
        $this->mapel_id = MataPelajaran::first()?->_id ?? '';
        $this->kelas_id = Kelas::first()?->_id ?? '';
        $this->waktu_mulai = now()->format('Y-m-d\TH:i');
        $this->waktu_selesai = now()->addWeek()->format('Y-m-d\TH:i');
        $this->durasi_menit = 20;
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $k = Kuis::findOrFail($id);
        $this->editId = $id;
        $this->judul_kuis = $k->judul_kuis;
        $this->mapel_id = (string) $k->mapel_id;
        $this->kelas_id = (string) $k->kelas_id;
        $this->deskripsi = $k->deskripsi;
        $this->waktu_mulai = optional($k->waktu_mulai)->format('Y-m-d\TH:i');
        $this->waktu_selesai = optional($k->waktu_selesai)->format('Y-m-d\TH:i');
        $this->durasi_menit = $k->durasi_menit ?? 20;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'judul_kuis' => 'required|string|max:150',
            'mapel_id' => 'required|string',
            'kelas_id' => 'required|string',
            'deskripsi' => 'nullable|string',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
            'durasi_menit' => 'required|integer|min:1|max:180',
        ]);

        $kuis = Kuis::updateOrCreate(['_id' => $this->editId], [
            'guru_id' => $this->guruId(),
            'mapel_id' => $this->mapel_id,
            'kelas_id' => $this->kelas_id,
            'judul_kuis' => $this->judul_kuis,
            'deskripsi' => $this->deskripsi,
            'waktu_mulai' => \Carbon\Carbon::parse($this->waktu_mulai),
            'waktu_selesai' => \Carbon\Carbon::parse($this->waktu_selesai),
            'durasi_menit' => $this->durasi_menit,
        ]);

        $this->showModal = false;
        session()->flash('success', $this->editId ? 'Kuis berhasil diperbarui.' : 'Kuis baru berhasil dibuat. Tambahkan soal sekarang.');
        $this->reset('judul_kuis', 'deskripsi', 'editId');

        if (! $this->editId) {
            $this->redirect(route('guru.kuis.soal', $kuis->_id), navigate: true);
        }
    }

    public function confirmDelete(string $id): void
    {
        $this->deleteId = $id;
    }

    public function delete(): void
    {
        $id = $this->deleteId;
        SoalKuis::where('kuis_id', $id)->delete();
        JawabanKuis::where('kuis_id', $id)->delete();
        Kuis::where('_id', $id)->delete();
        $this->deleteId = null;
        session()->flash('success', 'Kuis berhasil dihapus.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function render()
    {
        $items = Kuis::with('mapel', 'kelas')->where('guru_id', $this->guruId())->whereNull('modul_id')->orderByDesc('created_at')->get();

        $items->each(function ($k) {
            $k->jumlah_soal = SoalKuis::where('kuis_id', (string) $k->_id)->count();
            $k->jumlah_selesai = JawabanKuis::where('kuis_id', (string) $k->_id)->pluck('siswa_id')->unique()->count();
        });

        return $this->view('livewire.guru.kuis-manager', [
            'items' => $items,
            'mapelList' => MataPelajaran::orderBy('nama_mapel')->get(),
            'kelasList' => Kelas::orderBy('nama_kelas')->get(),
        ], 'Kuis', 'Buat dan kelola kuis interaktif untuk siswa');
    }
}