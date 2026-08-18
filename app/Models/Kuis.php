<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Kuis extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'kuis';

    protected $fillable = [
        'guru_id', 'mapel_id', 'kelas_id', 'judul_kuis', 'deskripsi',
        'waktu_mulai', 'waktu_selesai', 'durasi_menit', 'modul_id', 'peran',
    ];

    protected function casts(): array
    {
        return [
            'waktu_mulai' => 'datetime',
            'waktu_selesai' => 'datetime',
        ];
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id', '_id');
    }

    public function mapel()
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id', '_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', '_id');
    }

    public function soal()
    {
        return $this->hasMany(SoalKuis::class, 'kuis_id', '_id');
    }

    public function jawaban()
    {
        return $this->hasMany(JawabanKuis::class, 'kuis_id', '_id');
    }

    public function isBerlangsung(): bool
    {
        $now = now();
        return $this->waktu_mulai <= $now && $this->waktu_selesai >= $now;
    }
}