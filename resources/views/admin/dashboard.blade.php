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
                        Dashboard Admin
                    </h1>
                    <p class="mt-2 text-slate-300 font-medium text-sm md:text-base max-w-lg">
                        Selamat datang kembali. Berikut adalah ringkasan aktivitas perpustakaan.
                    </p>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full
                                    bg-emerald-500/10 border border-emerald-400/40 text-emerald-100
                                    text-[11px] font-semibold tracking-wide uppercase shadow-sm">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                            </span>
                            System Online
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
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Card 1: Buku --}}
        <a href="{{ route('admin.books.index', [], false) }}"
           class="group relative overflow-hidden bg-white dark:bg-slate-800 rounded-2xl p-6
                  shadow-sm hover:shadow-xl border border-slate-200 dark:border-slate-700
                  transition-all duration-300 hover:-translate-y-1">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 rounded-full
                        bg-indigo-50 dark:bg-indigo-900/20 transition-transform group-hover:scale-110"></div>
            <div class="relative space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-[0.12em]">
                        Total Buku
                    </h3>
                    <div class="p-2 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="text-3xl font-bold text-slate-800 dark:text-white">
                        {{ number_format($totalBooks ?? 0) }}
                    </span>
                    <span class="text-[11px] text-slate-400 dark:text-slate-500">
                        Koleksi terdaftar
                    </span>
                </div>
            </div>
        </a>

        {{-- Card 2: Anggota --}}
        <a href="{{ route('admin.students.index', [], false) }}"
           class="group relative overflow-hidden bg-white dark:bg-slate-800 rounded-2xl p-6
                  shadow-sm hover:shadow-xl border border-slate-200 dark:border-slate-700
                  transition-all duration-300 hover:-translate-y-1">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 rounded-full
                        bg-emerald-50 dark:bg-emerald-900/20 transition-transform group-hover:scale-110"></div>
            <div class="relative space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-[0.12em]">
                        Anggota Aktif
                    </h3>
                    <div class="p-2 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="text-3xl font-bold text-slate-800 dark:text-white">
                        {{ number_format($activeStudents ?? 0) }}
                    </span>
                    <span class="text-[11px] text-slate-400 dark:text-slate-500">
                        Siswa terdaftar
                    </span>
                </div>
            </div>
        </a>

        {{-- Card 3: Dipinjam --}}
        <div class="group relative overflow-hidden bg-white dark:bg-slate-800 rounded-2xl p-6
                    shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 rounded-full
                        bg-amber-50 dark:bg-amber-900/20"></div>
            <div class="relative space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-[0.12em]">
                        Dipinjam
                    </h3>
                    <div class="p-2 rounded-lg bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="text-3xl font-bold text-slate-800 dark:text-white">
                        {{ number_format($onLoan ?? 0) }}
                    </span>
                    <span class="text-[11px] text-slate-400 dark:text-slate-500">
                        Buku sedang dipinjam
                    </span>
                </div>
            </div>
        </div>

        {{-- Card 4: Kunjungan --}}
        <div class="group relative overflow-hidden bg-white dark:bg-slate-800 rounded-2xl p-6
                    shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 rounded-full
                        bg-blue-50 dark:bg-blue-900/20"></div>
            <div class="relative space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-[0.12em]">
                        Kunjungan Hari Ini
                    </h3>
                    <div class="p-2 rounded-lg bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                {{-- Total kunjungan --}}
                <div class="flex items-baseline justify-between">
                    <span class="text-3xl font-bold text-slate-800 dark:text-white">
                        {{ number_format($visitsToday ?? 0) }}
                    </span>
                    <span class="text-[11px] text-slate-400 dark:text-slate-500">
                        Total kunjungan
                    </span>
                </div>

                {{-- Breakdown: perorangan vs kelas --}}
                <div class="flex flex-wrap gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                    <span
                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                               bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                        Perorangan:
                        <strong>{{ $visitsTodayIndividual ?? 0 }}</strong>
                    </span>

                    <span
                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                               bg-emerald-50 dark:bg-emerald-900/40 border border-emerald-200 dark:border-emerald-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Kunjungan kelas:
                        <strong>{{ $visitsTodayClass ?? 0 }}</strong>
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- =============== CHART SECTION =============== --}}
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- 1. HARIAN --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 flex flex-col">
            <div class="mb-4">
                <h3 class="font-bold text-slate-800 dark:text-white text-lg">
                    Aktivitas Harian
                </h3>
                <p class="text-xs text-slate-500">
                    {{ $currentDate->translatedFormat('d F Y') }}
                </p>
            </div>
            <div class="flex-grow flex flex-col items-center justify-center">
                <div id="chartDaily" class="w-full flex justify-center"></div>
                <div class="flex gap-4 mt-3 justify-center text-[10px] text-slate-400">
                    <div class="flex items-center gap-1">
                        <span class="block w-2 h-2 rounded-full bg-indigo-500"></span> Pinjam
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="block w-2 h-2 rounded-full bg-emerald-500"></span> Kembali
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="block w-2 h-2 rounded-full bg-blue-500"></span> Visit
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. MINGGUAN --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 flex flex-col">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="font-bold text-slate-800 dark:text-white text-lg">
                        Aktivitas Mingguan
                    </h3>
                    <p class="text-xs text-slate-500">
                        Mulai dari tanggal
                    </p>
                </div>
                <input type="date"
                       class="text-xs border-slate-200 dark:border-slate-600 rounded-lg bg-slate-50 dark:bg-slate-700
                              text-slate-600 dark:text-slate-200 focus:ring-indigo-500 px-2 py-1"
                       value="{{ $currentDate->format('Y-m-d') }}"
                       onchange="updateFilter('start_date', this.value)">
            </div>
            <div class="flex-grow flex flex-col items-center justify-center">
                <div id="chartWeekly" class="w-full flex justify-center"></div>
                <div class="w-full mt-4 grid grid-cols-3 gap-2 text-center border-t border-slate-100 dark:border-slate-700 pt-4">
                    <div>
                        <p class="text-xs text-slate-400">Pinjam</p>
                        <p class="font-bold text-indigo-600" id="val-w-loan">0</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Kembali</p>
                        <p class="font-bold text-emerald-600" id="val-w-return">0</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Visit</p>
                        <p class="font-bold text-blue-600" id="val-w-visit">0</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. BULANAN --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 flex flex-col">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="font-bold text-slate-800 dark:text-white text-lg">
                        Aktivitas Bulanan
                    </h3>
                    <p class="text-xs text-slate-500">
                        Total per bulan
                    </p>
                </div>
                <input type="month"
                       class="text-xs border-slate-200 dark:border-slate-600 rounded-lg bg-slate-50 dark:bg-slate-700
                              text-slate-600 dark:text-slate-200 focus:ring-indigo-500 px-2 py-1"
                       value="{{ request('month', $currentDate->format('Y-m')) }}"
                       onchange="updateFilter('month', this.value)">
            </div>
            <div class="flex-grow flex flex-col items-center justify-center">
                <div id="chartMonthly" class="w-full flex justify-center"></div>
                <div class="w-full mt-4 grid grid-cols-3 gap-2 text-center border-t border-slate-100 dark:border-slate-700 pt-4">
                    <div>
                        <p class="text-xs text-slate-400">Pinjam</p>
                        <p class="font-bold text-indigo-600" id="val-m-loan">0</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Kembali</p>
                        <p class="font-bold text-emerald-600" id="val-m-return">0</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Visit</p>
                        <p class="font-bold text-blue-600" id="val-m-visit">0</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

{{-- =============== CHART DATA & SCRIPT =============== --}}
<script id="data-daily" type="application/json">@json($chartDaily)</script>
<script id="data-weekly" type="application/json">@json($chartWeekly)</script>
<script id="data-monthly" type="application/json">@json($chartMonthly)</script>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
  function updateFilter(key, value) {
      const url = new URL(window.location.href);
      url.searchParams.set(key, value);
      window.location.href = url.toString();
  }

  const getData = (id) => {
      try { return JSON.parse(document.getElementById(id).textContent); }
      catch(e) { return { loans: [], returns: [], visits: [] }; }
  };
  const sum = (arr) => arr.reduce((a, b) => a + b, 0);

  const daily   = getData('data-daily');
  const weekly  = getData('data-weekly');
  const monthly = getData('data-monthly');

  const dailyVals   = [sum(daily.loans || []),   sum(daily.returns || []),   sum(daily.visits || [])];
  const weeklyVals  = [sum(weekly.loans || []),  sum(weekly.returns || []),  sum(weekly.visits || [])];
  const monthlyVals = [sum(monthly.loans || []), sum(monthly.returns || []), sum(monthly.visits || [])];

  document.getElementById('val-w-loan').innerText   = weeklyVals[0];
  document.getElementById('val-w-return').innerText = weeklyVals[1];
  document.getElementById('val-w-visit').innerText  = weeklyVals[2];

  document.getElementById('val-m-loan').innerText   = monthlyVals[0];
  document.getElementById('val-m-return').innerText = monthlyVals[1];
  document.getElementById('val-m-visit').innerText  = monthlyVals[2];

  const colors = ['#6366f1', '#10b981', '#3b82f6'];
  const labels = ['Peminjaman', 'Pengembalian', 'Kunjungan'];
  const isDark = document.documentElement.classList.contains('dark');

  const baseOptions = {
      chart: {
          type: 'donut',
          height: 240,
          fontFamily: 'Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif',
          background: 'transparent'
      },
      colors: colors,
      labels: labels,
      stroke: {
          show: true,
          width: 2,
          colors: isDark ? ['#0f172a'] : ['#ffffff']
      },
      dataLabels: { enabled: false },
      legend: { show: false },
      tooltip: {
          theme: isDark ? 'dark' : 'light',
          y: { formatter: (val) => val + ' aktivitas' }
      },
      plotOptions: {
          pie: {
              donut: {
                  size: '70%',
                  labels: {
                      show: true,
                      name: {
                          show: true,
                          fontSize: '10px',
                          offsetY: -5,
                          color: '#94a3b8'
                      },
                      value: {
                          show: true,
                          fontSize: '20px',
                          fontWeight: 'bold',
                          offsetY: 5,
                          color: isDark ? '#ffffff' : '#0f172a'
                      },
                      total: {
                          show: true,
                          label: 'Total',
                          color: '#94a3b8',
                          formatter: (w) => w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                      }
                  }
              }
          }
      }
  };

  new ApexCharts(document.querySelector("#chartDaily"),   { ...baseOptions, series: dailyVals }).render();
  new ApexCharts(document.querySelector("#chartWeekly"),  { ...baseOptions, series: weeklyVals }).render();
  new ApexCharts(document.querySelector("#chartMonthly"), { ...baseOptions, series: monthlyVals }).render();
</script>
@endsection
