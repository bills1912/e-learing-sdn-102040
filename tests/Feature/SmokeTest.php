<?php

namespace Tests\Feature;

use App\Models\Kuis;
use App\Models\Tugas;
use App\Models\User;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    public function test_guest_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect('/login');
        $this->get('/login')->assertOk();
    }

    public function test_admin_can_login_and_view_all_pages(): void
    {
        $admin = User::where('email', 'admin@sdn102040.sch.id')->first();
        $this->assertNotNull($admin, 'Seeder must have run against local Mongo test db');

        $this->actingAs($admin);
        $this->get(route('admin.dashboard'))->assertOk()->assertSee('Dashboard Admin');
        $this->get(route('admin.guru.index'))->assertOk()->assertSee('Rizki Mahdiani');
        $this->get(route('admin.siswa.index'))->assertOk();
        $this->get(route('admin.kelas.index'))->assertOk();
        $this->get(route('admin.mapel.index'))->assertOk()->assertSee('Matematika');
    }

    public function test_guru_can_login_and_view_all_pages(): void
    {
        $guru = User::where('email', 'rizki.siregar@sdn102040.sch.id')->first();
        $this->assertNotNull($guru);

        $this->actingAs($guru);
        $this->get(route('guru.dashboard'))->assertOk();
        $this->get(route('guru.materi.index'))->assertOk();
        $this->get(route('guru.tugas.index'))->assertOk();
        $this->get(route('guru.kuis.index'))->assertOk();
        $this->get(route('guru.nilai.index'))->assertOk();

        $tugas = Tugas::first();
        $this->get(route('guru.tugas.pengumpulan', $tugas->_id))->assertOk();

        $kuis = Kuis::first();
        $this->get(route('guru.kuis.soal', $kuis->_id))->assertOk();
        $this->get(route('guru.kuis.hasil', $kuis->_id))->assertOk();
    }

    public function test_siswa_can_login_and_view_all_pages(): void
    {
        $siswa = User::where('role', 'siswa')->first();
        $this->assertNotNull($siswa);

        $this->actingAs($siswa);
        $this->get(route('siswa.dashboard'))->assertOk();
        $this->get(route('siswa.materi.index'))->assertOk();
        $this->get(route('siswa.tugas.index'))->assertOk();
        $this->get(route('siswa.kuis.index'))->assertOk();
        $this->get(route('siswa.nilai.index'))->assertOk();

        $kuisBerlangsung = Kuis::where('waktu_mulai', '<=', now())->where('waktu_selesai', '>=', now())->first();
        if ($kuisBerlangsung) {
            $this->get(route('siswa.kuis.kerjakan', $kuisBerlangsung->_id))->assertOk();
        }
    }

    public function test_role_middleware_blocks_cross_role_access(): void
    {
        $siswa = User::where('role', 'siswa')->first();
        $this->actingAs($siswa);
        $this->get(route('admin.dashboard'))->assertForbidden();
    }
}
