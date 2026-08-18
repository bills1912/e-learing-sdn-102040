<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — E-Learning SDN 102040</title>
    <link rel="icon" href="{{ url('logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased min-h-screen bg-canvas"
      x-data="{
          sidebarOpen: false,
          sidebarCollapsed: localStorage.getItem('elearning-sidebar-collapsed') === '1',
          toggleCollapse() {
              this.sidebarCollapsed = !this.sidebarCollapsed;
              localStorage.setItem('elearning-sidebar-collapsed', this.sidebarCollapsed ? '1' : '0');
          }
      }">

    <div class="flex min-h-screen">

        {{-- Mobile overlay --}}
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
             class="fixed inset-0 bg-gray-900/50 z-30 lg:hidden" x-transition.opacity></div>

        {{-- Sidebar --}}
        <aside
            class="fixed lg:sticky top-0 z-40 h-screen w-64 bg-white border-r border-gray-200 flex flex-col shrink-0
                   transition-[transform,width] duration-200 ease-out lg:translate-x-0"
            :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', sidebarCollapsed ? 'sidebar-collapsed' : '']"
        >
            {{-- Collapse toggle (desktop/tablet only) --}}
            <button @click="toggleCollapse()"
                    class="hidden lg:flex absolute -right-3 top-8 z-50 w-6 h-6 rounded-full bg-white text-gray-500 shadow-sm border border-gray-200 items-center justify-center hover:text-primary-600 hover:border-primary-200 transition-colors">
                <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <div class="flex items-center gap-3 px-5 py-5 sidebar-logo-row border-b border-gray-100">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center text-lg shrink-0">
                    <img src="{{ url('logo.png') }}" alt="logo-e-learning-sd">
                </div>
                <div class="leading-tight sidebar-brand-text">
                    <p class="font-display font-bold text-[15px] text-ink tracking-tight">SDN 102040</p>
                    <p class="text-[11px] text-ink-soft">Ujung Gading Julu</p>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto overflow-x-hidden scroll-thin px-3 py-4 space-y-1">
                @foreach(($navItems ?? []) as $item)
                    @php $active = request()->routeIs($item['active']); @endphp
                    <a href="{{ $item['url'] }}" wire:navigate
                       class="sidebar-nav-item group relative flex items-center gap-3 px-3 py-2 rounded-lg text-[14px] font-medium transition-colors duration-100
                              {{ $active ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <span class="w-5 h-5 shrink-0 flex items-center justify-center {{ $active ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-500' }}">
                            {!! $item['icon'] !!}
                        </span>
                        <span class="sidebar-label truncate">{{ $item['label'] }}</span>
                        @if($active)
                            <span class="sidebar-active-dot ml-auto w-1.5 h-1.5 rounded-full bg-primary-600"></span>
                        @endif

                        {{-- Tooltip shown only when collapsed --}}
                        <span class="sidebar-tooltip pointer-events-none absolute left-full top-1/2 -translate-y-1/2 ml-3 px-2.5 py-1.5 rounded-lg bg-gray-900 text-white text-[12px] font-medium whitespace-nowrap opacity-0 shadow-lg z-50">
                            {{ $item['label'] }}
                        </span>
                    </a>
                @endforeach
            </nav>

            <div class="px-3 pb-4 pt-3 border-t border-gray-100">
                <div class="flex items-center gap-3 px-2 py-2 sidebar-user-row">
                    <div class="w-8 h-8 rounded-full bg-primary-600 flex items-center justify-center font-display font-semibold text-[13px] text-white shrink-0">
                        {{ strtoupper(substr(auth()->user()->name ?? '?', 0, 1)) }}
                    </div>
                    <div class="leading-tight min-w-0 sidebar-brand-text">
                        <p class="text-[13px] font-medium text-ink truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[11px] text-ink-soft capitalize">{{ auth()->user()->role }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                    @csrf
                    <button type="submit" class="sidebar-nav-item group relative w-full flex items-center gap-2 px-3 py-2 rounded-lg text-[13.5px] font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        <svg class="w-4 h-4 shrink-0 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span class="sidebar-label">Keluar</span>
                        <span class="sidebar-tooltip pointer-events-none absolute left-full top-1/2 -translate-y-1/2 ml-3 px-2.5 py-1.5 rounded-lg bg-gray-900 text-white text-[12px] font-medium whitespace-nowrap opacity-0 shadow-lg z-50">Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main --}}
        <div class="flex-1 min-w-0 flex flex-col">

            {{-- Topbar --}}
            <header class="sticky top-0 z-20 bg-white border-b border-gray-200 px-4 sm:px-6 py-3.5 flex items-center gap-4">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 text-gray-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <div class="min-w-0">
                    <h1 class="font-display font-semibold text-[17px] sm:text-lg text-ink truncate">{{ $pageTitle ?? 'Dashboard' }}</h1>
                    @isset($pageSubtitle)
                        <p class="text-[12.5px] text-ink-soft truncate">{{ $pageSubtitle }}</p>
                    @endisset
                </div>

                <div class="ml-auto flex items-center gap-3">
                    <div x-data="{ now: '' }" x-init="
                        const fmt = () => now = new Date().toLocaleString('id-ID', { weekday: 'short', day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
                        fmt(); setInterval(fmt, 30000);
                    " class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-gray-50 border border-gray-200 text-gray-600 text-[12px] font-medium font-mono">
                        <span class="w-1.5 h-1.5 rounded-full bg-mint-500"></span>
                        <span x-text="now"></span>
                    </div>

                    @if((auth()->user()->role ?? null) === 'siswa')
                        @livewire('siswa.notification-bell')
                    @endif

                    <div class="relative">
                        <button type="button" data-dropdown-toggle="user-menu-dropdown" data-dropdown-placement="bottom-end"
                                class="w-9 h-9 rounded-full bg-primary-600 flex items-center justify-center text-white font-display font-semibold text-sm hover:bg-primary-700 transition-colors">
                            <span class="sr-only">Buka menu pengguna</span>
                            {{ strtoupper(substr(auth()->user()->name ?? '?', 0, 1)) }}
                        </button>
                        <div id="user-menu-dropdown" class="z-50 hidden my-2 w-56 text-base list-none surface-card !bg-white rounded-lg overflow-hidden">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-[13.5px] font-medium text-ink truncate">{{ auth()->user()->name }}</p>
                                <p class="text-[12px] text-ink-soft capitalize">{{ auth()->user()->role }}</p>
                            </div>
                            <div class="py-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-[13.5px] text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">
                                        {!! \App\Support\Icons::svg('logout', 'w-4 h-4') !!}
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Flash messages --}}
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
                     class="mx-4 sm:mx-6 mt-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm font-medium flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            <main class="flex-1 p-4 sm:p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>