<?php

namespace App\Livewire\Guru;

use App\Livewire\BaseComponent;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Materi;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class MateriManager extends BaseComponent
{
    use WithFileUploads;

    public bool $showModal = false;
    public ?string $editId = null;
    public ?string $deleteId = null;

    public string $judul_materi = '';
    public string $mapel_id = '';
    public string $kelas_id = '';
    public string $isi_materi = '';

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    public $file = null;
    public ?string $existingFileName = null;
    public ?string $existingFilePath = null;

    public string $filterKelas = '';

    protected function guruId(): ?string
    {
        return Guru::where('user_id', (string) auth()->id())->value('_id');
    }

    public function create(): void
    {
        $this->reset('judul_materi', 'isi_materi', 'editId', 'file', 'existingFileName', 'existingFilePath');
        $this->mapel_id = MataPelajaran::first()?->_id ?? '';
        $this->kelas_id = Kelas::first()?->_id ?? '';
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $m = Materi::findOrFail($id);
        $this->editId = $id;
        $this->judul_materi = $m->judul_materi;
        $this->mapel_id = (string) $m->mapel_id;
        $this->kelas_id = (string) $m->kelas_id;
        $this->isi_materi = $m->isi_materi;
        $this->file = null;
        $this->existingFileName = $m->file_name;
        $this->existingFilePath = $m->file_materi;
        $this->showModal = true;
    }

    public function removeExistingFile(): void
    {
        $this->existingFileName = null;
        $this->existingFilePath = null;
    }

    public function save(): void
    {
        $this->validate([
            'judul_materi' => 'required|string|max:150',
            'mapel_id' => 'required|string',
            'kelas_id' => 'required|string',
            'isi_materi' => 'required|string',
            'file' => 'nullable|file|max:10240|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png',
        ]);

        $data = [
            'guru_id' => $this->guruId(),
            'mapel_id' => $this->mapel_id,
            'kelas_id' => $this->kelas_id,
            'judul_materi' => $this->judul_materi,
            'isi_materi' => $this->isi_materi,
            'tanggal_upload' => now(),
        ];

        if ($this->file) {
            // Replacing: remove the old stored file first.
            if ($this->editId) {
                $old = Materi::find($this->editId);
                if ($old && $old->file_materi) {
                    Storage::disk('public')->delete($old->file_materi);
                }
            }
            $data['file_materi'] = $this->file->store('materi', 'public');
            $data['file_name'] = $this->file->getClientOriginalName();
        } elseif ($this->editId && ! $this->existingFilePath) {
            // User removed the attachment without uploading a replacement.
            $old = Materi::find($this->editId);
            if ($old && $old->file_materi) {
                Storage::disk('public')->delete($old->file_materi);
            }
            $data['file_materi'] = null;
            $data['file_name'] = null;
        }

        Materi::updateOrCreate(['_id' => $this->editId], $data);

        $this->showModal = false;
        session()->flash('success', $this->editId ? 'Materi berhasil diperbarui.' : 'Materi baru berhasil diunggah.');
        $this->reset('judul_materi', 'isi_materi', 'editId', 'file', 'existingFileName', 'existingFilePath');
    }

    public function confirmDelete(string $id): void
    {
        $this->deleteId = $id;
    }

    public function delete(): void
    {
        $m = Materi::find($this->deleteId);
        if ($m && $m->file_materi) {
            Storage::disk('public')->delete($m->file_materi);
        }
        Materi::where('_id', $this->deleteId)->delete();
        $this->deleteId = null;
        session()->flash('success', 'Materi berhasil dihapus.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function render()
    {
        $query = Materi::with('mapel', 'kelas')->where('guru_id', $this->guruId())->orderByDesc('created_at');

        if ($this->filterKelas) {
            $query->where('kelas_id', $this->filterKelas);
        }

        return $this->view('livewire.guru.materi-manager', [
            'items' => $query->get(),
            'mapelList' => MataPelajaran::orderBy('nama_mapel')->get(),
            'kelasList' => Kelas::orderBy('nama_kelas')->get(),
        ], 'Materi Pembelajaran', 'Kelola materi untuk siswa Anda');
    }
}