<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Nilai extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'nilai';

    protected $fillable = [
        'siswa_id', 'mapel_id', 'tugas_id', 'kuis_id', 'jenis',
        'nilai', 'keterangan',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', '_id');
    }

    public function mapel()
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id', '_id');
    }
}
