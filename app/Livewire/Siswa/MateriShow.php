<?php

namespace App\Livewire\Siswa;

use App\Models\MateriView;
use App\Models\Materi;
use App\Models\Modul;
use App\Models\Siswa;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.reader')]
class MateriShow extends Component
{
    public Materi $materi;

    public function mount(string $materiId): void
    {
        $item = Materi::with('mapel', 'guru', 'kelas')->findOrFail($materiId);

        $siswa = Siswa::where('user_id', (string) auth()->id())->first();

        // A student may only open materi that belongs to their own kelas.
        if (! $siswa || (string) $item->kelas_id !== (string) $siswa->kelas_id) {
            abort(403, 'Materi ini bukan untuk kelas Anda.');
        }

        // If this materi is part of a module, the pre-test must be completed first.
        // This is enforced here (server-side), not just hidden in the UI.
        $modul = Modul::where('materi_id', $materiId)->first();
        if ($modul) {
            $progress = $modul->progressFor((string) $siswa->_id);
            if ($progress['materi_terkunci']) {
                abort(403, 'Selesaikan Pre-Test terlebih dahulu sebelum membuka materi ini.');
            }
        }

        // Record that this student has viewed the materi (unlocks post-test, if any).
        MateriView::updateOrCreate(
            ['siswa_id' => (string) $siswa->_id, 'materi_id' => $materiId],
            ['viewed_at' => now()]
        );

        $this->materi = $item;
    }

    public function render()
    {
        return view('livewire.siswa.materi-show', [
            'title' => $this->materi->judul_materi,
        ]);
    }
}