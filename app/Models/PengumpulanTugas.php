<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class PengumpulanTugas extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'pengumpulan_tugas';

    protected $fillable = [
        'tugas_id', 'siswa_id', 'file_jawaban', 'keterangan',
        'tanggal_kumpul', 'nilai', 'feedback', 'status',
    ];

    protected function casts(): array
    {
        return ['tanggal_kumpul' => 'datetime'];
    }

    public function tugas()
    {
        return $this->belongsTo(Tugas::class, 'tugas_id', '_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', '_id');
    }
}
