@extends('layouts.app')

@section('title', 'Laporan Perpustakaan')

@section('content')
@php
    // Total kunjungan kelas & jumlah siswa selama range laporan (pakai daily summary)
    $totalClassVisitsRange   = isset($chartDaily['class_visits']) ? array_sum($chartDaily['class_visits']) : 0;
    $totalClassStudentsRange = isset($chartDaily['class_students']) ? array_sum($chartDaily['class_students']) : 0;
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 font-sans">

    {{-- HEADER & FILTER (Clean Row) --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight">
                Dashboard Laporan
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Ringkasan aktivitas perpustakaan periode ini.
            </p>
        </div>

        <div class="flex items-center gap-2 bg-white dark:bg-slate-800 p-1 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
            <form method="GET" class="flex items-center gap-2">
                <input type="date" name="from" value="{{ $from }}" 
                    class="border-none text-xs font-medium bg-transparent focus:ring-0 text-slate-600 dark:text-slate-300 p-2">
                <span class="text-slate-300">/</span>
                <input type="date" name="to" value="{{ $to }}" 
                    class="border-none text-xs font-medium bg-transparent focus:ring-0 text-slate-600 dark:text-slate-300 p-2">
                
                <button type="submit" class="px-4 py-1.5 bg-slate-900 dark:bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-slate-800 transition-colors">
                    Filter
                </button>
            </form>
            <div class="w-px h-6 bg-slate-200 dark:bg-slate-700 mx-1"></div>
            <a href="{{ route('admin.reports.export', ['from' => $from, 'to' => $to]) }}" 
               class="p-2 text-slate-500 hover:text-emerald-600 transition-colors" title="Export CSV">
               <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
               </svg>
            </a>
        </div>
    </div>

    {{-- KPI CARDS (Simple & Informatif) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Card 1 --}}
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Peminjaman</p>
                    <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1">
                        {{ number_format($totalLoansRange) }}
                    </h3>
                </div>
                <div class="p-2 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Card 2 --}}
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pengembalian</p>
                    <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1">
                        {{ number_format($totalReturnsRange) }}
                    </h3>
                </div>
                <div class="p-2 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Card 3: Kunjungan + Kunjungan Kelas --}}
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kunjungan</p>
                    <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1">
                        {{ number_format($totalVisitsRange) }}
                    </h3>
                    <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400 leading-snug">
                        Termasuk 
                        <span class="font-semibold">{{ $totalClassVisitsRange }}</span> kunjungan kelas
                        (<span class="font-semibold">{{ $totalClassStudentsRange }}</span> siswa).
                    </p>
                </div>
                <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Card 4 --}}
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-rose-100 dark:border-rose-900/30 shadow-sm relative overflow-hidden">
            <div class="flex justify-between items-start relative z-10">
                <div>
                    <p class="text-xs font-bold text-rose-400 uppercase tracking-wider">Keterlambatan</p>
                    <h3 class="text-2xl font-bold text-rose-600 mt-1">
                        {{ number_format($totalLateLoansRange) }}
                    </h3>
                </div>
                <div class="p-2 bg-rose-50 dark:bg-rose-900/20 rounded-lg text-rose-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-rose-50 dark:bg-rose-900/10 rounded-full blur-xl"></div>
        </div>
    </div>

    {{-- MAIN CHART SECTION --}}
    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-800 dark:text-white">Statistik Aktivitas</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Menampilkan kunjungan, kunjungan kelas, peminjaman, dan keterlambatan.
                </p>
            </div>
            
            {{-- JS Tabs (Vanilla) --}}
            <div class="flex bg-slate-100 dark:bg-slate-900 p-1 rounded-lg self-start sm:self-auto">
                <button onclick="switchChart('daily')" id="btn-daily"
                        class="chart-tab-btn px-4 py-1.5 text-xs font-bold rounded-md transition-all
                               bg-white dark:bg-slate-700 shadow text-slate-800 dark:text-white">
                    Harian
                </button>
                <button onclick="switchChart('weekly')" id="btn-weekly"
                        class="chart-tab-btn px-4 py-1.5 text-xs font-bold rounded-md transition-all
                               text-slate-500 dark:text-slate-400 hover:text-slate-700">
                    Mingguan
                </button>
                <button onclick="switchChart('monthly')" id="btn-monthly"
                        class="chart-tab-btn px-4 py-1.5 text-xs font-bold rounded-md transition-all
                               text-slate-500 dark:text-slate-400 hover:text-slate-700">
                    Bulanan
                </button>
            </div>
        </div>
        
        <div class="h-[300px] w-full">
            <canvas id="mainChart"></canvas>
        </div>

        {{-- Ringkasan Kunjungan Kelas per Tab --}}
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-700">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                    Kunjungan Kelas (Rombongan)
                </p>
                <p id="stat-class-visits" class="mt-1 text-lg font-bold text-blue-600 dark:text-blue-400">
                    0
                </p>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-700">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                    Siswa Kunjungan Kelas
                </p>
                <p id="stat-class-students" class="mt-1 text-lg font-bold text-indigo-600 dark:text-indigo-400">
                    0
                </p>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-700">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                    Periode Data
                </p>
                <p class="mt-1 text-xs font-semibold text-slate-700 dark:text-slate-200">
                    {{ $from }} s/d {{ $to }}
                </p>
            </div>
        </div>
    </div>

    {{-- TOP LISTS (Grid Layout) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        @php
            $sections = [
                ['title' => 'Top Buku', 'data' => $topBooks, 'icon' => 'book', 'field_main' => 'title', 'field_sub' => 'total', 'sub_label' => 'dipinjam'],
                ['title' => 'Top Peminjam', 'data' => $topBorrowers, 'icon' => 'user', 'field_main' => 'name', 'field_sub' => 'total', 'sub_label' => 'kali'],
                ['title' => 'Top Pengunjung', 'data' => $topVisitors, 'icon' => 'users', 'field_main' => 'name', 'field_sub' => 'total', 'sub_label' => 'hadir'],
            ];
        @endphp

        @foreach($sections as $section)
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm">
            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                {{ $section['title'] }}
            </h3>
            <div class="space-y-4">
                @forelse($section['data'] as $idx => $item)
                <div class="flex items-center gap-3">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold 
                        {{ $idx == 0 ? 'bg-amber-100 text-amber-700' : ($idx == 1 ? 'bg-slate-100 text-slate-600' : 'bg-orange-50 text-orange-800') }}">
                        {{ $idx + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-slate-800 dark:text-white truncate">
                            {{ $item->{$section['field_main']} }}
                        </p>
                        <div class="flex items-center gap-2 mt-0.5">
                            <div class="flex-1 h-1 bg-slate-100 dark:bg-slate-700 rounded-full">
                                @php 
                                    $max = $section['data']->first()->total ?? 1;
                                    $width = ($item->total / $max) * 100;
                                @endphp
                                
                            </div>
                            <span class="text-[10px] text-slate-500">{{ $item->total }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400 text-center py-4">Tidak ada data</p>
                @endforelse
            </div>
        </div>
        @endforeach

        {{-- Late List (Special Style) --}}
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-rose-100 dark:border-rose-900/30 shadow-sm">
            <h3 class="text-sm font-bold text-rose-600 dark:text-rose-400 mb-4 flex items-center gap-2">
                Terlambat Kembali
            </h3>
            <div class="space-y-3">
                @forelse($lateLoansTop as $item)
                <div class="p-2.5 rounded-xl bg-rose-50 dark:bg-rose-900/10 border border-rose-100 dark:border-rose-800/20">
                    <div class="flex justify-between items-start">
                        <p class="text-xs font-bold text-slate-800 dark:text-white truncate pr-2">
                            {{ $item->student_name }}
                        </p>
                        <span class="text-[10px] font-bold text-rose-600 bg-white dark:bg-slate-800 px-1.5 py-0.5 rounded shadow-sm border border-rose-100 dark:border-rose-900">
                            {{ $item->days_late }} Hari
                        </span>
                    </div>
                    <p class="text-[10px] text-slate-500 mt-1 truncate">
                        {{ $item->book_title }}
                    </p>
                </div>
                @empty
                <div class="text-center py-8">
                    <svg class="w-8 h-8 mx-auto text-emerald-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-xs text-slate-500">Semua buku aman!</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

{{-- DATA JSON (Hidden) --}}
<script id="chart-data-daily" type="application/json">@json($chartDaily)</script>
<script id="chart-data-weekly" type="application/json">@json($chartWeekly)</script>
<script id="chart-data-monthly" type="application/json">@json($chartMonthly)</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. Setup Data
    const rawData = {
        daily:   JSON.parse(document.getElementById('chart-data-daily').textContent),
        weekly:  JSON.parse(document.getElementById('chart-data-weekly').textContent),
        monthly: JSON.parse(document.getElementById('chart-data-monthly').textContent),
    };

    let chartInstance = null;
    const ctx = document.getElementById('mainChart').getContext('2d');

    const sumArray = (arr) => (arr || []).reduce((a, b) => a + b, 0);

    // 2. Fungsi Render Chart
    function renderChart(type) {
        const data = rawData[type];

        if (chartInstance) {
            chartInstance.destroy();
        }

        const visits          = data.visits           || [];
        const loans           = data.loans            || [];
        const late            = data.late             || [];
        const classVisits     = data.class_visits     || [];
        const classStudents   = data.class_students   || [];

        // Gradien untuk harian
        let gradientVisits = ctx.createLinearGradient(0, 0, 0, 300);
        gradientVisits.addColorStop(0, 'rgba(99, 102, 241, 0.2)');
        gradientVisits.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

        let gradientLoans = ctx.createLinearGradient(0, 0, 0, 300);
        gradientLoans.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
        gradientLoans.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

        chartInstance = new Chart(ctx, {
            type: type === 'daily' ? 'line' : 'bar',
            data: {
                labels: data.labels || [],
                datasets: [
                    {
                        label: 'Kunjungan',
                        data: visits,
                        borderColor: '#6366f1',
                        backgroundColor: type === 'daily' ? gradientVisits : '#6366f1',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        pointRadius: 0,
                        pointHoverRadius: 4,
                    },
                    {
                        label: 'Kunjungan Kelas (Rombongan)',
                        data: classVisits,
                        borderColor: '#3b82f6',
                        backgroundColor: type === 'daily' ? 'rgba(59,130,246,0.15)' : '#3b82f6',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: type === 'daily',
                        pointRadius: 0,
                        pointHoverRadius: 4,
                        type: type === 'daily' ? 'line' : 'bar',
                        barThickness: type === 'daily' ? undefined : 6,
                    },
                    {
                        label: 'Peminjaman',
                        data: loans,
                        borderColor: '#10b981',
                        backgroundColor: type === 'daily' ? gradientLoans : '#10b981',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        pointRadius: 0,
                        pointHoverRadius: 4,
                    },
                    {
                        label: 'Terlambat',
                        data: late,
                        type: 'bar',
                        backgroundColor: '#f43f5e',
                        borderWidth: 0,
                        barThickness: 5,
                    },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 6,
                            font: { size: 11 }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleColor: '#fff',
                        bodyColor: '#cbd5e1',
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: true
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    },
                    y: {
                        border: { display: false },
                        grid: { color: '#f1f5f9' },
                        beginAtZero: true
                    }
                },
                interaction: { mode: 'index', intersect: false }
            }
        });

        // Update ringkasan kunjungan kelas per tab
        const totalClassVisits   = sumArray(classVisits);
        const totalClassStudents = sumArray(classStudents);

        const elVisits   = document.getElementById('stat-class-visits');
        const elStudents = document.getElementById('stat-class-students');

        if (elVisits)   elVisits.textContent   = totalClassVisits;
        if (elStudents) elStudents.textContent = totalClassStudents;

        // Update Tombol Aktif
        document.querySelectorAll('.chart-tab-btn').forEach(btn => {
            btn.classList.remove('bg-white', 'dark:bg-slate-700', 'shadow', 'text-slate-800', 'dark:text-white');
            btn.classList.add('text-slate-500', 'dark:text-slate-400');
        });
        const activeBtn = document.getElementById('btn-' + type);
        if (activeBtn) {
            activeBtn.classList.remove('text-slate-500', 'dark:text-slate-400');
            activeBtn.classList.add('bg-white', 'dark:bg-slate-700', 'shadow', 'text-slate-800', 'dark:text-white');
        }
    }

    // Init pertama kali
    document.addEventListener('DOMContentLoaded', () => {
        renderChart('daily');
    });

    // Fungsi global untuk switch chart
    window.switchChart = function(type) {
        renderChart(type);
    }
</script>
@endsection
