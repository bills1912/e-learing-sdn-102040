<div class="w-full max-w-md">
    <div class="flex items-center justify-center gap-3 mb-8">
        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-xl"><img src="{{ url('logo.png') }}" alt="logo-e-learning-sd"></div>
        <div class="text-center">
            <p class="font-display font-bold text-ink text-lg leading-tight">E-Learning SDN 102040</p>
            <p class="text-[12px] text-ink-soft">Ujung Gading Julu, Simangambat</p>
        </div>
    </div>

    <div class="surface-card rounded-lg p-6 sm:p-8 space-y-6 animate-pop-in">
        <h2 class="text-xl font-bold text-ink">Masuk ke akun Anda</h2>

        <form wire:submit="authenticate" class="space-y-5">
            <div>
                <label class="block mb-1.5 text-sm font-medium text-ink">Email</label>
                <input type="email" wire:model="email" autofocus autocomplete="username"
                       class="bg-gray-50 border border-gray-300 text-ink text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 outline-none transition-colors"
                       placeholder="nama@sdn102040.sch.id">
                @error('email') <p class="text-danger text-[12.5px] mt-1.5 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block mb-1.5 text-sm font-medium text-ink">Kata Sandi</label>
                <input type="password" wire:model="password" autocomplete="current-password"
                       class="bg-gray-50 border border-gray-300 text-ink text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 outline-none transition-colors"
                       placeholder="••••••••">
                @error('password') <p class="text-danger text-[12.5px] mt-1.5 font-medium">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center">
                <input type="checkbox" wire:model="remember" id="remember"
                       class="w-4 h-4 border-gray-300 rounded-sm bg-gray-50 text-primary-600 focus:ring-primary-300">
                <label for="remember" class="ml-2 text-sm font-medium text-ink">Ingat saya</label>
            </div>

            <button type="submit"
                    wire:loading.attr="disabled" wire:target="authenticate"
                    class="btn-primary w-full !py-2.5 disabled:opacity-70">
                <svg wire:loading wire:target="authenticate" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                <span wire:loading.remove wire:target="authenticate">Masuk</span>
                <span wire:loading wire:target="authenticate">Memproses…</span>
            </button>
        </form>

        <div class="pt-5 border-t border-gray-100">
            <p class="text-[12px] font-medium text-ink-soft mb-3">Coba akun demo</p>
            <div class="grid grid-cols-3 gap-2">
                <button type="button" wire:click="fillDemo('admin')" class="px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 text-gray-700 text-[12.5px] font-medium hover:bg-gray-100 transition-colors">Admin</button>
                <button type="button" wire:click="fillDemo('guru')" class="px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 text-gray-700 text-[12.5px] font-medium hover:bg-gray-100 transition-colors">Guru</button>
                <button type="button" wire:click="fillDemo('siswa')" class="px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 text-gray-700 text-[12.5px] font-medium hover:bg-gray-100 transition-colors">Siswa</button>
            </div>
        </div>
    </div>
</div>