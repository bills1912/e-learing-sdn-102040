<?php

namespace App\Livewire\Siswa;

use App\Models\Pengumuman;
use App\Models\PengumumanRead;
use App\Models\Siswa;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class NotificationBell extends Component
{
    public bool $open = false;

    protected function siswa(): ?Siswa
    {
        return Siswa::where('user_id', (string) auth()->id())->first();
    }

    #[Computed]
    public function pengumuman()
    {
        return Pengumuman::with('guru')->orderByDesc('created_at')->limit(8)->get();
    }

    #[Computed]
    public function readIds(): array
    {
        $siswaId = (string) $this->siswa()?->_id;

        return PengumumanRead::where('siswa_id', $siswaId)->pluck('pengumuman_id')->map(fn ($id) => (string) $id)->all();
    }

    #[Computed]
    public function unreadCount(): int
    {
        return $this->pengumuman->filter(fn ($p) => ! in_array((string) $p->_id, $this->readIds))->count();
    }

    public function toggle(): void
    {
        $this->open = ! $this->open;

        if ($this->open) {
            $this->markAllRead();
        }
    }

    public function close(): void
    {
        $this->open = false;
    }

    #[On('pengumuman-read')]
    public function markAllRead(): void
    {
        $siswa = $this->siswa();
        if (! $siswa) {
            return;
        }

        $siswaId = (string) $siswa->_id;

        foreach ($this->pengumuman as $p) {
            PengumumanRead::updateOrCreate(
                ['siswa_id' => $siswaId, 'pengumuman_id' => (string) $p->_id],
                ['read_at' => now()]
            );
        }

        unset($this->readIds, $this->unreadCount);
    }

    public function render()
    {
        return view('livewire.siswa.notification-bell');
    }
}
