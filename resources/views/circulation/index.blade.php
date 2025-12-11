@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- =============== HEADER & INFO =============== --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-800 dark:text-white">Sirkulasi Cepat</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Scan barcode kartu anggota dan buku untuk memproses Pinjam & Kembali.</p>
        </div>
        <div class="hidden sm:block">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300 text-xs font-bold border border-indigo-100 dark:border-indigo-800">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                </span>
                Scanner Ready
            </span>
        </div>
    </div>

    {{-- =============== ALERTS (Tema Keren & Rich) =============== --}}
    @if(session('ok'))
    <div class="mb-6 p-4 rounded-xl bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 border border-emerald-200 dark:border-emerald-800/50 flex items-center gap-3">
        <div class="flex-shrink-0 p-2 rounded-lg bg-emerald-100 dark:bg-emerald-900/50">
            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div class="flex-1">
            <p class="font-medium text-emerald-800 dark:text-emerald-300">{{ session('ok') }}</p>
        </div>
    </div>
    @endif
    @if(session('err'))
    <div class="mb-6 p-4 rounded-xl bg-gradient-to-r from-rose-50 to-pink-50 dark:from-rose-900/20 dark:to-pink-900/20 border border-rose-200 dark:border-rose-800/50 flex items-center gap-3">
        <div class="flex-shrink-0 p-2 rounded-lg bg-rose-100 dark:bg-rose-900/50">
            <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div class="flex-1">
            <p class="font-medium text-rose-800 dark:text-rose-300">{{ session('err') }}</p>
        </div>
    </div>
    @endif


    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- KOLOM KIRI (7/12): SCANNER + RIWAYAT --}}
        <div class="lg:col-span-7 flex flex-col gap-6">

            {{-- 1. SCANNER PANEL (Card Keren & Fokus) --}}
            <section id="scanner-panel" class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-lg border border-indigo-200 dark:border-indigo-700 relative overflow-hidden">
                
                {{-- Hiasan Background Simpel --}}
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 rounded-full bg-indigo-50 dark:bg-indigo-900/10 pointer-events-none"></div>

                <h2 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2 mb-4 relative z-10">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    Scanner Otomatis
                </h2>

                {{-- Mode Switcher --}}
                <div class="flex p-1 bg-slate-100 dark:bg-slate-900/50 rounded-xl mb-6 relative z-10">
                    <button id="modeBorrow" class="flex-1 flex items-center justify-center gap-2 py-2.5 text-sm font-bold rounded-lg transition-all duration-200 shadow-sm bg-white text-indigo-600 dark:bg-slate-700 dark:text-white ring-1 ring-slate-200 dark:ring-white/10">
                        <span>📤</span> Mode Pinjam
                    </button>
                    <button id="modeReturn" class="flex-1 flex items-center justify-center gap-2 py-2.5 text-sm font-bold rounded-lg transition-all duration-200 text-slate-500 dark:text-slate-400 hover:text-slate-700">
                        <span>📥</span> Mode Kembali
                    </button>
                </div>

                <div class="space-y-4 relative z-10">
                    {{-- Main Input --}}
                    <div class="relative group">
                        {{-- Efek Glow Lebih Terkontrol --}}
                        <div class="absolute -inset-0.5 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-xl opacity-30 group-focus-within:opacity-100 transition duration-500 blur-sm"></div>
                        <input id="scanInput" autocomplete="off" autofocus tabindex="1"
                            class="relative block w-full rounded-xl bg-white dark:bg-slate-900 border-0 ring-1 ring-slate-200 dark:ring-slate-700 px-4 py-3 text-lg font-mono tracking-wider text-slate-800 dark:text-white placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500 transition-shadow"
                            placeholder="Klik & Scan Barcode..."
                        >
                        <div class="absolute right-3 top-1/2 -translate-y-1/2">
                            <span class="animate-pulse w-2 h-2 rounded-full bg-indigo-500"></span>
                        </div>
                    </div>
                    
                    {{-- Display Readonly --}}
                    <div class="grid grid-cols-2 gap-3 mt-2">
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1 block">ID Siswa</label>
                            <input id="scanStudent" readonly class="w-full bg-transparent border-none p-0 text-sm font-semibold text-slate-700 dark:text-slate-200 focus:ring-0" placeholder="Awaiting Scan...">
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1 block">ID Buku</label>
                            <input id="scanBook" readonly class="w-full bg-transparent border-none p-0 text-sm font-semibold text-slate-700 dark:text-slate-200 focus:ring-0" placeholder="Awaiting Scan...">
                        </div>
                    </div>
                </div>

                {{-- HIDDEN FORMS --}}
                <form id="formBorrow" action="{{ route('circulation.borrow') }}" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="student_id" id="fBorrowStudent">
                    <input type="hidden" name="book_id"    id="fBorrowBook">
                    <input type="hidden" name="days" value="7">
                </form>
                <form id="formReturn" action="{{ route('circulation.return') }}" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="student_id" id="fReturnStudent">
                    <input type="hidden" name="book_id"    id="fReturnBook">
                </form>

                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-700 text-center">
                    <span class="text-xs font-mono text-slate-400 bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded">
                        Tekan [ESC] untuk reset | Urutan: Siswa &rarr; Buku
                    </span>
                </div>
            </section>

            {{-- 2. RIWAYAT SESI INI --}}
            <section class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 flex flex-col">
                <div class="p-6 pb-0 mb-4 border-b border-slate-100 dark:border-slate-700">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2 pb-4">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Riwayat Sesi Ini (15 Transaksi Terakhir)
                    </h2>
                </div>
                {{-- Data riwayat dibatasi di Controller/query SQL untuk performa --}}
                <div class="flex-1 overflow-y-auto max-h-[400px] p-0">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 dark:bg-slate-900/50 sticky top-0 z-10 text-xs uppercase text-slate-500 font-semibold">
                            <tr>
                                <th class="px-6 py-3 border-b border-slate-100 dark:border-slate-700 w-1/5">Waktu</th>
                                <th class="px-6 py-3 border-b border-slate-100 dark:border-slate-700 w-3/5">Detail Transaksi</th>
                                <th class="px-6 py-3 border-b border-slate-100 dark:border-slate-700 text-right w-1/5">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-sm">
                            {{-- $recent harus di-pass dari Controller, berisi transaksi terbaru --}}
                            @forelse($recent as $r)
                                @php
                                    $r = (object)$r;
                                    $time = \Illuminate\Support\Carbon::parse($r->created_at ?? now())->format('H:i');
                                    $bookTitle = $r->book_title ?? 'N/A';
                                    $studentName = $r->student_name ?? 'Anggota N/A';
                                    $isReturned = $r->returned_at ?? false;
                                @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors group">
                                    <td class="px-6 py-3 text-slate-500 whitespace-nowrap">{{ $time }}</td>
                                    <td class="px-6 py-3">
                                        <div class="font-medium text-slate-800 dark:text-slate-200 truncate max-w-[250px]" title="{{ $bookTitle }}">
                                            {{ $bookTitle }}
                                        </div>
                                        <div class="text-xs text-slate-500 truncate max-w-[250px]">
                                            {{ $studentName }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        @if($isReturned)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                                Kembali
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                                                Pinjam
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-10 text-center text-slate-400 text-sm italic">
                                    Belum ada transaksi baru.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        {{-- KOLOM KANAN (5/12): MANUAL INPUT (Hanya Tampil Satu Form) --}}
        <div class="lg:col-span-5 flex flex-col gap-6">

            {{-- 3. Form Pinjam Manual --}}
            <section id="manual-panel-borrow" class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-lg border border-rose-200 dark:border-rose-700 h-full">
                <h2 class="text-lg font-bold text-rose-500 dark:text-rose-400 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Pinjam Manual
                </h2>

                <form action="{{ route('circulation.borrow') }}" method="POST" class="space-y-4">
                    @csrf
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-400 block -mb-2">ID Siswa</label>
                    <input name="student_id" placeholder="ID Siswa / NIS" tabindex="2" class="w-full rounded-lg bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 focus:ring-indigo-500 focus:border-indigo-500">
                    
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-400 block -mb-2">ID Buku</label>
                    <input name="book_id"    placeholder="ID Buku / Barcode" tabindex="3" class="w-full rounded-lg bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 focus:ring-indigo-500 focus:border-indigo-500">
                    
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-400 block -mb-2">Durasi Pinjam (Hari)</label>
                    <input type="number" name="days" value="7" min="1" max="30" title="Durasi (Hari)" tabindex="4" class="w-20 text-center rounded-lg bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 focus:ring-indigo-500 focus:border-indigo-500">
                    
                    <button type="submit" tabindex="5" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 rounded-xl transition shadow-sm shadow-indigo-200 dark:shadow-none">
                        Proses Pinjam Manual
                    </button>
                </form>
            </section>
            
            {{-- 4. Form Kembali Manual (default hidden) --}}
            <section id="manual-panel-return" class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-lg border border-emerald-200 dark:border-emerald-700 h-full hidden">
                <h2 class="text-lg font-bold text-emerald-500 dark:text-emerald-400 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    Pengembalian Manual
                </h2>

                <form action="{{ route('circulation.return') }}" method="POST" class="space-y-4">
                    @csrf
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-400 block -mb-2">ID Siswa</label>
                    <input name="student_id" placeholder="ID Siswa / NIS" tabindex="6" class="w-full rounded-lg bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 focus:ring-emerald-500 focus:border-emerald-500">
                    
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-400 block -mb-2">ID Buku</label>
                    <input name="book_id"    placeholder="ID Buku / Barcode" tabindex="7" class="w-full rounded-lg bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 focus:ring-emerald-500 focus:border-emerald-500">
                    
                    <button type="submit" tabindex="8" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-3 rounded-xl transition shadow-sm shadow-emerald-200 dark:shadow-none">
                        Proses Kembali Manual
                    </button>
                </form>
            </section>
        </div>
    </div>
</div>

{{-- =============== JAVASCRIPT SCANNER LOGIC (DISAMAKAN DENGAN VISIT) =============== --}}
<script>
(function () {
    const scanInput   = document.getElementById('scanInput');
    const scanStudent = document.getElementById('scanStudent');
    const scanBook    = document.getElementById('scanBook');
    const modeBorrow  = document.getElementById('modeBorrow');
    const modeReturn  = document.getElementById('modeReturn');
    const panelBorrow = document.getElementById('manual-panel-borrow');
    const panelReturn = document.getElementById('manual-panel-return');
    const formBorrow  = document.getElementById('formBorrow');
    const formReturn  = document.getElementById('formReturn');

    const manualInputs = document.querySelectorAll(
        '#manual-panel-borrow input, #manual-panel-borrow button, ' +
        '#manual-panel-return input, #manual-panel-return button'
    );

    // Hidden form fields
    const fBorrowS = document.getElementById('fBorrowStudent');
    const fBorrowB = document.getElementById('fBorrowBook');
    const fReturnS = document.getElementById('fReturnStudent');
    const fReturnB = document.getElementById('fReturnBook');

    // Styling Classes
    const activeClass   = ['bg-white', 'text-indigo-600', 'shadow-sm', 'ring-1', 'ring-slate-200', 'dark:bg-slate-700', 'dark:text-white', 'dark:ring-white/10'];
    const inactiveClass = ['text-slate-500', 'hover:text-slate-700', 'dark:text-slate-400'];

    let mode   = 'borrow';
    let buffer = '';
    let lastTs = 0;
    const SCAN_GAP = 80; // sedikit dilonggarkan biar scanner lebih aman

    // --- FOKUS OTOMATIS ---
    const isFocusingManualInput = () => {
        const activeElement = document.activeElement;
        return activeElement &&
            (activeElement.closest('#manual-panel-borrow') ||
             activeElement.closest('#manual-panel-return'));
    };

    const focusScanner = () => {
        if (!scanInput) return;
        if (!isFocusingManualInput() && document.activeElement !== scanInput) {
            setTimeout(() => {
                if (document.activeElement !== scanInput) {
                    scanInput.focus();
                }
            }, 50);
        }
    };

    window.addEventListener('load', focusScanner);
    window.addEventListener('focus', focusScanner);

    manualInputs.forEach(el => {
        el.addEventListener('blur', () => {
            setTimeout(focusScanner, 100);
        });
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('#scanner-panel') &&
            !e.target.closest('#manual-panel-borrow') &&
            !e.target.closest('#manual-panel-return')) {
            focusScanner();
        }
    });

    // --- LOGIKA MODE + RESET ---
    function resetScan() {
        buffer = '';
        if (scanStudent) scanStudent.value = '';
        if (scanBook)    scanBook.value    = '';
        if (scanInput) {
            scanInput.value = '';
            scanInput.placeholder = "Klik & Scan Barcode...";
        }
    }

    function setMode(m) {
        mode = m;

        if (m === 'borrow') {
            modeBorrow.classList.add(...activeClass);
            modeBorrow.classList.remove(...inactiveClass);
            modeReturn.classList.remove(...activeClass);
            modeReturn.classList.add(...inactiveClass);

            panelBorrow.classList.remove('hidden');
            panelReturn.classList.add('hidden');
        } else {
            modeReturn.classList.add(...activeClass);
            modeReturn.classList.remove(...inactiveClass);
            modeBorrow.classList.remove(...activeClass);
            modeBorrow.classList.add(...inactiveClass);

            panelReturn.classList.remove('hidden');
            panelBorrow.classList.add('hidden');
        }

        resetScan();
        focusScanner();
    }

    modeBorrow.addEventListener('click', () => setMode('borrow'));
    modeReturn.addEventListener('click', () => setMode('return'));

    setMode('borrow'); // default

    // --- LOGIKA PUSH CODE (SISWA -> BUKU -> SUBMIT) ---
    function pushCode(code) {
        if (!scanStudent.value) {
            // pertama: asumsikan ID siswa
            scanStudent.value = code;
            scanInput.placeholder = "Scan Barcode Buku...";
        } else if (!scanBook.value) {
            // kedua: asumsikan ID buku
            scanBook.value = code;
            scanInput.placeholder = "Memproses...";

            if (mode === 'borrow') {
                fBorrowS.value = scanStudent.value;
                fBorrowB.value = scanBook.value;
                formBorrow.submit();
            } else {
                fReturnS.value = scanStudent.value;
                fReturnB.value = scanBook.value;
                formReturn.submit();
            }
        } else {
            // kalau sudah ada dua, mulai sesi baru dengan code ini sebagai siswa
            resetScan();
            scanStudent.value = code;
            scanInput.placeholder = "Scan Barcode Buku...";
        }

        scanInput.value = ''; // kosongkan input fisik
    }

    // --- EVENT SCANNER ---
    scanInput.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            e.preventDefault();
            resetScan();
        }
    });

    scanInput.addEventListener('keypress', (e) => {
        const now = performance.now();

        if (now - lastTs > SCAN_GAP) {
            buffer = '';
        }
        lastTs = now;

        if (e.key === 'Enter') {
            e.preventDefault();

            // pakai buffer, tapi kalau kosong fallback ke isi input
            const code = (buffer || scanInput.value).trim();
            buffer = '';
            scanInput.value = '';

            if (code) {
                pushCode(code);
            }
            return;
        }

        if (e.key.length === 1) {
            buffer += e.key;
        }
    });
})();
</script>
@endsection
