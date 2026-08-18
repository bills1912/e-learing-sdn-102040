<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use MongoDB\Laravel\Eloquent\Model;

class Materi extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'materi';

    protected $fillable = [
        'guru_id', 'mapel_id', 'kelas_id', 'judul_materi', 'isi_materi',
        'file_materi', 'file_name', 'tanggal_upload', 'modul_id',
    ];

    protected function casts(): array
    {
        return ['tanggal_upload' => 'datetime'];
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id', '_id');
    }

    public function mapel()
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id', '_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', '_id');
    }

    public function modul()
    {
        return $this->belongsTo(Modul::class, 'modul_id', '_id');
    }

    public function hasFile(): bool
    {
        return ! empty($this->file_materi);
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->hasFile() ? Storage::disk('public')->url($this->file_materi) : null;
    }

    public function getFileExtensionAttribute(): ?string
    {
        return $this->hasFile() ? strtolower(pathinfo($this->file_materi, PATHINFO_EXTENSION)) : null;
    }

    public function getFileKindAttribute(): string
    {
        return match ($this->file_extension) {
            'pdf' => 'pdf',
            'jpg', 'jpeg', 'png', 'gif', 'webp' => 'image',
            'doc', 'docx' => 'word',
            'ppt', 'pptx' => 'powerpoint',
            'xls', 'xlsx' => 'excel',
            default => 'file',
        };
    }
}