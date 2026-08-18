<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function updatedEmail(): void
    {
        $this->email = trim($this->email);
    }

    public function authenticate(): void
    {
        $this->email = trim($this->email);

        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [], [
            'email' => 'email',
            'password' => 'kata sandi',
        ]);

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau kata sandi yang Anda masukkan salah.',
            ]);
        }

        request()->session()->regenerate();

        $user = Auth::user();

        $this->redirect(match ($user->role) {
            'admin' => route('admin.dashboard'),
            'guru' => route('guru.dashboard'),
            'siswa' => route('siswa.dashboard'),
            default => '/',
        }, navigate: true);
    }

    public function fillDemo(string $role): void
    {
        $map = [
            'admin' => 'admin@sdn102040.sch.id',
            'guru' => 'rizki.siregar@sdn102040.sch.id',
            'siswa' => 'ahmad.fauzi.nasution@siswa.sdn102040.sch.id',
        ];
        $this->email = $map[$role] ?? '';
        $this->password = 'password';
    }

    public function render()
    {
        return view('livewire.auth.login', ['title' => 'Masuk']);
    }
}