<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class JawabanKuis extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'jawaban_kuis';

    protected $fillable = [
        'kuis_id', 'soal_id', 'siswa_id', 'jawaban', 'benar',
    ];

    protected function casts(): array
    {
        return ['benar' => 'boolean'];
    }

    public function kuis()
    {
        return $this->belongsTo(Kuis::class, 'kuis_id', '_id');
    }

    public function soal()
    {
        return $this->belongsTo(SoalKuis::class, 'soal_id', '_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', '_id');
    }
}
