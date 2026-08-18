<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class SoalKuis extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'soal_kuis';

    protected $fillable = [
        'kuis_id', 'pertanyaan', 'pilihan_a', 'pilihan_b', 'pilihan_c',
        'pilihan_d', 'jawaban_benar',
    ];

    public function kuis()
    {
        return $this->belongsTo(Kuis::class, 'kuis_id', '_id');
    }
}
