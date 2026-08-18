<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Kelas extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'kelas';

    protected $fillable = ['nama_kelas', 'tingkat', 'wali_kelas_id'];

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'kelas_id', '_id');
    }
}
