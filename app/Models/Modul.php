<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Modul extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'modul';

    protected $fillable = [
        'guru_id', 'mapel_id', 'kelas_id', 'judul_modul', 'deskripsi', 'urutan',
        'pretest_kuis_id', 'materi_id', 'posttest_kuis_id',
    ];

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

    public function pretest()
    {
        return $this->belongsTo(Kuis::class, 'pretest_kuis_id', '_id');
    }

    public function materi()
    {
        return $this->belongsTo(Materi::class, 'materi_id', '_id');
    }

    public function posttest()
    {
        return $this->belongsTo(Kuis::class, 'posttest_kuis_id', '_id');
    }

    /**
     * Progress status for a given siswa: how far they've gotten through
     * pretest -> materi -> posttest, and what's currently unlocked.
     */
    public function progressFor(string $siswaId): array
    {
        $pretestTotal = SoalKuis::where('kuis_id', $this->pretest_kuis_id)->count();
        $pretestDijawab = JawabanKuis::where('kuis_id', $this->pretest_kuis_id)->where('siswa_id', $siswaId)->count();
        $pretestSelesai = $pretestTotal > 0 && $pretestDijawab >= $pretestTotal;

        $materiDibaca = $pretestSelesai && MateriView::where('materi_id', $this->materi_id)->where('siswa_id', $siswaId)->exists();

        $posttestTotal = SoalKuis::where('kuis_id', $this->posttest_kuis_id)->count();
        $posttestDijawab = JawabanKuis::where('kuis_id', $this->posttest_kuis_id)->where('siswa_id', $siswaId)->count();
        $posttestSelesai = $materiDibaca && $posttestTotal > 0 && $posttestDijawab >= $posttestTotal;

        $posttestSkor = null;
        if ($posttestSelesai) {
            $benar = JawabanKuis::where('kuis_id', $this->posttest_kuis_id)->where('siswa_id', $siswaId)->where('benar', true)->count();
            $posttestSkor = $posttestTotal > 0 ? round($benar / $posttestTotal * 100) : 0;
        }

        $pretestSkor = null;
        if ($pretestSelesai) {
            $benar = JawabanKuis::where('kuis_id', $this->pretest_kuis_id)->where('siswa_id', $siswaId)->where('benar', true)->count();
            $pretestSkor = $pretestTotal > 0 ? round($benar / $pretestTotal * 100) : 0;
        }

        return [
            'pretest_selesai' => $pretestSelesai,
            'pretest_skor' => $pretestSkor,
            'materi_terkunci' => ! $pretestSelesai,
            'materi_dibaca' => $materiDibaca,
            'posttest_terkunci' => ! $materiDibaca,
            'posttest_selesai' => $posttestSelesai,
            'posttest_skor' => $posttestSkor,
            'modul_selesai' => $posttestSelesai,
        ];
    }
}
