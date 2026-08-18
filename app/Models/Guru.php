<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Guru extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'guru';

    protected $fillable = [
        'user_id', 'nip', 'nama_guru', 'jenis_kelamin', 'alamat', 'no_hp',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', '_id');
    }

    public function materi()
    {
        return $this->hasMany(Materi::class, 'guru_id', '_id');
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class, 'guru_id', '_id');
    }

    public function kuis()
    {
        return $this->hasMany(Kuis::class, 'guru_id', '_id');
    }
}
