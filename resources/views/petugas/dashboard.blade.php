@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-8">

    {{-- =============== HERO SECTION =============== --}}
    <section class="relative overflow-hidden rounded-3xl min-h-[240px] md:min-h-[280px] shadow-xl ring-1 ring-slate-900/5">
        <img src="/assets/bg-dalamsekoalh.jpeg"
             class="absolute inset-0 w-full h-full object-cover object-center brightness-75 dark:brightness-50"
             alt="Sekolah">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-900/90 via-slate-900/60 to-transparent"></div>

        <div class="relative h-full p-6 sm:p-10 flex flex-col justify-center z-10">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6">
                <div>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight text-white drop-shadow-md">
                        Dashboard Petugas
                    </h1>
                    <p class="mt-2 text-slate-300 font-medium text-sm md:text-base max-w-lg">
                        Halo, <span class="text-white font-bold">{{ auth()->user()->name }}</span>. Siap melayani sirkulasi hari ini?
                    </p>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full
                                    bg-emerald-500/10 border border-emerald-400/40 text-emerald-100
                                    text-[11px] font-semibold tracking-wide uppercase shadow-sm">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                            </span>
                            Layanan Aktif
                        </div>
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full
                                    bg-white/10 border border-white/30 text-[11px] font-medium text-slate-100
                                    backdrop-blur">
                            <svg class="w-3.5 h-3.5 text-slate-200" viewBox="0 0 24 24" fill="none">
                                <path d="M8 7V3m8 4V3M5 11h14M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- =============== KPI CARDS =============== --}}
    @php($today = now()->toDateString())
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Card 1: Pinjam Hari Ini --}}
        <a href="{{ route('circulation.history', ['type'=>'loans','from'=>$today,'to'=>$today], false) }}"
           class="group relative overflow-hidden bg-white dark:bg-slate-800 rounded-2xl p-6
                  shadow-sm hover:shadow-xl border border-slate-200 dark:border-slate-700
                  transition-all duration-300 hover:-translate-y-1">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 rounded-full
                        bg-indigo-50 dark:bg-indigo-900/20 transition-transform group-hover:scale-110"></div>
            <div class="relative space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-[0.12em]">
                        Pinjam Hari Ini
                    </h3>
                    <div class="p-2 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="text-3xl font-bold text-slate-800 dark:text-white">
                        {{ number_format($loansToday ?? 0) }}
                    </span>
                    <span class="text-[11px] text-slate-400 dark:text-slate-500">
                        Transaksi hari ini
                    </span>
                </div>
            </div>
        </a>

        {{-- Card 2: Kembali Hari Ini --}}
        <a href="{{ route('circulation.history', ['type'=>'returns','from'=>$today,'to'=>$today], false) }}"
           class="group relative overflow-hidden bg-white dark:bg-slate-800 rounded-2xl p-6
                  shadow-sm hover:shadow-xl border border-slate-200 dark:border-slate-700
                  transition-all duration-300 hover:-translate-y-1">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 rounded-full
                        bg-emerald-50 dark:bg-emerald-900/20 transition-transform group-hover:scale-110"></div>
            <div class="relative space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-[0.12em]">
                        Kembali Hari Ini
                    </h3>
                    <div class="p-2 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="text-3xl font-bold text-slate-800 dark:text-white">
                        {{ number_format($returnsToday ?? 0) }}
                    </span>
                    <span class="text-[11px] text-slate-400 dark:text-slate-500">
                        Dikembalikan hari ini
                    </span>
                </div>
            </div>
        </a>

        {{-- Card 3: Kunjungan Hari Ini --}}
        <a href="{{ route('visits.history', ['from'=>$today,'to'=>$today], false) }}"
           class="group relative overflow-hidden bg-white dark:bg-slate-800 rounded-2xl p-6
                  shadow-sm hover:shadow-xl border border-slate-200 dark:border-slate-700
                  transition-all duration-300 hover:-translate-y-1">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 rounded-full
                        bg-blue-50 dark:bg-blue-900/20 transition-transform group-hover:scale-110"></div>
            <div class="relative space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-[0.12em]">
                        Kunjungan Hari Ini
                    </h3>
                    <div class="p-2 rounded-lg bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="text-3xl font-bold text-slate-800 dark:text-white">
                        {{ number_format($visitsToday ?? 0) }}
                    </span>
                    <span class="text-[11px] text-slate-400 dark:text-slate-500">
                        Pengunjung perpustakaan
                    </span>
                </div>
            </div>
        </a>

        {{-- Card 4: Terlambat --}}
        <a href="{{ route('circulation.history', ['type'=>'overdue','to'=>$today], false) }}"
           class="group relative overflow-hidden bg-white dark:bg-slate-800 rounded-2xl p-6
                  shadow-sm hover:shadow-xl border border-rose-200 dark:border-rose-900/50
                  transition-all duration-300 hover:-translate-y-1">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 rounded-full
                        bg-rose-50 dark:bg-rose-900/20 transition-transform group-hover:scale-110"></div>
            <div class="relative space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-semibold text-rose-500 dark:text-rose-400 uppercase tracking-[0.12em]">
                        Terlambat
                    </h3>
                    <div class="p-2 rounded-lg bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="text-3xl font-bold text-slate-800 dark:text-white">
                        {{ number_format($overdue ?? 0) }}
                    </span>
                    <span class="text-[11px] text-slate-400 dark:text-slate-500">
                        Peminjam terlambat
                    </span>
                </div>
            </div>
        </a>
    </section>

    {{-- =============== OPERASIONAL SECTION =============== --}}
    <section class="space-y-6">
        {{-- AKSI CEPAT --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="font-bold text-slate-800 dark:text-white text-lg">Aksi Cepat</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Akses layanan dengan satu klik</p>
                </div>
                <div class="px-3 py-1 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-sm font-medium">
                    4 Menu
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('circulation.index', [], false) }}" 
                   class="group relative flex flex-col items-center justify-center p-4 rounded-xl bg-gradient-to-b from-white to-slate-50 dark:from-slate-800 dark:to-slate-900 border border-indigo-100 dark:border-indigo-800/50 hover:border-indigo-300 dark:hover:border-indigo-600 shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="relative mb-3">
                        <div class="p-3 rounded-full bg-gradient-to-r from-indigo-500 to-blue-500 text-white shadow-md">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </div>
                    </div>
                    <span class="font-semibold text-slate-800 dark:text-white text-sm">Scan Pinjam</span>
                    <span class="text-xs text-slate-500 dark:text-slate-400 mt-1">Transaksi baru</span>
                </a>

                <a href="{{ route('circulation.index', [], false) }}" 
                   class="group relative flex flex-col items-center justify-center p-4 rounded-xl bg-gradient-to-b from-white to-slate-50 dark:from-slate-800 dark:to-slate-900 border border-amber-100 dark:border-amber-800/50 hover:border-amber-300 dark:hover:border-amber-600 shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="relative mb-3">
                        <div class="p-3 rounded-full bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-md">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
                        </div>
                    </div>
                    <span class="font-semibold text-slate-800 dark:text-white text-sm">Scan Kembali</span>
                    <span class="text-xs text-slate-500 dark:text-slate-400 mt-1">Pengembalian</span>
                </a>

                <a href="{{ route('visits.index', [], false) }}" 
                   class="group relative flex flex-col items-center justify-center p-4 rounded-xl bg-gradient-to-b from-white to-slate-50 dark:from-slate-800 dark:to-slate-900 border border-teal-100 dark:border-teal-800/50 hover:border-teal-300 dark:hover:border-teal-600 shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="relative mb-3">
                        <div class="p-3 rounded-full bg-gradient-to-r from-teal-500 to-emerald-500 text-white shadow-md">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        </div>
                    </div>
                    <span class="font-semibold text-slate-800 dark:text-white text-sm">Catat Kunjungan</span>
                    <span class="text-xs text-slate-500 dark:text-slate-400 mt-1">Pengunjung</span>
                </a>

                <a href="{{ route('circulation.history', ['type'=>'all','from'=>$today,'to'=>$today], false) }}"
                   class="group relative flex flex-col items-center justify-center p-4 rounded-xl bg-gradient-to-b from-white to-slate-50 dark:from-slate-800 dark:to-slate-900 border border-slate-100 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="relative mb-3">
                        <div class="p-3 rounded-full bg-gradient-to-r from-slate-500 to-gray-500 text-white shadow-md">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>
                    <span class="font-semibold text-slate-800 dark:text-white text-sm">Cari / Riwayat</span>
                    <span class="text-xs text-slate-500 dark:text-slate-400 mt-1">Pencarian</span>
                </a>
            </div>
        </div>

        {{-- INFO CEPAT --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <h3 class="font-bold text-slate-800 dark:text-white text-lg mb-6">Info Cepat</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-center justify-between p-4 rounded-xl bg-gradient-to-r from-amber-50 to-yellow-50 dark:from-amber-900/20 dark:to-yellow-900/20 border border-amber-100 dark:border-amber-800/50">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-amber-100 dark:bg-amber-900/50">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-amber-800 dark:text-amber-300">Jatuh Tempo</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Hari ini</p>
                        </div>
                    </div>
                    <span class="text-2xl font-bold text-slate-900 dark:text-white">{{ $dueToday ?? '0' }}</span>
                </div>

                <div class="flex items-center justify-between p-4 rounded-xl bg-gradient-to-r from-rose-50 to-pink-50 dark:from-rose-900/20 dark:to-pink-900/20 border border-rose-100 dark:border-rose-800/50">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-rose-100 dark:bg-rose-900/50">
                            <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-rose-800 dark:text-rose-300">Overdue Aktif</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Perlu tindakan</p>
                        </div>
                    </div>
                    <span class="text-2xl font-bold text-slate-900 dark:text-white">{{ $overdue ?? '0' }}</span>
                </div>

                <div class="flex items-center justify-between p-4 rounded-xl bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 border border-emerald-100 dark:border-emerald-800/50">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-emerald-100 dark:bg-emerald-900/50">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-emerald-800 dark:text-emerald-300">Di Ruangan</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Pengunjung aktif</p>
                        </div>
                    </div>
                    <span class="text-2xl font-bold text-slate-900 dark:text-white">{{ $inRoom ?? '0' }}</span>
                </div>
            </div>
        </div>

        {{-- MULAI PROSES --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <a href="{{ route('circulation.index', [], false) }}" class="group flex items-center justify-between w-full px-6 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-md shadow-indigo-500/30 transition-colors duration-200">
                    <div class="flex items-center gap-4">
                        <div class="p-2 rounded-lg bg-white/20 backdrop-blur-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="font-bold block">Sirkulasi Buku</span>
                            <span class="text-sm text-white/80">Pinjam & Kembali</span>
                        </div>
                    </div>
                    <svg class="w-5 h-5 transform group-hover:translate-x-2 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </a>

                <a href="{{ route('visits.index', [], false) }}" class="group flex items-center justify-between w-full px-6 py-4 bg-teal-600 hover:bg-teal-700 text-white rounded-xl shadow-md shadow-teal-500/30 transition-colors duration-200">
                    <div class="flex items-center gap-4">
                        <div class="p-2 rounded-lg bg-white/20 backdrop-blur-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="font-bold block">Kunjungan</span>
                            <span class="text-sm text-white/80">Catat pengunjung</span>
                        </div>
                    </div>
                    <svg class="w-5 h-5 transform group-hover:translate-x-2 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </a>
            </div>
            
            <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-700">
                <div class="flex items-start gap-3">
                    <div class="p-2 rounded-lg bg-blue-100 dark:bg-blue-900/50">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-800 dark:text-white">Tips Petugas</p>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Pastikan data diinput dengan benar sebelum menyimpan transaksi.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection