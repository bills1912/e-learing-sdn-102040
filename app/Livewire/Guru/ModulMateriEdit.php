<?php

namespace App\Livewire\Guru;

use App\Livewire\BaseComponent;
use App\Models\Materi;
use App\Models\Modul;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class ModulMateriEdit extends BaseComponent
{
    use WithFileUploads;

    public string $materiId;
    public string $judul_materi = '';
    public string $isi_materi = '';

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    public $file = null;
    public ?string $existingFileName = null;
    public ?string $existingFilePath = null;

    public function mount(string $materiId): void
    {
        $materi = Materi::findOrFail($materiId);
        $this->materiId = $materiId;
        $this->judul_materi = $materi->judul_materi;
        $this->isi_materi = $materi->isi_materi;
        $this->existingFileName = $materi->file_name;
        $this->existingFilePath = $materi->file_materi;
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
            'isi_materi' => 'required|string',
            'file' => 'nullable|file|max:10240|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png',
        ]);

        $materi = Materi::findOrFail($this->materiId);
        $data = [
            'judul_materi' => $this->judul_materi,
            'isi_materi' => $this->isi_materi,
            'tanggal_upload' => now(),
        ];

        if ($this->file) {
            if ($materi->file_materi) {
                Storage::disk('public')->delete($materi->file_materi);
            }
            $data['file_materi'] = $this->file->store('materi', 'public');
            $data['file_name'] = $this->file->getClientOriginalName();
        } elseif (! $this->existingFilePath) {
            if ($materi->file_materi) {
                Storage::disk('public')->delete($materi->file_materi);
            }
            $data['file_materi'] = null;
            $data['file_name'] = null;
        }

        $materi->update($data);

        session()->flash('success', 'Materi modul berhasil disimpan.');
        $this->redirect(route('guru.modul.index'), navigate: true);
    }

    public function render()
    {
        $modul = Modul::where('materi_id', $this->materiId)->first();

        return $this->view('livewire.guru.modul-materi-edit', [
            'modul' => $modul,
        ], 'Isi Materi Modul', $modul->judul_modul ?? 'Materi');
    }
}
