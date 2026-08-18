<?php

namespace App\Livewire\Admin;

use App\Livewire\BaseComponent;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class GuruManager extends BaseComponent
{
    public bool $showModal = false;
    public ?string $editId = null;
    public ?string $deleteId = null;

    public string $nama_guru = '';

    public string $nip = '';

    public string $email = '';

    public string $jenis_kelamin = 'Laki-laki';

    public string $alamat = '';

    public string $no_hp = '';

    public function create(): void
    {
        $this->reset('nama_guru', 'nip', 'email', 'alamat', 'no_hp', 'editId');
        $this->jenis_kelamin = 'Laki-laki';
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $g = Guru::with('user')->findOrFail($id);
        $this->editId = $id;
        $this->nama_guru = $g->nama_guru;
        $this->nip = $g->nip;
        $this->email = $g->user->email ?? '';
        $this->jenis_kelamin = $g->jenis_kelamin;
        $this->alamat = $g->alamat ?? '';
        $this->no_hp = $g->no_hp ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate($this->rulesFor());

        $currentUserId = $this->editId ? Guru::find($this->editId)?->user_id : null;
        $emailTaken = User::where('email', $this->email)
            ->when($currentUserId, fn ($q) => $q->where('_id', '!=', $currentUserId))
            ->exists();

        if ($emailTaken) {
            $this->addError('email', 'Email ini sudah digunakan oleh akun lain.');
            return;
        }

        if ($this->editId) {
            $guru = Guru::findOrFail($this->editId);
            $guru->update([
                'nama_guru' => $this->nama_guru,
                'nip' => $this->nip,
                'jenis_kelamin' => $this->jenis_kelamin,
                'alamat' => $this->alamat,
                'no_hp' => $this->no_hp,
            ]);
            User::where('_id', $guru->user_id)->update([
                'name' => $this->nama_guru,
                'email' => $this->email,
            ]);
            session()->flash('success', 'Data guru berhasil diperbarui.');
        } else {
            $user = User::create([
                'name' => $this->nama_guru,
                'email' => $this->email,
                'password' => Hash::make('password'),
                'role' => 'guru',
            ]);
            Guru::create([
                'user_id' => (string) $user->_id,
                'nama_guru' => $this->nama_guru,
                'nip' => $this->nip,
                'jenis_kelamin' => $this->jenis_kelamin,
                'alamat' => $this->alamat,
                'no_hp' => $this->no_hp,
            ]);
            session()->flash('success', 'Guru baru ditambahkan. Kata sandi default: password');
        }

        $this->showModal = false;
        $this->reset('nama_guru', 'nip', 'email', 'alamat', 'no_hp', 'editId');
    }

    protected function rulesFor(): array
    {
        return [
            'nama_guru' => 'required|string|max:100',
            'nip' => 'required|string|max:30',
            'email' => 'required|email|max:150',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'alamat' => 'nullable|string|max:255',
            'no_hp' => 'nullable|string|max:20',
        ];
    }

    public function confirmDelete(string $id): void
    {
        $this->deleteId = $id;
    }

    public function delete(): void
    {
        $guru = Guru::find($this->deleteId);
        if ($guru) {
            User::where('_id', $guru->user_id)->delete();
            $guru->delete();
        }
        $this->deleteId = null;
        session()->flash('success', 'Data guru berhasil dihapus.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function render()
    {
        return $this->view('livewire.admin.guru-manager', [
            'items' => Guru::with('user')->orderBy('nama_guru')->get(),
        ], 'Data Guru', 'Kelola data guru dan akun pengajar');
    }
}
