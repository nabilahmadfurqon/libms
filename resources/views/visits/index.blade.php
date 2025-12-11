@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- =============== HEADER & INFO (Compact) =============== --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between pb-3">
        <div class="flex items-center gap-2">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                <span class="text-indigo-600">Check-in</span> Perpustakaan
            </h1>
            <span class="text-sm text-slate-500 dark:text-slate-400">| Smart Scanner Mode</span>
        </div>
        <div class="mt-3 sm:mt-0 px-3 py-1 rounded-full bg-indigo-50 dark:bg-slate-700/50">
            <span class="text-xs font-semibold text-indigo-700 dark:text-indigo-400">
                Total Hari Ini: {{ $visitsToday }} Kunjungan
            </span>
        </div>
    </div>

    {{-- =============== FLASH MESSAGES (Error & Success Feedback) =============== --}}
    @if(session('ok'))
        <div class="mb-6 p-4 rounded-xl bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 border border-emerald-200 dark:border-emerald-800/50 flex items-center gap-3">
            <div class="flex-shrink-0 p-2 rounded-lg bg-emerald-100 dark:bg-emerald-900/50">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <p class="font-medium text-emerald-800 dark:text-emerald-300">{{ session('ok') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-gradient-to-r from-rose-50 to-pink-50 dark:from-rose-900/20 dark:to-pink-900/20 border border-rose-200 dark:border-rose-800/50 flex items-center gap-3">
            <div class="flex-shrink-0 p-2 rounded-lg bg-rose-100 dark:bg-rose-900/50">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <p class="font-medium text-rose-800 dark:text-rose-300">{{ $errors->first() }}</p>
            </div>
        </div>
    @endif


    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- 1. MAIN PANEL (7/12 - SCANNER & AKTIVITAS) --}}
        <div class="lg:col-span-7">
            <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-xl border border-indigo-200 dark:border-indigo-700 h-full flex flex-col">
                
                {{-- SCANNER CARD --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl p-4 sm:p-5 shadow-sm space-y-4">

                    {{-- Header kecil --}}
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-900 flex items-center justify-center">
                                <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 7a2 2 0 012-2h3l2-2h4l2 2h3a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold tracking-wider text-slate-400 uppercase">Scanner Kunjungan</p>
                                <p class="text-sm text-slate-600 dark:text-slate-300">Fokuskan ke kolom, lalu scan kartu siswa.</p>
                            </div>
                        </div>

                        <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/40 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 dark:text-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span id="scanStatus">SIAP</span>
                        </span>
                    </div>

                    {{-- Input scanner --}}
                    <div class="relative mt-2">
                        <input
                            id="vScan"
                            autocomplete="off"
                            autofocus
                            tabindex="1"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-4 py-3 text-base sm:text-lg font-mono tracking-[0.25em] text-slate-800 dark:text-slate-50 placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/50 outline-none transition"
                            placeholder="Klik di sini & scan kartu..."
                        >
                        <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </div>

                    {{-- Info ID terakhir + nama --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm pt-2 border-t border-slate-100 dark:border-slate-800 mt-2">
                        <div>
                            <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase">ID Terakhir</p>
                            <p id="vStudentDisplay" class="mt-1 font-mono text-sm font-semibold text-slate-800 dark:text-slate-100">
                                -
                            </p>
                        </div>
                        <div class="sm:text-right">
                            <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Nama / Info</p>
                            <p id="vStudentName" class="mt-1 text-sm text-slate-600 dark:text-slate-300">
                                Menunggu scan...
                            </p>
                        </div>
                    </div>

                    <p class="mt-1 text-[11px] text-slate-400 flex items-center justify-between">
                        <span>Scanner otomatis kirim setelah kode selesai terbaca.</span>
                        <span class="font-mono">[ESC] untuk reset</span>
                    </p>
                </div>

                {{-- Hidden form untuk submit hasil scan --}}
                <form id="vForm" action="{{ route('visits.store') }}" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="student_id" id="vStudent">
                    <input type="hidden" name="purpose" value="baca">
                </form>

                {{-- TODAY'S VISITS LIST (Minimalis) --}}
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 border-b border-slate-200 dark:border-slate-700 pb-2 mt-6">
                    Aktivitas Terbaru
                </h3>
                <div class="flex-1 overflow-y-auto max-h-[450px]">
                    <div class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse(is_object($today) && method_exists($today, 'items') ? $today->items() : $today as $v)
                            @php
                                $v = (object)$v;
                                $is_class       = property_exists($v, 'class_name') && $v->class_name;
                                $student_name   = $v->student_name ?? $v->student_id ?? 'Data Siswa';
                                $student_class  = $v->student_class ?? '-';
                                $class_name     = $v->class_name ?? 'N/A';
                                $student_count  = $v->student_count ?? 0;
                                $purpose        = $v->purpose ?? 'baca';
                                $time           = \Carbon\Carbon::parse($v->visited_at ?? $v->created_at ?? now())->format('H:i:s');
                            @endphp

                            <div class="py-2.5 px-2 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 min-w-0 flex items-center gap-2">
                                        <div class="text-xs font-medium text-slate-500 dark:text-slate-400 flex-shrink-0">
                                            {{ $time }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            @if($is_class)
                                                <div class="font-medium text-emerald-600 dark:text-emerald-400 truncate">
                                                    Kelas {{ $class_name }} ({{ $student_count }} Siswa)
                                                </div>
                                            @else
                                                <div class="font-medium text-slate-800 dark:text-slate-200 truncate">
                                                    {{ $student_name }}
                                                </div>
                                                <div class="text-xs text-slate-600 dark:text-slate-400 truncate">
                                                    ID: {{ $v->student_id ?? 'N/A' }}
                                                    @if($student_class != '-')
                                                        • {{ $student_class }}
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium flex-shrink-0
                                        @if($purpose === 'baca')
                                            bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-400
                                        @elseif($purpose === 'pinjam' || $purpose === 'kembali')
                                            bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400
                                        @elseif($purpose === 'kunjungan_kelas')
                                            bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400
                                        @elseif($purpose === 'kartu_rusak')
                                            bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400
                                        @else
                                            bg-slate-100 text-slate-700 dark:bg-slate-700/50 dark:text-slate-300
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $purpose)) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-sm text-slate-500 dark:text-slate-400">
                                Belum ada aktivitas kunjungan hari ini.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Pagination Links --}}
                @if(is_object($today) && method_exists($today, 'hasPages') && $today->hasPages())
                    <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-700">
                        {{ $today->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- 2. SIDEBAR (5/12 - INPUT MANUAL & KELAS) --}}
        <div class="lg:col-span-5 space-y-6">
            
            {{-- Input Manual Per Siswa --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-lg border border-red-200 dark:border-red-700">
                <h3 class="text-lg font-bold text-red-600 dark:text-red-400 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                    Check-in Manual Siswa
                </h3>
                <form action="{{ route('visits.store') }}" method="POST" class="space-y-3">
                    @csrf
                    
                    <div class="manual-input-field">
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">ID Siswa</label>
                        <input name="student_id" placeholder="ID Siswa / NIS" required tabindex="2"
                            class="w-full text-sm rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 focus:ring-2 focus:ring-red-500 focus:border-red-500 px-3 py-2 transition-colors">
                    </div>

                    <div class="manual-input-field">
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Tujuan</label>
                        <select name="purpose" required tabindex="3"
                            class="w-full text-sm rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 focus:ring-2 focus:ring-red-500 focus:border-red-500 px-3 py-2 transition-colors">
                            <option value="baca">📖 Baca di tempat</option>
                            <option value="pinjam">📚 Pinjam buku</option>
                            <option value="kembali">↩️ Kembalikan buku</option>
                            <option value="kartu_rusak">🔄 Kartu rusak / lupa kartu</option>
                        </select>
                    </div>

                    <button type="submit" tabindex="4"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2.5 rounded-lg transition-all text-sm">
                        Submit Manual
                    </button>
                </form>
            </div>

            {{-- Form Kunjungan Kelas --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-lg border border-emerald-200 dark:border-emerald-700">
                <h3 class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Kunjungan Kelas
                </h3>

                <form action="{{ route('visits.store-class') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="purpose" value="kunjungan_kelas"> 
                    
                    <div class="grid grid-cols-2 gap-3 class-input-field">
                        <div>
                            <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Grade</label>
                            <input name="grade" placeholder="Mis. X" tabindex="5"
                                class="w-full text-sm rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Kelas</label>
                            <input name="class" placeholder="Mis. IPA 1" tabindex="6"
                                class="w-full text-sm rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 px-3 py-2">
                        </div>
                    </div>

                    <div class="class-input-field">
                        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Jml Siswa</label>
                        <input type="number" name="student_count" min="1" placeholder="Jumlah siswa" tabindex="7"
                            class="w-full text-sm rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 px-3 py-2">
                    </div>
                    
                    <button type="submit" tabindex="8"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2.5 rounded-lg transition-all text-sm">
                        Catat Kelas
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
        if (statusEl) statusEl.textContent = 'SIAP';
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
            if (statusEl) statusEl.textContent = 'MEMPROSES';

            out.value = code;
            form.submit();   // langsung kirim ke visits.store

            return;
        }

        if (e.key.length === 1) {
            buf += e.key;
        }
    });

    // init awal
    resetScanner();
})();
</script>
@endsection
