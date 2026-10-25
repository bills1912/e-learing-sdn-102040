<?php

namespace App\Livewire\Admin;

use App\Livewire\BaseComponent;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\WithPagination;

class SiswaManager extends BaseComponent
{
    use WithPagination;

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