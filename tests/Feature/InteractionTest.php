<?php

namespace Tests\Feature;

use App\Livewire\Admin\KelasManager;
use App\Livewire\Guru\PengumpulanManager;
use App\Livewire\Siswa\KerjakanKuis;
use App\Livewire\Siswa\TugasList;
use App\Models\Kelas;
use App\Models\Kuis;
use App\Models\PengumpulanTugas;
use App\Models\Siswa;
use App\Models\SoalKuis;
use App\Models\Tugas;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class InteractionTest extends TestCase
{
    public function test_admin_can_create_and_delete_kelas(): void
    {
        $admin = User::where('email', 'admin@sdn102040.sch.id')->first();
        $this->actingAs($admin);

        Livewire::test(KelasManager::class)
            ->call('create')
            ->set('nama_kelas', 'IV')
            ->set('tingkat', '4')
            ->call('save')
            ->assertHasNoErrors();

        $kelas = Kelas::where('nama_kelas', 'IV')->first();
        $this->assertNotNull($kelas, 'Kelas IV should have been created');

        Livewire::test(KelasManager::class)
            ->call('confirmDelete', (string) $kelas->_id)
            ->call('delete');

        $this->assertNull(Kelas::find($kelas->_id));
    }

    public function test_siswa_can_submit_tugas(): void
    {
        $siswa = Siswa::first();
        $user = User::find($siswa->user_id);
        $tugas = Tugas::where('kelas_id', (string) $siswa->kelas_id)->first();

        PengumpulanTugas::where('tugas_id', (string) $tugas->_id)->where('siswa_id', (string) $siswa->_id)->delete();

        $this->actingAs($user);

        Livewire::test(TugasList::class)
            ->call('openSubmit', (string) $tugas->_id)
            ->set('keterangan', 'Ini jawaban tugas percobaan otomatis.')
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('pengumpulan_tugas', [
            'tugas_id' => (string) $tugas->_id,
            'siswa_id' => (string) $siswa->_id,
        ], 'mongodb');
    }

    public function test_guru_can_grade_submission(): void
    {
        $guruUser = User::where('email', 'rizki.siregar@sdn102040.sch.id')->first();
        $this->actingAs($guruUser);

        $pengumpulan = PengumpulanTugas::where('status', 'menunggu')->first();
        $this->assertNotNull($pengumpulan);

        Livewire::test(PengumpulanManager::class, ['tugas' => (string) $pengumpulan->tugas_id])
            ->call('openGrade', (string) $pengumpulan->_id)
            ->set('nilaiInput', '88')
            ->set('feedbackInput', 'Kerja bagus!')
            ->call('saveGrade')
            ->assertHasNoErrors();

        $pengumpulan->refresh();
        $this->assertEquals(88, (float) $pengumpulan->nilai);
        $this->assertEquals('dinilai', $pengumpulan->status);
    }

    public function test_siswa_can_answer_and_finish_kuis(): void
    {
        $kuis = Kuis::where('waktu_mulai', '<=', now())->where('waktu_selesai', '>=', now())->first();
        $siswa = Siswa::where('kelas_id', (string) $kuis->kelas_id)->skip(15)->first();
        $user = User::find($siswa->user_id);

        $this->actingAs($user);

        $component = Livewire::test(KerjakanKuis::class, ['kuis' => (string) $kuis->_id]);

        foreach (SoalKuis::where('kuis_id', (string) $kuis->_id)->get() as $soal) {
            $component->call('pilih', (string) $soal->_id, $soal->jawaban_benar);
        }

        $component->call('finish')->assertSet('finished', true);

        $this->assertDatabaseHas('nilai', [
            'siswa_id' => (string) $siswa->_id,
            'kuis_id' => (string) $kuis->_id,
            'nilai' => 100,
        ], 'mongodb');
    }
}
