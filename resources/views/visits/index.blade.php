@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- =============== HERO SECTION KUNJUNGAN (PETUGAS) =============== --}}
    @php
        $user  = auth()->user();
        $role  = $user?->role;
        $name  = $user?->name ?: 'Petugas';

        $hour = now()->format('H');
        if ($hour < 10) {
            $greeting = 'Selamat pagi';
        } elseif ($hour < 15) {
            $greeting = 'Selamat siang';
        } elseif ($hour < 18) {
            $greeting = 'Selamat sore';
        } else {
            $greeting = 'Selamat malam';
        }
    @endphp

    <section class="relative overflow-hidden rounded-3xl min-h-[220px] md:min-h-[260px] shadow-xl ring-1 ring-slate-900/5 mb-8">
        {{-- Background foto sekolah --}}
        <img src="/assets/bg-dalamsekoalh.jpeg"
             class="absolute inset-0 w-full h-full object-cover object-center brightness-75 dark:brightness-50"
             alt="Perpustakaan">

        {{-- Overlay gradasi --}}
        <div class="absolute inset-0 bg-gradient-to-r from-slate-900/95 via-slate-900/70 to-slate-900/10"></div>

        <div class="relative h-full p-6 sm:p-10 flex flex-col justify-center z-10">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6">

                {{-- Kiri: Judul & sapaan --}}
                <div class="max-w-xl">

                    <h1 class="text-3xl md:text-4xl font-black tracking-tight text-white drop-shadow-md">
                        @if($role === 'petugas')
                            Dashboard Kunjungan Petugas
                        @elseif($role === 'pengunjung')
                            {{ $greeting }}, {{ $name }}
                        @else
                            Check-in Perpustakaan
                        @endif
                    </h1>

                    <p class="mt-2 text-slate-200/90 font-medium text-sm text-white md:text-base font-white">
                        Halaman ini digunakan untuk mencatat semua kunjungan siswa ke perpustakaan,
                        baik menggunakan kartu barcode maupun input manual.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-3">
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

                {{-- Kanan: Ringkasan hari ini --}}
                <div
                    class="w-full sm:w-72 lg:w-80 bg-slate-950/40 border border-white/10 rounded-2xl p-4 sm:p-5
                           backdrop-blur-md flex flex-col justify-between text-xs text-slate-100">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[11px] font-semibold tracking-[0.18em] uppercase text-slate-300">
                            Ringkasan Hari Ini
                        </p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-500/15 border border-emerald-400/40 text-[10px] font-semibold text-emerald-200">
                            {{ $visitsToday ?? 0 }} kunjungan
                        </span>
                    </div>

                    <div class="mt-2 grid grid-cols-3 gap-2 text-center">
                        <div class="rounded-xl bg-slate-900/60 border border-white/10 px-2 py-2">
                            <p class="text-[10px] text-slate-300 uppercase tracking-wide">Total</p>
                            <p class="mt-1 text-lg font-bold text-white">
                                {{ $visitsToday ?? 0 }}
                            </p>
                            <p class="text-[10px] text-slate-400">kunjungan</p>
                        </div>
                        <div class="rounded-xl bg-slate-900/60 border border-white/10 px-2 py-2">
                            <p class="text-[10px] text-slate-300 uppercase tracking-wide">Perorangan</p>
                            <p class="mt-1 text-lg font-bold text-emerald-200">
                                {{ $visitsTodayIndividual ?? 0 }}
                            </p>
                            <p class="text-[10px] text-slate-400">siswa</p>
                        </div>
                        <div class="rounded-xl bg-slate-900/60 border border-white/10 px-2 py-2">
                            <p class="text-[10px] text-slate-300 uppercase tracking-wide">Kelas</p>
                            <p class="mt-1 text-lg font-bold text-sky-200">
                                {{ $visitsTodayClass ?? 0 }}
                            </p>
                            <p class="text-[10px] text-slate-400">rombongan</p>
                        </div>
                    </div>

                    <div class="mt-3 pt-3 border-t border-white/10 text-[11px] leading-relaxed text-slate-200">
                        Petugas:
                        pastikan setiap siswa yang masuk perpustakaan
                        melakukan scan kartu atau dicatat manual bila tidak membawa kartu.
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- =============== FLASH MESSAGES & ERRORS =============== --}}
    @if(session('ok'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 flex items-center gap-3 shadow-sm">
            <div class="flex-1">
                <p class="font-medium text-emerald-800 dark:text-emerald-300 text-sm">{{ session('ok') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 flex items-center gap-3 shadow-sm">
            <div class="flex-1">
                <p class="font-medium text-rose-800 dark:text-rose-300 text-sm">{{ $errors->first() }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- 1. MAIN PANEL (SCANNER + LIST) --}}
        <div class="lg:col-span-7">
            <div class="bg-gradient-to-br from-sky-50 via-indigo-50 to-emerald-50 
                        dark:from-slate-900 dark:via-slate-900 dark:to-slate-900 
                        rounded-3xl p-1 shadow-xl border border-sky-100/70 dark:border-slate-700/80 h-full flex flex-col">
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 sm:p-6 h-full flex flex-col gap-6">

                    {{-- SCANNER CARD --}}
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-900/80 p-4 sm:p-5 shadow-sm space-y-4">

                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-2xl bg-sky-500 flex items-center justify-center shadow-md">
                                    <span class="text-xs font-bold text-white tracking-wide">SCAN</span>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold tracking-wider text-slate-400 uppercase">
                                        Scanner Kunjungan
                                    </p>
                                    <p class="text-sm text-slate-600 dark:text-slate-300">
                                        Fokuskan kursor ke kotak di bawah, lalu tempelkan kartu barcode siswa.
                                    </p>
                                </div>
                            </div>

                            <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/40 px-3 py-1 text-[11px] font-semibold text-emerald-700 dark:text-emerald-200">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span id="scanStatus">Siap scan</span>
                            </span>
                        </div>

                        {{-- Input scanner --}}
                        <div class="relative mt-2">
                            <input
                                id="vScan"
                                autocomplete="off"
                                autofocus
                                tabindex="1"
                                class="w-full rounded-2xl border border-sky-100 dark:border-slate-700 bg-white dark:bg-slate-950/60 px-4 py-3 text-base sm:text-lg font-mono tracking-[0.25em] text-slate-800 dark:text-slate-50 placeholder:text-slate-400 focus:border-sky-400 focus:ring-2 focus:ring-sky-300/70 outline-none transition"
                                placeholder="Klik di sini lalu scan kartu..."
                            >
                            <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center gap-1 text-[11px] text-slate-400">
                                <span class="hidden sm:inline">Otomatis Enter</span>
                            </div>
                        </div>

                        {{-- error khusus scanner barcode --}}
                        @error('student_barcode')
                            <p class="mt-2 text-xs font-medium text-rose-600 dark:text-rose-400">
                                {{ $message }}
                            </p>
                        @enderror

                        {{-- Info ID terakhir + nama --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm pt-3 border-t border-slate-100 dark:border-slate-800 mt-1">
                            <div>
                                <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Barcode Terakhir</p>
                                <p id="vStudentDisplay" class="mt-1 font-mono text-sm font-semibold text-slate-800 dark:text-slate-100 bg-slate-100/60 dark:bg-slate-800/70 rounded-lg px-3 py-1.5">
                                    -
                                </p>
                            </div>
                            <div class="sm:text-right">
                                <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Nama / Info</p>
                                <p id="vStudentName" class="mt-1 text-sm text-slate-700 dark:text-slate-200">
                                    Menunggu scan...
                                </p>
                            </div>
                        </div>

                        <p class="mt-1 text-[11px] text-slate-400 flex items-center justify-between">
                            <span>Jika salah scan, tekan <span class="font-mono px-1 rounded bg-slate-200/70 dark:bg-slate-800">ESC</span> untuk reset.</span>
                            <span class="font-mono">Scan → otomatis simpan</span>
                        </p>
                    </div>

                    {{-- Hidden form (SCAN → BARCODE) --}}
                    <form id="vForm" action="{{ route('visits.store') }}" method="POST" class="hidden">
                        @csrf
                        <input type="hidden" name="student_barcode" id="vStudent">
                        <input type="hidden" name="purpose" value="baca">
                    </form>

                    {{-- TODAY'S VISITS LIST --}}
                    <div class="flex flex-col flex-1">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-base md:text-lg font-bold text-slate-800 dark:text-white">
                                Aktivitas Kunjungan Hari Ini
                            </h3>
                            <span class="text-[11px] text-slate-400 dark:text-slate-500">
                                Menampilkan kunjungan hari ini saja.
                            </span>
                        </div>

                        <div class="flex-1 overflow-y-auto max-h-[420px] rounded-2xl border border-slate-100 dark:border-slate-800 bg-white/70 dark:bg-slate-900/70">
                            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                                @forelse(is_object($today) && method_exists($today, 'items') ? $today->items() : $today as $v)
                                    @php
                                        $v = (object)$v;
                                        $is_class       = property_exists($v, 'class_name') && $v->class_name;
                                        $student_name   = $v->student_name ?? $v->student_id ?? 'Data Siswa';
                                        $student_class  = $v->student_class ?? '-';
                                        $class_name     = $v->class_name ?? 'N/A';
                                        $student_count  = $v->student_count ?? 0;
                                        $purpose        = $v->purpose ?? 'baca';
                                        $time           = \Carbon\Carbon::parse($v->visited_at ?? $v->created_at ?? now())->format('H:i');
                                    @endphp

                                    <div class="py-2.5 px-3 hover:bg-sky-50/60 dark:hover:bg-slate-800/70 transition-colors">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex-1 min-w-0 flex items-center gap-3">
                                                <div class="flex flex-col items-center text-[10px] text-slate-500 dark:text-slate-400 flex-shrink-0">
                                                    <span class="font-semibold">{{ $time }}</span>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    @if($is_class)
                                                        <div class="font-semibold text-emerald-600 dark:text-emerald-400 truncate">
                                                            Kelas {{ $class_name }} ({{ $student_count }} siswa)
                                                        </div>
                                                        <div class="text-[11px] text-slate-500 dark:text-slate-400">
                                                            Kunjungan kelas
                                                        </div>
                                                    @else
                                                        <div class="font-semibold text-slate-800 dark:text-slate-50 truncate">
                                                            {{ $student_name }}
                                                        </div>
                                                        <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                                            ID: {{ $v->student_id ?? 'N/A' }}
                                                            @if($student_class != '-')
                                                                • Kelas {{ $student_class }}
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium flex-shrink-0
                                                @if($purpose === 'baca')
                                                    bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-300
                                                @elseif($purpose === 'pinjam' || $purpose === 'kembali')
                                                    bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300
                                                @elseif($purpose === 'kunjungan_kelas')
                                                    bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300
                                                @elseif($purpose === 'kartu_rusak')
                                                    bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300
                                                @else
                                                    bg-slate-100 text-slate-700 dark:bg-slate-700/60 dark:text-slate-200
                                                @endif">
                                                {{ ucfirst(str_replace('_', ' ', $purpose)) }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-6 text-center text-sm text-slate-500 dark:text-slate-400">
                                        Belum ada aktivitas kunjungan hari ini.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        @if(is_object($today) && method_exists($today, 'hasPages') && $today->hasPages())
                            <div class="pt-3 mt-3 border-t border-slate-100 dark:border-slate-700">
                                {{ $today->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. SIDEBAR (INPUT MANUAL & KELAS) --}}
        <div class="lg:col-span-5 space-y-6">

            {{-- Input Manual Per Siswa --}}
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 shadow-lg border border-rose-100 dark:border-rose-800/70">
                <h3 class="text-lg font-bold text-rose-600 dark:text-rose-400 mb-4">
                    Check-in Manual Siswa
                </h3>
                <form action="{{ route('visits.store') }}" method="POST" class="space-y-3">
                    @csrf

                    <div class="manual-input-field">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Barcode Siswa
                        </label>
                        <input name="student_barcode" placeholder="Scan atau ketik barcode siswa" required tabindex="2"
                               class="w-full text-sm rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 focus:ring-2 focus:ring-rose-400 focus:border-rose-400 px-3 py-2.5 transition-colors">
                        @error('student_barcode')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="manual-input-field">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Tujuan Kunjungan
                        </label>
                        <select name="purpose" required tabindex="3"
                                class="w-full text-sm rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 focus:ring-2 focus:ring-rose-400 focus:border-rose-400 px-3 py-2.5 transition-colors">
                            <option value="baca">Baca di tempat</option>
                            <option value="pinjam">Pinjam buku</option>
                            <option value="kembali">Kembalikan buku</option>
                            <option value="kartu_rusak">Kartu rusak / lupa kartu</option>
                        </select>
                    </div>

                    <button type="submit" tabindex="4"
                            class="w-full bg-rose-500 hover:bg-rose-600 text-white font-semibold py-2.5 rounded-xl transition-all text-sm shadow-sm">
                        Simpan Check-in
                    </button>
                </form>
            </div>

            {{-- Form Kunjungan Kelas --}}
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 shadow-lg border border-emerald-100 dark:border-emerald-800/70">
                <h3 class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mb-4">
                    Kunjungan Kelas
                </h3>

                <form action="{{ route('visits.store-class') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="purpose" value="kunjungan_kelas">

                    <div class="grid grid-cols-2 gap-3 class-input-field">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Grade</label>
                            <input name="grade" placeholder="Mis. 4 / 5 / 6" tabindex="5"
                                   class="w-full text-sm rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 px-3 py-2.5">
                            @error('grade')
                                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kelas</label>
                            <input name="class" placeholder="Mis. A / B / C" tabindex="6"
                                   class="w-full text-sm rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 px-3 py-2.5">
                            @error('class')
                                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="class-input-field">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Jumlah Siswa</label>
                        <input type="number" name="student_count" min="1" placeholder="Contoh: 30" tabindex="7"
                               class="w-full text-sm rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 px-3 py-2.5">
                        @error('student_count')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" tabindex="8"
                            class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-2.5 rounded-xl transition-all text-sm shadow-sm">
                        Catat Kunjungan Kelas
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- =============== JAVASCRIPT SCANNER (AUTO SUBMIT) =============== --}}
<script>
(function(){
    const SCAN_GAP = 80;

    const input    = document.getElementById('vScan');
    const out      = document.getElementById('vStudent');
    const display  = document.getElementById('vStudentDisplay');
    const nameEl   = document.getElementById('vStudentName');
    const statusEl = document.getElementById('scanStatus');
    const form     = document.getElementById('vForm');

    if (!input || !form) return;

    let buf  = '';
    let last = 0;

    const resetScanner = () => {
        buf = '';
        if (input)   input.value = '';
        if (display) display.textContent = '-';
        if (nameEl)  nameEl.textContent = 'Menunggu scan...';
        if (statusEl) statusEl.textContent = 'Siap scan';
    };

    const focusScanner = () => {
        const active = document.activeElement;
        const isManual =
            active && (active.closest('.manual-input-field') || active.closest('.class-input-field'));

        if (!isManual && active !== input) {
            input.focus();
        }
    };

    window.addEventListener('load', focusScanner);
    window.addEventListener('focus', focusScanner);

    document.addEventListener('click', (e) => {
        if (!e.target.closest('input, textarea, select, button')) {
            focusScanner();
        }
    });

    document
        .querySelectorAll('.manual-input-field input, .manual-input-field select, .class-input-field input, .class-input-field select')
        .forEach(el => {
            el.addEventListener('blur', () => {
                setTimeout(focusScanner, 100);
            });
        });

    input.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            e.preventDefault();
            resetScanner();
            focusScanner();
        }
    });

    input.addEventListener('keypress', (e) => {
        const now = performance.now();

        if (now - last > SCAN_GAP) {
            buf = '';
        }
        last = now;

        if (e.key === 'Enter') {
            e.preventDefault();

            const code = (buf || input.value).trim();
            buf = '';
            input.value = '';

            if (!code) {
                focusScanner();
                return;
            }

            if (display) display.textContent = code;
            if (nameEl)  nameEl.textContent = 'Menyimpan kunjungan...';
            if (statusEl) statusEl.textContent = 'Memproses';

            out.value = code;   // barcode siswa
            form.submit();

            return;
        }

        if (e.key.length === 1) {
            buf += e.key;
        }
    });

    resetScanner();
})();
</script>
@endsection
