<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\JawabanKuis;
use App\Models\Kelas;
use App\Models\Kuis;
use App\Models\MataPelajaran;
use App\Models\Materi;
use App\Models\MateriView;
use App\Models\Modul;
use App\Models\Nilai;
use App\Models\Pengumuman;
use App\Models\PengumpulanTugas;
use App\Models\Siswa;
use App\Models\SoalKuis;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->info('Membersihkan koleksi lama...');
        foreach ([User::class, Guru::class, Siswa::class, Kelas::class, MataPelajaran::class, Materi::class, Tugas::class, PengumpulanTugas::class, Kuis::class, SoalKuis::class, JawabanKuis::class, Nilai::class, Modul::class, MateriView::class, Pengumuman::class, \App\Models\PengumumanRead::class] as $model) {
            $model::truncate();
        }

        // ================= ADMIN =================
        User::create([
            'name' => 'Administrator Sekolah',
            'email' => 'admin@sdn102040.sch.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // ================= KELAS =================
        $kelasV = Kelas::create(['nama_kelas' => 'V', 'tingkat' => '5']);
        $kelasVI = Kelas::create(['nama_kelas' => 'VI', 'tingkat' => '6']);

        // ================= MATA PELAJARAN =================
        $mapelData = [
            ['nama_mapel' => 'Matematika', 'deskripsi' => 'Bilangan, pecahan, geometri, dan pengukuran dasar.', 'icon' => 'chart'],
            ['nama_mapel' => 'Bahasa Indonesia', 'deskripsi' => 'Membaca, menulis, dan tata bahasa Indonesia.', 'icon' => 'book'],
            ['nama_mapel' => 'IPA', 'deskripsi' => 'Ilmu Pengetahuan Alam — mengenal lingkungan dan makhluk hidup.', 'icon' => 'academic-cap'],
            ['nama_mapel' => 'IPS', 'deskripsi' => 'Ilmu Pengetahuan Sosial — sejarah, geografi, dan kemasyarakatan.', 'icon' => 'grid'],
            ['nama_mapel' => 'PPKn', 'deskripsi' => 'Pendidikan Pancasila dan Kewarganegaraan.', 'icon' => 'check-badge'],
            ['nama_mapel' => 'Bahasa Inggris', 'deskripsi' => 'Kosakata dan percakapan dasar bahasa Inggris.', 'icon' => 'quiz'],
        ];
        $mapel = collect($mapelData)->map(fn ($m) => MataPelajaran::create($m));
        [$matematika, $bindo, $ipa, $ips, $ppkn, $bing] = $mapel->all();

        // ================= GURU =================
        $userGuru1 = User::create([
            'name' => 'Rizki Mahdiani Siregar, S.T.',
            'email' => 'rizki.siregar@sdn102040.sch.id',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);
        $guru1 = Guru::create([
            'user_id' => (string) $userGuru1->_id,
            'nip' => '199203152019022001',
            'nama_guru' => 'Rizki Mahdiani Siregar, S.T.',
            'jenis_kelamin' => 'Perempuan',
            'alamat' => 'Desa Ujung Gading Julu, Kec. Simangambat, Kab. Padang Lawas Utara',
            'no_hp' => '081234567801',
        ]);

        $userGuru2 = User::create([
            'name' => 'Ahmad Syahputra Harahap, S.Pd.',
            'email' => 'ahmad.harahap@sdn102040.sch.id',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);
        $guru2 = Guru::create([
            'user_id' => (string) $userGuru2->_id,
            'nip' => '198811202015031002',
            'nama_guru' => 'Ahmad Syahputra Harahap, S.Pd.',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Simangambat, Padang Lawas Utara',
            'no_hp' => '081234567802',
        ]);

        // ================= SISWA (Kelas V) =================
        $namaSiswaV = [
            ['Ahmad Fauzi Nasution', 'L'], ['Siti Aisyah Harahap', 'P'], ['Muhammad Rizki Ritonga', 'L'],
            ['Putri Ayu Lestari', 'P'], ['Doli Simanjuntak', 'L'], ['Rani Br Harahap', 'P'],
            ['Fadli Ramadhan Pane', 'L'], ['Nurul Huda Siregar', 'P'], ['Aditya Pratama', 'L'],
            ['Salsabila Rangkuti', 'P'], ['Reza Pahlevi Hasibuan', 'L'], ['Yusuf Hakim Daulay', 'L'],
            ['Dewi Anggraini', 'P'], ['Ilham Maulana Batubara', 'L'], ['Farah Nabila Pulungan', 'P'],
            ['Rian Hidayat Siregar', 'L'], ['Khairunnisa Br Lubis', 'P'], ['Bayu Setiawan', 'L'],
        ];

        $siswaV = collect($namaSiswaV)->values()->map(function ($item, $i) use ($kelasV) {
            [$nama, $jk] = $item;
            $user = User::create([
                'name' => $nama,
                'email' => \Illuminate\Support\Str::slug($nama, '.').'@siswa.sdn102040.sch.id',
                'password' => Hash::make('password'),
                'role' => 'siswa',
            ]);

            return Siswa::create([
                'user_id' => (string) $user->_id,
                'kelas_id' => (string) $kelasV->_id,
                'nis' => '2425'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                'nama_siswa' => $nama,
                'jenis_kelamin' => $jk === 'L' ? 'Laki-laki' : 'Perempuan',
                'alamat' => 'Desa Ujung Gading Julu, Kec. Simangambat',
            ]);
        });

        // A handful of siswa in Kelas VI too, for variety in admin views
        $namaSiswaVI = [['Fajar Ramadhani', 'L'], ['Indah Permata Sari', 'P'], ['Wahyu Setiadi', 'L'], ['Zahra Amelia', 'P']];
        collect($namaSiswaVI)->values()->each(function ($item, $i) use ($kelasVI) {
            [$nama, $jk] = $item;
            $user = User::create([
                'name' => $nama,
                'email' => \Illuminate\Support\Str::slug($nama, '.').'@siswa.sdn102040.sch.id',
                'password' => Hash::make('password'),
                'role' => 'siswa',
            ]);
            Siswa::create([
                'user_id' => (string) $user->_id,
                'kelas_id' => (string) $kelasVI->_id,
                'nis' => '2425'.str_pad((string) ($i + 101), 3, '0', STR_PAD_LEFT),
                'nama_siswa' => $nama,
                'jenis_kelamin' => $jk === 'L' ? 'Laki-laki' : 'Perempuan',
                'alamat' => 'Simangambat, Padang Lawas Utara',
            ]);
        });

        // ================= MATERI =================
        // Note: Pecahan/Bangun Datar (Matematika) and Siklus Hidup Hewan (IPA) are
        // intentionally NOT seeded as standalone materi here — those topics are now
        // covered by Modul 1 (below), so students see them once, through the module.
        $materiData = [
            ['mapel' => $bindo, 'judul' => 'Menulis Teks Deskripsi', 'isi' => "Teks deskripsi adalah teks yang menggambarkan suatu objek, tempat, atau peristiwa secara detail sehingga pembaca seolah-olah dapat melihat, mendengar, atau merasakannya sendiri.\n\nCiri-ciri teks deskripsi:\n1. Menggambarkan objek secara rinci\n2. Menggunakan kata sifat\n3. Melibatkan panca indera\n\nContoh: 'Rumah adat Batak memiliki atap yang menjulang tinggi menyerupai punggung kerbau...'"],
            ['mapel' => $ips, 'judul' => 'Keragaman Budaya Sumatera Utara', 'isi' => "Sumatera Utara memiliki keragaman suku dan budaya, di antaranya suku Batak, Melayu, Nias, Mandailing, dan Pesisir.\n\nSetiap suku memiliki rumah adat, pakaian adat, tarian, dan bahasa daerahnya masing-masing. Kabupaten Padang Lawas Utara sendiri merupakan bagian dari wilayah budaya Mandailing dan Melayu.\n\nKeragaman ini menjadi kekayaan budaya yang perlu kita jaga dan lestarikan bersama."],
            ['mapel' => $ppkn, 'judul' => 'Makna Sila Pertama Pancasila', 'isi' => "Sila pertama Pancasila berbunyi 'Ketuhanan Yang Maha Esa'. Sila ini mengandung makna bahwa setiap warga negara Indonesia wajib meyakini dan menjalankan ajaran agama yang dianutnya.\n\nContoh pengamalan sila pertama:\n1. Menjalankan ibadah sesuai agama masing-masing\n2. Menghormati orang yang berbeda agama\n3. Tidak memaksakan agama kepada orang lain"],
        ];
        foreach ($materiData as $m) {
            Materi::create([
                'guru_id' => (string) $guru1->_id,
                'mapel_id' => (string) $m['mapel']->_id,
                'kelas_id' => (string) $kelasV->_id,
                'judul_materi' => $m['judul'],
                'isi_materi' => $m['isi'],
                'tanggal_upload' => now()->subDays(rand(1, 14)),
            ]);
        }

        // ================= TUGAS =================
        $tugas1 = Tugas::create([
            'guru_id' => (string) $guru1->_id, 'mapel_id' => (string) $matematika->_id, 'kelas_id' => (string) $kelasV->_id,
            'judul_tugas' => 'Latihan Soal Pecahan', 'deskripsi' => 'Kerjakan 10 soal pecahan sederhana pada buku tugas, foto hasil pekerjaan lalu jelaskan jawabannya di kolom pengumpulan.',
            'batas_waktu' => now()->addDays(3),
        ]);
        $tugas2 = Tugas::create([
            'guru_id' => (string) $guru1->_id, 'mapel_id' => (string) $bindo->_id, 'kelas_id' => (string) $kelasV->_id,
            'judul_tugas' => 'Menulis Teks Deskripsi Rumah Adat', 'deskripsi' => 'Tulis sebuah teks deskripsi (minimal 3 paragraf) tentang rumah adat di daerahmu.',
            'batas_waktu' => now()->subDays(1),
        ]);
        $tugas3 = Tugas::create([
            'guru_id' => (string) $guru1->_id, 'mapel_id' => (string) $ipa->_id, 'kelas_id' => (string) $kelasV->_id,
            'judul_tugas' => 'Mengamati Siklus Hidup Kupu-kupu', 'deskripsi' => 'Buat gambar dan penjelasan singkat tentang tahapan metamorfosis kupu-kupu.',
            'batas_waktu' => now()->addDays(6),
        ]);

        // Pengumpulan tugas - sebagian siswa sudah mengumpulkan, sebagian sudah dinilai
        foreach ($siswaV->take(12) as $i => $s) {
            $sudahDinilai = $i < 8;
            PengumpulanTugas::create([
                'tugas_id' => (string) $tugas2->_id,
                'siswa_id' => (string) $s->_id,
                'keterangan' => 'Rumah adat Batak Toba memiliki bentuk atap melengkung menyerupai tanduk kerbau. Rumah ini terbuat dari kayu dan ditopang oleh tiang-tiang besar tanpa menggunakan paku...',
                'tanggal_kumpul' => now()->subHours(rand(2, 40)),
                'nilai' => $sudahDinilai ? rand(70, 98) : null,
                'feedback' => $sudahDinilai ? collect(['Bagus sekali, tulisanmu sangat detail!', 'Perhatikan lagi penggunaan huruf kapital ya.', 'Kerja bagus, terus semangat belajar!'])->random() : null,
                'status' => $sudahDinilai ? 'dinilai' : 'menunggu',
            ]);
        }
        foreach ($siswaV->take(5) as $s) {
            PengumpulanTugas::create([
                'tugas_id' => (string) $tugas1->_id,
                'siswa_id' => (string) $s->_id,
                'keterangan' => 'Jawaban terlampir: 1) 1/4  2) 3/8  3) 2/5  4) 5/6  5) 1/2 ...',
                'tanggal_kumpul' => now()->subHours(rand(1, 20)),
                'status' => 'menunggu',
            ]);
        }

        // ================= KUIS =================
        $kuisBerlangsung = Kuis::create([
            'guru_id' => (string) $guru1->_id, 'mapel_id' => (string) $matematika->_id, 'kelas_id' => (string) $kelasV->_id,
            'judul_kuis' => 'Kuis Pecahan & Bangun Datar', 'deskripsi' => 'Kuis singkat untuk menguji pemahaman tentang pecahan dan bangun datar.',
            'waktu_mulai' => now()->subDays(1), 'waktu_selesai' => now()->addDays(4), 'durasi_menit' => 15,
        ]);
        $kuisTerjadwal = Kuis::create([
            'guru_id' => (string) $guru1->_id, 'mapel_id' => (string) $ipa->_id, 'kelas_id' => (string) $kelasV->_id,
            'judul_kuis' => 'Kuis Siklus Hidup Hewan', 'deskripsi' => 'Kuis tentang metamorfosis dan daur hidup hewan.',
            'waktu_mulai' => now()->addDays(2), 'waktu_selesai' => now()->addDays(9), 'durasi_menit' => 20,
        ]);
        $kuisSelesai = Kuis::create([
            'guru_id' => (string) $guru1->_id, 'mapel_id' => (string) $bindo->_id, 'kelas_id' => (string) $kelasV->_id,
            'judul_kuis' => 'Kuis Teks Deskripsi', 'deskripsi' => 'Kuis pemahaman ciri-ciri teks deskripsi.',
            'waktu_mulai' => now()->subDays(10), 'waktu_selesai' => now()->subDays(3), 'durasi_menit' => 15,
        ]);

        $soalPecahan = [
            ['Berapakah hasil dari 1/2 + 1/4?', '3/4', '2/6', '1/6', '2/4', 'A'],
            ['Pecahan 3/6 jika disederhanakan menjadi...', '1/3', '1/2', '2/3', '3/2', 'B'],
            ['Bangun datar yang memiliki 3 sisi disebut...', 'Persegi', 'Lingkaran', 'Segitiga', 'Trapesium', 'C'],
            ['Rumus luas persegi dengan sisi 5 cm adalah...', '10 cm2', '20 cm2', '15 cm2', '25 cm2', 'D'],
            ['Bangun datar yang semua sisinya sama panjang dan bersudut 90 derajat adalah...', 'Persegi panjang', 'Persegi', 'Segitiga', 'Layang-layang', 'B'],
        ];
        foreach ($soalPecahan as $q) {
            SoalKuis::create([
                'kuis_id' => (string) $kuisBerlangsung->_id, 'pertanyaan' => $q[0],
                'pilihan_a' => $q[1], 'pilihan_b' => $q[2], 'pilihan_c' => $q[3], 'pilihan_d' => $q[4],
                'jawaban_benar' => $q[5],
            ]);
        }

        $soalIpa = [
            ['Tahapan metamorfosis sempurna kupu-kupu diawali dari...', 'Pupa', 'Larva', 'Telur', 'Dewasa', 'C'],
            ['Hewan yang mengalami metamorfosis tidak sempurna adalah...', 'Kupu-kupu', 'Nyamuk', 'Belalang', 'Katak', 'C'],
            ['Ulat akan berubah menjadi kepompong yang disebut juga...', 'Nimfa', 'Pupa', 'Larva', 'Imago', 'B'],
            ['Hewan berikut yang TIDAK mengalami metamorfosis adalah...', 'Kucing', 'Katak', 'Kupu-kupu', 'Nyamuk', 'A'],
            ['Tahap akhir metamorfosis sempurna disebut fase...', 'Larva', 'Pupa', 'Telur', 'Dewasa/Imago', 'D'],
        ];
        foreach ($soalIpa as $q) {
            SoalKuis::create([
                'kuis_id' => (string) $kuisTerjadwal->_id, 'pertanyaan' => $q[0],
                'pilihan_a' => $q[1], 'pilihan_b' => $q[2], 'pilihan_c' => $q[3], 'pilihan_d' => $q[4],
                'jawaban_benar' => $q[5],
            ]);
        }

        $soalBindo = [
            ['Teks yang menggambarkan objek secara rinci disebut teks...', 'Narasi', 'Deskripsi', 'Eksposisi', 'Argumentasi', 'B'],
            ['Ciri utama teks deskripsi adalah...', 'Berisi langkah-langkah', 'Melibatkan panca indera', 'Berisi pendapat', 'Berisi dialog', 'B'],
            ['Kalimat "Rumah itu berwarna biru muda dengan atap merah" termasuk kalimat...', 'Deskripsi', 'Ajakan', 'Perintah', 'Tanya', 'A'],
        ];
        foreach ($soalBindo as $q) {
            SoalKuis::create([
                'kuis_id' => (string) $kuisSelesai->_id, 'pertanyaan' => $q[0],
                'pilihan_a' => $q[1], 'pilihan_b' => $q[2], 'pilihan_c' => $q[3], 'pilihan_d' => $q[4],
                'jawaban_benar' => $q[5],
            ]);
        }

        // ================= JAWABAN KUIS (sebagian siswa sudah mengerjakan kuis yang sedang berlangsung) =================
        $soalKuisBerlangsung = SoalKuis::where('kuis_id', (string) $kuisBerlangsung->_id)->get();
        foreach ($siswaV->take(9) as $i => $s) {
            $jumlahDijawab = $i < 6 ? $soalKuisBerlangsung->count() : rand(1, 3);
            foreach ($soalKuisBerlangsung->take($jumlahDijawab) as $soal) {
                $opsi = ['A', 'B', 'C', 'D'];
                $jawab = rand(0, 100) < 75 ? $soal->jawaban_benar : $opsi[array_rand($opsi)];
                JawabanKuis::create([
                    'kuis_id' => (string) $kuisBerlangsung->_id,
                    'soal_id' => (string) $soal->_id,
                    'siswa_id' => (string) $s->_id,
                    'jawaban' => $jawab,
                    'benar' => $jawab === $soal->jawaban_benar,
                ]);
            }
        }

        // Kuis selesai - semua siswa sudah mengerjakan
        $soalKuisSelesai = SoalKuis::where('kuis_id', (string) $kuisSelesai->_id)->get();
        foreach ($siswaV as $s) {
            foreach ($soalKuisSelesai as $soal) {
                $opsi = ['A', 'B', 'C', 'D'];
                $jawab = rand(0, 100) < 80 ? $soal->jawaban_benar : $opsi[array_rand($opsi)];
                JawabanKuis::create([
                    'kuis_id' => (string) $kuisSelesai->_id,
                    'soal_id' => (string) $soal->_id,
                    'siswa_id' => (string) $s->_id,
                    'jawaban' => $jawab,
                    'benar' => $jawab === $soal->jawaban_benar,
                ]);
            }
        }

        // ================= NILAI (rekap dari tugas & kuis yang sudah dinilai/selesai) =================
        foreach (PengumpulanTugas::where('status', 'dinilai')->get() as $p) {
            $tugas = Tugas::find($p->tugas_id);
            Nilai::create([
                'siswa_id' => (string) $p->siswa_id, 'mapel_id' => (string) $tugas->mapel_id,
                'tugas_id' => (string) $p->tugas_id, 'jenis' => 'tugas',
                'nilai' => $p->nilai, 'keterangan' => $tugas->judul_tugas,
            ]);
        }
        foreach ($siswaV as $s) {
            $jawabanSiswa = JawabanKuis::where('kuis_id', (string) $kuisSelesai->_id)->where('siswa_id', (string) $s->_id)->get();
            if ($jawabanSiswa->isNotEmpty()) {
                $benar = $jawabanSiswa->where('benar', true)->count();
                $total = $jawabanSiswa->count();
                Nilai::create([
                    'siswa_id' => (string) $s->_id, 'mapel_id' => (string) $bindo->_id,
                    'kuis_id' => (string) $kuisSelesai->_id, 'jenis' => 'kuis',
                    'nilai' => round($benar / $total * 100), 'keterangan' => $kuisSelesai->judul_kuis,
                ]);
            }
        }

        // ================= MODUL PEMBELAJARAN (Pre-Test -> Materi -> Post-Test) =================
        // Modul 1: Matematika - siswa sudah mulai mengerjakan (variasi progress untuk demo)
        $modul1Pretest = Kuis::create([
            'guru_id' => (string) $guru1->_id, 'mapel_id' => (string) $matematika->_id, 'kelas_id' => (string) $kelasV->_id,
            'judul_kuis' => 'Pre-Test: Modul Pecahan & Bangun Datar', 'deskripsi' => 'Kerjakan pre-test ini sebelum membaca materi.',
            'waktu_mulai' => now()->subDays(5), 'waktu_selesai' => now()->addYear(), 'durasi_menit' => 10,
            'peran' => 'pretest',
        ]);
        $modul1Materi = Materi::create([
            'guru_id' => (string) $guru1->_id, 'mapel_id' => (string) $matematika->_id, 'kelas_id' => (string) $kelasV->_id,
            'judul_materi' => 'Modul Pecahan & Bangun Datar',
            'isi_materi' => "Selamat datang di Modul Pecahan & Bangun Datar!\n\nDalam modul ini kamu akan belajar tentang:\n1. Pengertian pecahan dan bagian-bagiannya\n2. Cara menyederhanakan pecahan\n3. Jenis-jenis bangun datar dan sifatnya\n4. Menghitung luas bangun datar sederhana\n\nBaca materi ini dengan saksama, karena setelah ini kamu akan mengerjakan Post-Test untuk menguji pemahamanmu.",
            'tanggal_upload' => now()->subDays(5),
        ]);
        $modul1Posttest = Kuis::create([
            'guru_id' => (string) $guru1->_id, 'mapel_id' => (string) $matematika->_id, 'kelas_id' => (string) $kelasV->_id,
            'judul_kuis' => 'Post-Test: Modul Pecahan & Bangun Datar', 'deskripsi' => 'Kerjakan post-test ini setelah membaca materi.',
            'waktu_mulai' => now()->subDays(5), 'waktu_selesai' => now()->addYear(), 'durasi_menit' => 10,
            'peran' => 'posttest',
        ]);
        $modul1 = Modul::create([
            'guru_id' => (string) $guru1->_id, 'mapel_id' => (string) $matematika->_id, 'kelas_id' => (string) $kelasV->_id,
            'judul_modul' => 'Modul 1: Pecahan & Bangun Datar', 'deskripsi' => 'Pelajari pecahan sederhana dan bangun datar langkah demi langkah.',
            'urutan' => 1,
            'pretest_kuis_id' => (string) $modul1Pretest->_id, 'materi_id' => (string) $modul1Materi->_id, 'posttest_kuis_id' => (string) $modul1Posttest->_id,
        ]);
        $modul1Pretest->update(['modul_id' => (string) $modul1->_id]);
        $modul1Materi->update(['modul_id' => (string) $modul1->_id]);
        $modul1Posttest->update(['modul_id' => (string) $modul1->_id]);

        $modul1SoalPretest = [
            ['Pecahan digunakan untuk menyatakan...', 'Bilangan bulat', 'Bagian dari keseluruhan', 'Bilangan negatif', 'Bilangan genap', 'B'],
            ['Bangun datar yang memiliki 4 sisi sama panjang disebut...', 'Segitiga', 'Lingkaran', 'Persegi', 'Trapesium', 'C'],
            ['Angka di bagian bawah pecahan disebut...', 'Pembilang', 'Penyebut', 'Bilangan bulat', 'Desimal', 'B'],
        ];
        foreach ($modul1SoalPretest as $q) {
            SoalKuis::create(['kuis_id' => (string) $modul1Pretest->_id, 'pertanyaan' => $q[0], 'pilihan_a' => $q[1], 'pilihan_b' => $q[2], 'pilihan_c' => $q[3], 'pilihan_d' => $q[4], 'jawaban_benar' => $q[5]]);
        }
        $modul1SoalPosttest = [
            ['Hasil dari 2/4 disederhanakan menjadi...', '1/4', '1/2', '2/2', '1/3', 'B'],
            ['Keliling persegi dengan sisi 6 cm adalah...', '12 cm', '18 cm', '24 cm', '36 cm', 'C'],
            ['Pecahan 3/4 lebih besar dari...', '1/2', '4/5', '7/8', '1', 'A'],
        ];
        foreach ($modul1SoalPosttest as $q) {
            SoalKuis::create(['kuis_id' => (string) $modul1Posttest->_id, 'pertanyaan' => $q[0], 'pilihan_a' => $q[1], 'pilihan_b' => $q[2], 'pilihan_c' => $q[3], 'pilihan_d' => $q[4], 'jawaban_benar' => $q[5]]);
        }

        // Vary student progress through Modul 1 for a realistic demo:
        // - first 4 students: fully completed (pretest + materi + posttest)
        // - next 3 students: completed pretest + read materi, haven't done posttest
        // - next 3 students: completed pretest only
        // - remaining students: haven't started
        $soalPretestM1 = SoalKuis::where('kuis_id', (string) $modul1Pretest->_id)->get();
        $soalPosttestM1 = SoalKuis::where('kuis_id', (string) $modul1Posttest->_id)->get();

        foreach ($siswaV->slice(0, 10) as $i => $s) {
            $stage = $i < 4 ? 'full' : ($i < 7 ? 'materi' : 'pretest');

            foreach ($soalPretestM1 as $soal) {
                $jawab = rand(0, 100) < 80 ? $soal->jawaban_benar : ['A', 'B', 'C', 'D'][array_rand(['A', 'B', 'C', 'D'])];
                JawabanKuis::create(['kuis_id' => (string) $modul1Pretest->_id, 'soal_id' => (string) $soal->_id, 'siswa_id' => (string) $s->_id, 'jawaban' => $jawab, 'benar' => $jawab === $soal->jawaban_benar]);
            }
            $benarPretest = JawabanKuis::where('kuis_id', (string) $modul1Pretest->_id)->where('siswa_id', (string) $s->_id)->where('benar', true)->count();
            Nilai::create(['siswa_id' => (string) $s->_id, 'mapel_id' => (string) $matematika->_id, 'kuis_id' => (string) $modul1Pretest->_id, 'jenis' => 'kuis', 'nilai' => round($benarPretest / max(1, $soalPretestM1->count()) * 100), 'keterangan' => $modul1Pretest->judul_kuis]);

            if ($stage === 'materi' || $stage === 'full') {
                MateriView::create(['siswa_id' => (string) $s->_id, 'materi_id' => (string) $modul1Materi->_id, 'viewed_at' => now()->subDays(rand(1, 3))]);
            }

            if ($stage === 'full') {
                foreach ($soalPosttestM1 as $soal) {
                    $jawab = rand(0, 100) < 85 ? $soal->jawaban_benar : ['A', 'B', 'C', 'D'][array_rand(['A', 'B', 'C', 'D'])];
                    JawabanKuis::create(['kuis_id' => (string) $modul1Posttest->_id, 'soal_id' => (string) $soal->_id, 'siswa_id' => (string) $s->_id, 'jawaban' => $jawab, 'benar' => $jawab === $soal->jawaban_benar]);
                }
                $benarPosttest = JawabanKuis::where('kuis_id', (string) $modul1Posttest->_id)->where('siswa_id', (string) $s->_id)->where('benar', true)->count();
                Nilai::create(['siswa_id' => (string) $s->_id, 'mapel_id' => (string) $matematika->_id, 'kuis_id' => (string) $modul1Posttest->_id, 'jenis' => 'kuis', 'nilai' => round($benarPosttest / max(1, $soalPosttestM1->count()) * 100), 'keterangan' => $modul1Posttest->judul_kuis]);
            }
        }

        // Modul 2: IPA - masih fresh, belum ada siswa yang mengerjakan (demo status "Belum mulai")
        $modul2Pretest = Kuis::create([
            'guru_id' => (string) $guru1->_id, 'mapel_id' => (string) $ipa->_id, 'kelas_id' => (string) $kelasV->_id,
            'judul_kuis' => 'Pre-Test: Modul Siklus Hidup Hewan', 'deskripsi' => 'Kerjakan pre-test ini sebelum membaca materi.',
            'waktu_mulai' => now(), 'waktu_selesai' => now()->addYear(), 'durasi_menit' => 10,
            'peran' => 'pretest',
        ]);
        $modul2Materi = Materi::create([
            'guru_id' => (string) $guru1->_id, 'mapel_id' => (string) $ipa->_id, 'kelas_id' => (string) $kelasV->_id,
            'judul_materi' => 'Modul Siklus Hidup Hewan',
            'isi_materi' => "Selamat datang di Modul Siklus Hidup Hewan!\n\nDalam modul ini kamu akan belajar tentang:\n1. Metamorfosis sempurna dan tidak sempurna\n2. Tahapan daur hidup kupu-kupu\n3. Tahapan daur hidup belalang\n4. Perbedaan hewan yang bermetamorfosis dan tidak\n\nSetelah membaca materi ini, kerjakan Post-Test untuk menguji pemahamanmu.",
            'tanggal_upload' => now(),
        ]);
        $modul2Posttest = Kuis::create([
            'guru_id' => (string) $guru1->_id, 'mapel_id' => (string) $ipa->_id, 'kelas_id' => (string) $kelasV->_id,
            'judul_kuis' => 'Post-Test: Modul Siklus Hidup Hewan', 'deskripsi' => 'Kerjakan post-test ini setelah membaca materi.',
            'waktu_mulai' => now(), 'waktu_selesai' => now()->addYear(), 'durasi_menit' => 10,
            'peran' => 'posttest',
        ]);
        $modul2 = Modul::create([
            'guru_id' => (string) $guru1->_id, 'mapel_id' => (string) $ipa->_id, 'kelas_id' => (string) $kelasV->_id,
            'judul_modul' => 'Modul 1: Siklus Hidup Hewan', 'deskripsi' => 'Pelajari metamorfosis dan daur hidup berbagai hewan.',
            'urutan' => 1,
            'pretest_kuis_id' => (string) $modul2Pretest->_id, 'materi_id' => (string) $modul2Materi->_id, 'posttest_kuis_id' => (string) $modul2Posttest->_id,
        ]);
        $modul2Pretest->update(['modul_id' => (string) $modul2->_id]);
        $modul2Materi->update(['modul_id' => (string) $modul2->_id]);
        $modul2Posttest->update(['modul_id' => (string) $modul2->_id]);

        foreach ($soalIpa as $q) {
            SoalKuis::create(['kuis_id' => (string) $modul2Pretest->_id, 'pertanyaan' => $q[0], 'pilihan_a' => $q[1], 'pilihan_b' => $q[2], 'pilihan_c' => $q[3], 'pilihan_d' => $q[4], 'jawaban_benar' => $q[5]]);
        }
        $modul2SoalPosttest = [
            ['Proses perubahan bentuk pada hewan disebut...', 'Fotosintesis', 'Metamorfosis', 'Respirasi', 'Adaptasi', 'B'],
            ['Kupu-kupu termasuk hewan dengan metamorfosis...', 'Tidak sempurna', 'Sempurna', 'Tanpa metamorfosis', 'Setengah sempurna', 'B'],
            ['Contoh hewan yang tidak mengalami metamorfosis adalah...', 'Katak', 'Nyamuk', 'Ayam', 'Kupu-kupu', 'C'],
        ];
        foreach ($modul2SoalPosttest as $q) {
            SoalKuis::create(['kuis_id' => (string) $modul2Posttest->_id, 'pertanyaan' => $q[0], 'pilihan_a' => $q[1], 'pilihan_b' => $q[2], 'pilihan_c' => $q[3], 'pilihan_d' => $q[4], 'jawaban_benar' => $q[5]]);
        }

        // ================= PENGUMUMAN =================
        Pengumuman::create([
            'guru_id' => (string) $guru1->_id,
            'judul' => 'Selamat Datang di E-Learning SDN 102040!',
            'isi' => "Halo anak-anak! Selamat datang di platform belajar online kita. Silakan cek menu Modul untuk mulai belajar Pre-Test, Materi, dan Post-Test setiap mata pelajaran. Semangat belajar ya! 🎉",
            'created_at' => now()->subDays(4),
        ]);
        Pengumuman::create([
            'guru_id' => (string) $guru1->_id,
            'judul' => 'Batas Pengumpulan Tugas Bahasa Indonesia',
            'isi' => "Diingatkan kembali untuk mengumpulkan tugas menulis teks deskripsi paling lambat besok. Yang belum mengumpulkan segera dikerjakan ya, terima kasih.",
            'created_at' => now()->subDays(1),
        ]);
        Pengumuman::create([
            'guru_id' => (string) $guru2->_id,
            'judul' => 'Jadwal Ujian Tengah Semester',
            'isi' => "Ujian Tengah Semester akan dilaksanakan minggu depan. Pastikan sudah menyelesaikan semua Pre-Test dan Post-Test di menu Modul sebagai bahan persiapan. Belajar yang rajin ya!",
            'created_at' => now()->subHours(3),
        ]);

        $this->command?->info('Seeder selesai: '.User::count().' user, '.Siswa::count().' siswa, '.Guru::count().' guru dibuat.');
    }
}