@php $role = auth()->user()?->role; @endphp

<aside id="sidebar"
       class="col-span-12 lg:col-span-3 lg:translate-x-0 -translate-x-full lg:static fixed top-14 left-0
              h-[calc(100vh-56px)] w-72 lg:w-auto z-30 transition-transform">
  <div class="h-full rounded-2xl bg-white/80 dark:bg-slate-900/70 backdrop-blur
              ring-1 ring-slate-200/70 dark:ring-white/10 p-4 shadow-glass">
    <nav class="space-y-1 text-sm">
      @if($role === 'admin')
        {{-- === ADMIN ONLY === --}}
        <x-nav.item :href="route('admin.dashboard', [], false)" icon="bar">
          Dashboard Admin
        </x-nav.item>

        {{-- Manajemen User → pakai route admin.users.index --}}
        <x-nav.item :href="route('admin.users.index', [], false)" icon="users">
          Manajemen User
        </x-nav.item>

        <x-nav.item :href="route('admin.reports', [], false)" icon="doc">
          Laporan
        </x-nav.item>

      @elseif($role === 'petugas')
        {{-- === PETUGAS ONLY === --}}
        <x-nav.item :href="route('petugas.dashboard', [], false)" icon="bar">
          Dashboard Petugas
        </x-nav.item>

        <div class="mt-3 mb-1 text-[11px] uppercase tracking-wide text-slate-500 dark:text-slate-400 px-2">
          Operasional
        </div>
        <x-nav.item :href="route('circulation.index', [], false)" icon="scan">
          Sirkulasi
        </x-nav.item>
        <x-nav.item :href="route('visits.index', [], false)" icon="calendar">
          Kunjungan
        </x-nav.item>
      @endif

      <div class="mt-4 pt-4 border-t border-slate-200/60 dark:border-white/10"></div>
    </nav>
  </div>
</aside>
