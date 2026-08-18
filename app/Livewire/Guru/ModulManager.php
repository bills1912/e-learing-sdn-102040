<?php

namespace App\Livewire\Guru;

use App\Livewire\BaseComponent;
use App\Models\Guru;
use App\Models\JawabanKuis;
use App\Models\Kuis;
use App\Models\MataPelajaran;
use App\Models\Materi;
use App\Models\Modul;
use App\Models\SoalKuis;

class ModulManager extends BaseComponent
{
    public bool $showModal = false;
    public ?string $editId = null;
    public ?string $deleteId = null;

    public string $materi_id = '';
    public string $deskripsi = '';

    // Read-only info shown while editing (materi link cannot be changed after creation).
    public string $editingMateriTitle = '';

    protected function guruId(): ?string
    {
        return Guru::where('user_id', (string) auth()->id())->value('_id');
    }

    public function mount(): void
    {
        // Coming from the "Buat Modul" button on a specific Materi card:
        // pre-select that materi and open the create form right away.
        $preselect = request()->query('materi_id');
        if ($preselect && Materi::where('_id', $preselect)->whereNull('modul_id')->exists()) {
            $this->materi_id = $preselect;
            $this->deskripsi = '';
            $this->editId = null;
            $this->showModal = true;
        }
    }

    public function create(): void
    {
        $this->reset('deskripsi', 'editId', 'materi_id');
        $this->materi_id = $this->availableMateri()->first()?->_id ?? '';
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $m = Modul::findOrFail($id);
        $this->editId = $id;
        $this->deskripsi = $m->deskripsi ?? '';
        $this->editingMateriTitle = Materi::find($m->materi_id)?->judul_materi ?? '-';
        $this->showModal = true;
    }

    protected function availableMateri()
    {
        // Materi belonging to this guru that aren't already wrapped in a module
        // (or, when editing, also include the one already linked to this module).
        return Materi::where('guru_id', $this->guruId())
            ->when($this->editId, function ($q) {
                $modul = Modul::find($this->editId);
                $q->where(fn ($q2) => $q2->whereNull('modul_id')->orWhere('_id', $modul?->materi_id));
            }, fn ($q) => $q->whereNull('modul_id'))
            ->orderByDesc('created_at')
            ->get();
    }

    public function save(): void
    {
        if ($this->editId) {
            $this->validate(['deskripsi' => 'nullable|string|max:500']);

            Modul::where('_id', $this->editId)->update(['deskripsi' => $this->deskripsi]);
            session()->flash('success', 'Modul berhasil diperbarui.');
        } else {
            $this->validate([
                'materi_id' => 'required|string',
                'deskripsi' => 'nullable|string|max:500',
            ]);

            $materi = Materi::find($this->materi_id);
            if (! $materi) {
                $this->addError('materi_id', 'Materi tidak ditemukan.');
                return;
            }
            if ($materi->modul_id) {
                $this->addError('materi_id', 'Materi ini sudah menjadi bagian dari modul lain.');
                return;
            }

            $urutan = Modul::where('mapel_id', $materi->mapel_id)->where('kelas_id', $materi->kelas_id)->count() + 1;

            $pretest = Kuis::create([
                'guru_id' => $this->guruId(), 'mapel_id' => $materi->mapel_id, 'kelas_id' => $materi->kelas_id,
                'judul_kuis' => 'Pre-Test: '.$materi->judul_materi,
                'deskripsi' => 'Kerjakan pre-test ini sebelum membaca materi.',
                'waktu_mulai' => now(), 'waktu_selesai' => now()->addYear(), 'durasi_menit' => 15,
                'peran' => 'pretest',
            ]);

            $posttest = Kuis::create([
                'guru_id' => $this->guruId(), 'mapel_id' => $materi->mapel_id, 'kelas_id' => $materi->kelas_id,
                'judul_kuis' => 'Post-Test: '.$materi->judul_materi,
                'deskripsi' => 'Kerjakan post-test ini setelah membaca materi.',
                'waktu_mulai' => now(), 'waktu_selesai' => now()->addYear(), 'durasi_menit' => 15,
                'peran' => 'posttest',
            ]);

            $modul = Modul::create([
                'guru_id' => $this->guruId(), 'mapel_id' => $materi->mapel_id, 'kelas_id' => $materi->kelas_id,
                'judul_modul' => $materi->judul_materi, 'deskripsi' => $this->deskripsi, 'urutan' => $urutan,
                'pretest_kuis_id' => (string) $pretest->_id,
                'materi_id' => (string) $materi->_id,
                'posttest_kuis_id' => (string) $posttest->_id,
            ]);

            $pretest->update(['modul_id' => (string) $modul->_id]);
            $posttest->update(['modul_id' => (string) $modul->_id]);
            $materi->update(['modul_id' => (string) $modul->_id]);

            session()->flash('success', 'Modul dibuat dari materi "'.$materi->judul_materi.'". Sekarang tambahkan soal pre-test dan post-test.');
        }

        $this->showModal = false;
        $this->reset('deskripsi', 'editId', 'materi_id');
    }

    public function confirmDelete(string $id): void
    {
        $this->deleteId = $id;
    }

    public function delete(): void
    {
        $modul = Modul::find($this->deleteId);
        if ($modul) {
            // Pre-test / post-test only exist for this module, so they're removed with it.
            foreach ([$modul->pretest_kuis_id, $modul->posttest_kuis_id] as $kuisId) {
                if ($kuisId) {
                    SoalKuis::where('kuis_id', $kuisId)->delete();
                    JawabanKuis::where('kuis_id', $kuisId)->delete();
                    Kuis::where('_id', $kuisId)->delete();
                }
            }
            // The materi existed independently before joining this module, so it's
            // unlinked (not deleted) and returns to the regular Materi list.
            if ($modul->materi_id) {
                Materi::where('_id', $modul->materi_id)->update(['modul_id' => null]);
            }
            $modul->delete();
        }
        $this->deleteId = null;
        session()->flash('success', 'Modul berhasil dihapus. Materinya tetap tersimpan di menu Materi.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function render()
    {
        $items = Modul::with('mapel', 'kelas')->where('guru_id', $this->guruId())->orderBy('mapel_id')->orderBy('urutan')->get();

        $items->each(function ($m) {
            $m->jumlah_soal_pretest = SoalKuis::where('kuis_id', $m->pretest_kuis_id)->count();
            $m->jumlah_soal_posttest = SoalKuis::where('kuis_id', $m->posttest_kuis_id)->count();
            $m->materiObj = Materi::find($m->materi_id);
        });

        return $this->view('livewire.guru.modul-manager', [
            'items' => $items,
            'availableMateri' => $this->availableMateri(),
        ], 'Modul Pembelajaran', 'Kelola modul: pre-test, materi, dan post-test per mata pelajaran');
    }
}