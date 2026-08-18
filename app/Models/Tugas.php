<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Tugas extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'tugas';

    protected $fillable = [
        'guru_id', 'mapel_id', 'kelas_id', 'judul_tugas', 'deskripsi',
        'file_tugas', 'batas_waktu',
    ];

    protected function casts(): array
    {
        return ['batas_waktu' => 'datetime'];
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

    public function pengumpulan()
    {
        return $this->hasMany(PengumpulanTugas::class, 'tugas_id', '_id');
    }
}
