<?php

namespace App\Livewire\Admin;

use App\Livewire\BaseComponent;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class SiswaManager extends BaseComponent
{
    use WithPagination;
    use WithFileUploads;

    public bool $showModal = false;
    public ?string $editId = null;
    public ?string $deleteId = null;

    public string $nama_siswa = '';
    public string $nis = '';
    public string $email = '';
    public string $jenis_kelamin = 'Laki-laki';
    public string $kelas_id = '';
    public string $alamat = '';

    public string $filterKelas = '';
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterKelas(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset('search', 'filterKelas');
        $this->resetPage();
    }

    /** Base query shared by the table and the export (respects search + kelas filter). */
    protected function filteredQuery()
    {
        $query = Siswa::with(['user', 'kelas'])->orderBy('nama_siswa');

        if ($this->filterKelas) {
            $query->where('kelas_id', $this->filterKelas);
        }

        $term = trim($this->search);
        if ($term !== '') {
            $userIds = User::where('role', 'siswa')
                ->where('email', 'like', '%' . $term . '%')
                ->pluck('_id')
                ->map(fn ($id) => (string) $id)
                ->all();

            $query->where(function ($q) use ($term, $userIds) {
                $q->where('nama_siswa', 'like', '%' . $term . '%')
                  ->orWhere('nis', 'like', '%' . $term . '%');
                if ($userIds) {
                    $q->orWhereIn('user_id', $userIds);
                }
            });
        }

        return $query;
    }

    /* ===================== IMPORT ===================== */

    /** Column order of the import template — keep in sync with the siswa collection. */
    public const IMPORT_COLUMNS = ['nama_siswa', 'nis', 'email', 'jenis_kelamin', 'kelas', 'alamat'];
    public const IMPORT_MAX_ROWS = 1000;

    public bool $showImportModal = false;
    public $importFile = null;
    public ?array $importResult = null;

    public function openImport(): void
    {
        $this->reset('importFile', 'importResult');
        $this->resetErrorBag();
        $this->showImportModal = true;
    }

    public function closeImport(): void
    {
        $this->showImportModal = false;
        $this->reset('importFile', 'importResult');
    }

    /** Download a CSV template (semicolon-separated, opens directly in Excel). */
    public function downloadTemplate()
    {
        $kelas = Kelas::orderBy('nama_kelas')->value('nama_kelas') ?? 'I';

        return response()->streamDownload(function () use ($kelas) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, self::IMPORT_COLUMNS, ';');
            fputcsv($out, ['Contoh Nama Siswa', '2026001', 'contoh.siswa@siswa.sdn102040.sch.id', 'Laki-laki', $kelas, 'Ujung Gading Julu'], ';');
            fputcsv($out, ['Contoh Siswi', '2026002', 'contoh.siswi@siswa.sdn102040.sch.id', 'Perempuan', $kelas, ''], ';');
            fclose($out);
        }, 'template-import-siswa.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function import(): void
    {
        $this->validate([
            'importFile' => 'required|file|mimes:csv,txt|max:2048',
        ], [
            'importFile.required' => 'Pilih file CSV terlebih dahulu.',
            'importFile.mimes' => 'Format file harus .csv (simpan dari Excel sebagai "CSV").',
            'importFile.max' => 'Ukuran file maksimal 2 MB.',
        ]);

        $rows = $this->readCsv($this->importFile->getRealPath());
        if ($rows === null) {
            $this->addError('importFile', 'Header tidak dikenali. Gunakan template: ' . implode(', ', self::IMPORT_COLUMNS) . '.');
            return;
        }
        if (count($rows) > self::IMPORT_MAX_ROWS) {
            $this->addError('importFile', 'Maksimal ' . self::IMPORT_MAX_ROWS . ' baris per sekali import.');
            return;
        }

        // Lookup tables: kelas by name, existing emails & NIS.
        $kelasMap = Kelas::all()->mapWithKeys(fn ($k) => [mb_strtolower(trim($k->nama_kelas)) => (string) $k->_id]);
        $existingEmails = User::pluck('email')->map(fn ($e) => mb_strtolower($e))->flip();
        $existingNis = Siswa::pluck('nis')->map(fn ($n) => (string) $n)->flip();

        $created = 0;
        $errors = [];

        foreach ($rows as $lineNo => $r) {
            $nama = trim($r['nama_siswa'] ?? '');
            $nis = trim($r['nis'] ?? '');
            $email = mb_strtolower(trim($r['email'] ?? ''));
            $jk = $this->normalizeGender($r['jenis_kelamin'] ?? '');
            $kelasKey = mb_strtolower(trim(preg_replace('/^kelas\s+/i', '', trim($r['kelas'] ?? ''))));
            $alamat = trim($r['alamat'] ?? '');

            if ($nama === '' && $nis === '' && $email === '') {
                continue; // empty line
            }

            $problems = [];
            if ($nama === '') $problems[] = 'nama kosong';
            if ($nis === '') $problems[] = 'NIS kosong';
            elseif (isset($existingNis[$nis])) $problems[] = "NIS {$nis} sudah terdaftar";
            if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) $problems[] = 'email tidak valid';
            elseif (isset($existingEmails[$email])) $problems[] = "email {$email} sudah dipakai";
            if (! $jk) $problems[] = 'jenis kelamin harus Laki-laki/Perempuan (atau L/P)';
            if (! isset($kelasMap[$kelasKey])) $problems[] = 'kelas "' . ($r['kelas'] ?? '') . '" tidak ditemukan';

            if ($problems) {
                $errors[] = ['baris' => $lineNo, 'nama' => $nama ?: '-', 'pesan' => implode('; ', $problems)];
                continue;
            }

            $user = User::create([
                'name' => $nama,
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => 'siswa',
            ]);
            Siswa::create([
                'user_id' => (string) $user->_id,
                'nama_siswa' => $nama,
                'nis' => $nis,
                'jenis_kelamin' => $jk,
                'kelas_id' => $kelasMap[$kelasKey],
                'alamat' => $alamat,
            ]);

            // Prevent duplicates within the same file.
            $existingEmails[$email] = true;
            $existingNis[$nis] = true;
            $created++;
        }

        $this->importResult = ['created' => $created, 'errors' => $errors];
        $this->importFile = null;
        $this->resetPage();

        if ($created > 0) {
            session()->flash('success', "{$created} siswa berhasil diimport. Kata sandi default: password");
        }
    }

    /**
     * Parse CSV into [lineNumber => [column => value]].
     * Auto-detects ; or , delimiter, strips BOM, and also accepts the file produced by Export.
     */
    protected function readCsv(string $path): ?array
    {
        $fh = fopen($path, 'r');
        if (! $fh) return null;

        $first = fgets($fh);
        if ($first === false) { fclose($fh); return []; }
        $first = preg_replace('/^\xEF\xBB\xBF/', '', $first);
        $delimiter = substr_count($first, ';') >= substr_count($first, ',') ? ';' : ',';

        $aliases = ['nama' => 'nama_siswa', 'nama_lengkap' => 'nama_siswa', 'jk' => 'jenis_kelamin', 'l/p' => 'jenis_kelamin', 'nama_kelas' => 'kelas'];
        $header = array_map(function ($h) use ($aliases) {
            $h = str_replace(' ', '_', mb_strtolower(trim($h)));
            return $aliases[$h] ?? $h;
        }, str_getcsv(trim($first), $delimiter));

        foreach (['nama_siswa', 'nis', 'email', 'jenis_kelamin', 'kelas'] as $required) {
            if (! in_array($required, $header, true)) { fclose($fh); return null; }
        }

        $rows = [];
        $line = 1;
        while (($cols = fgetcsv($fh, 0, $delimiter)) !== false) {
            $line++;
            if ($cols === [null]) continue;
            $row = [];
            foreach ($header as $i => $key) {
                $val = (string) ($cols[$i] ?? '');
                // Undo the ="0123" wrapper used by Export to keep leading zeros.
                if (preg_match('/^="(.*)"$/', $val, $m)) $val = $m[1];
                $row[$key] = $val;
            }
            $rows[$line] = $row;
        }
        fclose($fh);

        return $rows;
    }

    protected function normalizeGender(string $v): ?string
    {
        $v = mb_strtolower(trim($v));
        return match (true) {
            in_array($v, ['l', 'lk', 'laki-laki', 'laki laki', 'laki'], true) => 'Laki-laki',
            in_array($v, ['p', 'pr', 'perempuan', 'wanita'], true) => 'Perempuan',
            default => null,
        };
    }

    /** Export the currently filtered students as a CSV that opens cleanly in Excel. */
    public function export()
    {
        $rows = $this->filteredQuery()->get();
        $filename = 'data-siswa-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel reads accents correctly
            $put = fn (array $r) => fputcsv($out, $r, ';');

            $put(['No', 'Nama Siswa', 'NIS', 'Jenis Kelamin', 'Kelas', 'Email', 'Alamat']);
            foreach ($rows as $i => $s) {
                $nis = (string) $s->nis;
                $put([
                    $i + 1,
                    $s->nama_siswa,
                    str_starts_with($nis, '0') ? '="' . $nis . '"' : $nis, // keep leading zeros in Excel
                    $s->jenis_kelamin,
                    $s->kelas->nama_kelas ?? '-',
                    $s->user->email ?? '-',
                    $s->alamat ?? '',
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function create(): void
    {
        $this->reset('nama_siswa', 'nis', 'email', 'alamat', 'editId');
        $this->jenis_kelamin = 'Laki-laki';
        $this->kelas_id = Kelas::first()?->_id ?? '';
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $s = Siswa::with('user')->findOrFail($id);
        $this->editId = $id;
        $this->nama_siswa = $s->nama_siswa;
        $this->nis = $s->nis;
        $this->email = $s->user->email ?? '';
        $this->jenis_kelamin = $s->jenis_kelamin;
        $this->kelas_id = (string) $s->kelas_id;
        $this->alamat = $s->alamat ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'nama_siswa' => 'required|string|max:100',
            'nis' => 'required|string|max:30',
            'email' => 'required|email|max:150',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'kelas_id' => 'required|string',
            'alamat' => 'nullable|string|max:255',
        ]);

        $currentUserId = $this->editId ? Siswa::find($this->editId)?->user_id : null;
        $emailTaken = User::where('email', $this->email)
            ->when($currentUserId, fn ($q) => $q->where('_id', '!=', $currentUserId))
            ->exists();

        if ($emailTaken) {
            $this->addError('email', 'Email ini sudah digunakan oleh akun lain.');
            return;
        }

        if ($this->editId) {
            $siswa = Siswa::findOrFail($this->editId);
            $siswa->update([
                'nama_siswa' => $this->nama_siswa,
                'nis' => $this->nis,
                'jenis_kelamin' => $this->jenis_kelamin,
                'kelas_id' => $this->kelas_id,
                'alamat' => $this->alamat,
            ]);
            User::where('_id', $siswa->user_id)->update([
                'name' => $this->nama_siswa,
                'email' => $this->email,
            ]);
            session()->flash('success', 'Data siswa berhasil diperbarui.');
        } else {
            $user = User::create([
                'name' => $this->nama_siswa,
                'email' => $this->email,
                'password' => Hash::make('password'),
                'role' => 'siswa',
            ]);
            Siswa::create([
                'user_id' => (string) $user->_id,
                'nama_siswa' => $this->nama_siswa,
                'nis' => $this->nis,
                'jenis_kelamin' => $this->jenis_kelamin,
                'kelas_id' => $this->kelas_id,
                'alamat' => $this->alamat,
            ]);
            session()->flash('success', 'Siswa baru ditambahkan. Kata sandi default: password');
        }

        $this->showModal = false;
        $this->reset('nama_siswa', 'nis', 'email', 'alamat', 'editId');
    }

    public function confirmDelete(string $id): void
    {
        $this->deleteId = $id;
    }

    public function delete(): void
    {
        $siswa = Siswa::find($this->deleteId);
        if ($siswa) {
            User::where('_id', $siswa->user_id)->delete();
            $siswa->delete();
        }
        $this->deleteId = null;
        session()->flash('success', 'Data siswa berhasil dihapus.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function render()
    {
        return $this->view('livewire.admin.siswa-manager', [
            'items' => $this->filteredQuery()->paginate(10),
            'kelasList' => Kelas::orderBy('nama_kelas')->get(),
        ], 'Data Siswa', 'Kelola data siswa dan akun pembelajaran');
    }
}