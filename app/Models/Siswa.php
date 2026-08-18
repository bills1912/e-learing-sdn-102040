<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Siswa extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'siswa';

    protected $fillable = [
        'user_id', 'kelas_id', 'nis', 'nama_siswa', 'jenis_kelamin', 'alamat',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', '_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', '_id');
    }

    public function pengumpulanTugas()
    {
        return $this->hasMany(PengumpulanTugas::class, 'siswa_id', '_id');
    }

    public function jawabanKuis()
    {
        return $this->hasMany(JawabanKuis::class, 'siswa_id', '_id');
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class, 'siswa_id', '_id');
    }
}
