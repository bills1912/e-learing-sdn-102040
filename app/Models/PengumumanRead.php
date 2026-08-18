<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class PengumumanRead extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'pengumuman_read';

    protected $fillable = ['siswa_id', 'pengumuman_id', 'read_at'];

    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }
}
