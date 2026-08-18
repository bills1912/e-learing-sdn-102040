<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Pengumuman extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'pengumuman';

    protected $fillable = ['guru_id', 'judul', 'isi'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime', 'updated_at' => 'datetime'];
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id', '_id');
    }
}
