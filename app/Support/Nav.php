<?php

namespace App\Support;

class Nav
{
    public static function items(string $role): array
    {
        return match ($role) {
            'admin' => [
                ['label' => 'Dashboard', 'url' => route('admin.dashboard'), 'active' => 'admin.dashboard', 'icon' => Icons::svg('home')],
                ['label' => 'Data Guru', 'url' => route('admin.guru.index'), 'active' => 'admin.guru.*', 'icon' => Icons::svg('briefcase')],
                ['label' => 'Data Siswa', 'url' => route('admin.siswa.index'), 'active' => 'admin.siswa.*', 'icon' => Icons::svg('user-graduate')],
                ['label' => 'Kelas', 'url' => route('admin.kelas.index'), 'active' => 'admin.kelas.*', 'icon' => Icons::svg('grid')],
                ['label' => 'Mata Pelajaran', 'url' => route('admin.mapel.index'), 'active' => 'admin.mapel.*', 'icon' => Icons::svg('book')],
            ],
            'guru' => [
                ['label' => 'Dashboard', 'url' => route('guru.dashboard'), 'active' => 'guru.dashboard', 'icon' => Icons::svg('home')],
                ['label' => 'Modul', 'url' => route('guru.modul.index'), 'active' => 'guru.modul.*', 'icon' => Icons::svg('academic-cap')],
                ['label' => 'Materi', 'url' => route('guru.materi.index'), 'active' => 'guru.materi.*', 'icon' => Icons::svg('book')],
                ['label' => 'Tugas', 'url' => route('guru.tugas.index'), 'active' => 'guru.tugas.*', 'icon' => Icons::svg('clipboard')],
                ['label' => 'Kuis', 'url' => route('guru.kuis.index'), 'active' => 'guru.kuis.*', 'icon' => Icons::svg('quiz')],
                ['label' => 'Rekap Nilai', 'url' => route('guru.nilai.index'), 'active' => 'guru.nilai.*', 'icon' => Icons::svg('chart')],
                ['label' => 'Pengumuman', 'url' => route('guru.pengumuman.index'), 'active' => 'guru.pengumuman.*', 'icon' => Icons::svg('bell')],
            ],
            'siswa' => [
                ['label' => 'Dashboard', 'url' => route('siswa.dashboard'), 'active' => 'siswa.dashboard', 'icon' => Icons::svg('home')],
                ['label' => 'Modul', 'url' => route('siswa.modul.index'), 'active' => 'siswa.modul.*', 'icon' => Icons::svg('academic-cap')],
                ['label' => 'Materi', 'url' => route('siswa.materi.index'), 'active' => 'siswa.materi.*', 'icon' => Icons::svg('book')],
                ['label' => 'Tugas', 'url' => route('siswa.tugas.index'), 'active' => 'siswa.tugas.*', 'icon' => Icons::svg('clipboard')],
                ['label' => 'Kuis', 'url' => route('siswa.kuis.index'), 'active' => 'siswa.kuis.*', 'icon' => Icons::svg('quiz')],
                ['label' => 'Nilai Saya', 'url' => route('siswa.nilai.index'), 'active' => 'siswa.nilai.*', 'icon' => Icons::svg('trophy')],
            ],
            default => [],
        };
    }
}