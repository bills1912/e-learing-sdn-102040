<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class MateriView extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'materi_view';

    protected $fillable = ['siswa_id', 'materi_id', 'viewed_at'];

    protected function casts(): array
    {
        return ['viewed_at' => 'datetime'];
    }
}
