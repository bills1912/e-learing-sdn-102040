<?php

use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route(match (auth()->user()->role) {
            'admin' => 'admin.dashboard',
            'guru' => 'guru.dashboard',
            'siswa' => 'siswa.dashboard',
            default => 'login',
        });
    }

    return redirect()->route('login');
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout')->middleware('auth');

// ================= ADMIN =================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', \App\Livewire\Admin\Dashboard::class)->name('dashboard');
    Route::get('/guru', \App\Livewire\Admin\GuruManager::class)->name('guru.index');
    Route::get('/siswa', \App\Livewire\Admin\SiswaManager::class)->name('siswa.index');
    Route::get('/kelas', \App\Livewire\Admin\KelasManager::class)->name('kelas.index');
    Route::get('/mapel', \App\Livewire\Admin\MapelManager::class)->name('mapel.index');
});

// ================= GURU =================
Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/', \App\Livewire\Guru\Dashboard::class)->name('dashboard');
    Route::get('/modul', \App\Livewire\Guru\ModulManager::class)->name('modul.index');
    Route::get('/modul/materi/{materiId}', \App\Livewire\Guru\ModulMateriEdit::class)->name('modul.materi');
    Route::get('/modul/{modulId}/soal', \App\Livewire\Guru\ModulSoalManager::class)->name('modul.soal');
    Route::get('/materi', \App\Livewire\Guru\MateriManager::class)->name('materi.index');
    Route::get('/tugas', \App\Livewire\Guru\TugasManager::class)->name('tugas.index');
    Route::get('/tugas/{tugas}/pengumpulan', \App\Livewire\Guru\PengumpulanManager::class)->name('tugas.pengumpulan');
    Route::get('/kuis', \App\Livewire\Guru\KuisManager::class)->name('kuis.index');
    Route::get('/kuis/{kuis}/soal', \App\Livewire\Guru\SoalManager::class)->name('kuis.soal');
    Route::get('/kuis/{kuis}/hasil', \App\Livewire\Guru\HasilKuis::class)->name('kuis.hasil');
    Route::get('/nilai', \App\Livewire\Guru\NilaiOverview::class)->name('nilai.index');
    Route::get('/pengumuman', \App\Livewire\Guru\PengumumanManager::class)->name('pengumuman.index');
});

// ================= SISWA =================
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/', \App\Livewire\Siswa\Dashboard::class)->name('dashboard');
    Route::get('/modul', \App\Livewire\Siswa\ModulList::class)->name('modul.index');
    Route::get('/modul/{modulId}', \App\Livewire\Siswa\ModulShow::class)->name('modul.show');
    Route::get('/materi', \App\Livewire\Siswa\MateriList::class)->name('materi.index');
    Route::get('/materi/{materiId}', \App\Livewire\Siswa\MateriShow::class)->name('materi.show');
    Route::get('/tugas', \App\Livewire\Siswa\TugasList::class)->name('tugas.index');
    Route::get('/kuis', \App\Livewire\Siswa\KuisList::class)->name('kuis.index');
    Route::get('/kuis/{kuis}/kerjakan', \App\Livewire\Siswa\KerjakanKuis::class)->name('kuis.kerjakan');
    Route::get('/nilai', \App\Livewire\Siswa\NilaiList::class)->name('nilai.index');
    Route::get('/pengumuman', \App\Livewire\Siswa\PengumumanList::class)->name('pengumuman.index');
});