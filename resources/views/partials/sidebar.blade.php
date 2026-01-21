@php
    $user = auth()->user();
    $role = $user?->role;
@endphp

<aside id="sidebar"
       class="col-span-12 lg:col-span-3
              fixed top-14 left-0 lg:static
              h-[calc(100vh-56px)] lg:h-auto
              w-72 lg:w-auto
              -translate-x-full lg:translate-x-0
              z-30 transition-transform">

    <div class="h-full lg:h-auto rounded-2xl bg-white/80 dark:bg-slate-900/70
                backdrop-blur ring-1 ring-slate-200/70 dark:ring-white/10
                p-4 shadow-glass flex flex-col">

        {{-- BRAND / HEADER KECIL --}}
        <div class="mb-4 flex items-center gap-2 px-1">
            <img src="/assets/logo-yapi.png"
                 class="h-8 w-8 rounded-full ring-1 ring-slate-200 dark:ring-white/10 bg-white"
                 alt="YAPI">
            <div class="flex flex-col leading-tight">
                <span class="font-semibold text-slate-900 dark:text-white text-sm">LibMS</span>
                <span class="text-[11px] text-slate-500 dark:text-slate-400">
                    {{ strtoupper($role ?? '-') }}
                </span>
            </div>
        </div>

        <nav class="space-y-4 text-sm flex-1 overflow-y-auto">

            {{-- ========== PETUGAS (atau ADMIN lama) → MENU LENGKAP ========== --}}
            @if($role === 'petugas' || $role === 'admin')

                {{-- DASHBOARD --}}
                <div class="px-1">
                    <x-nav.item :href="route('admin.dashboard', [], false)" icon="home">
                        Dashboard
                    </x-nav.item>
                </div>

                {{-- OPERASIONAL --}}
                <div>
                    <div class="px-1 mt-4 mb-1 text-[11px] uppercase tracking-wide
                                text-slate-500 dark:text-slate-400">
                        Operasional
                    </div>
                    <div class="space-y-1 px-1">
                        <x-nav.item :href="route('circulation.index', [], false)" icon="scan">
                            Sirkulasi
                        </x-nav.item>
                        <x-nav.item :href="route('visits.index', [], false)" icon="calendar">
                            Kunjungan
                        </x-nav.item>
                    </div>
                </div>

                {{-- MASTER DATA --}}
                <div>
                    <div class="px-1 mt-4 mb-1 text-[11px] uppercase tracking-wide
                                text-slate-500 dark:text-slate-400">
                        Master Data
                    </div>
                    <div class="space-y-1 px-1">
                        <x-nav.item :href="route('admin.books.index', [], false)" icon="book">
                            Buku
                        </x-nav.item>
                        <x-nav.item :href="route('admin.students.index', [], false)" icon="users">
                            Siswa
                        </x-nav.item>
                        <x-nav.item :href="route('admin.users.index', [], false)" icon="user-settings">
                            Pengguna
                        </x-nav.item>
                    </div>
                </div>

                {{-- LAPORAN & BARCODE --}}
                <div class="mb-2">
                    <div class="px-1 mt-4 mb-1 text-[11px] uppercase tracking-wide
                                text-slate-500 dark:text-slate-400">
                        Laporan & Barcode
                    </div>
                    <div class="space-y-1 px-1">
                        <x-nav.item :href="route('admin.reports', [], false)" icon="report">
                            Laporan
                        </x-nav.item>
                        <x-nav.item :href="route('admin.barcodes.books', [], false)" icon="barcode">
                            Barcode Buku
                        </x-nav.item>
                        <x-nav.item :href="route('admin.barcodes.students', [], false)" icon="idcard">
                            Barcode Siswa
                        </x-nav.item>
                    </div>
                </div>

            {{-- ========== PENGUNJUNG (KIOSK / SISWA) ========== --}}
            @elseif($role === 'pengunjung')

                <div class="px-1 space-y-1 mt-1">
                    <x-nav.item :href="route('pengunjung.dashboard', [], false)" icon="scan">
                        Scan Kunjungan
                    </x-nav.item>

                    <x-nav.item :href="route('pengunjung.circulation.index', [], false)" icon="book">
                        Sirkulasi Mandiri
                    </x-nav.item>
                </div>

                <div class="mt-6 px-1 pt-3 border-t border-slate-200/60 dark:border-white/10">
                    <x-nav.item :href="route('pengunjung.logout.show', [], false)" icon="lock">
                        Logout Kiosk (Guru)
                    </x-nav.item>
                </div>

            @else
                {{-- fallback --}}
                <div class="px-1 mt-3 text-xs text-slate-500 dark:text-slate-400">
                    Role tidak dikenali. Hubungi administrator.
                </div>
            @endif
        </nav>

        {{-- FOOTER KECIL --}}
        <div class="mt-3 pt-2 border-t border-slate-200/60 dark:border-white/10
                    text-[11px] text-slate-400 dark:text-slate-500">
            LibMS · v1.0
        </div>
    </div>
</aside>
