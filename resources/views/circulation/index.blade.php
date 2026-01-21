@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- =============== HEADER & INFO =============== --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-sky-500">
                Sirkulasi Buku
            </p>
            <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-slate-800 dark:text-white">
                Halaman Pinjam &amp; Kembali
            </h1>
            <p class="mt-2 text-xs md:text-sm text-slate-500 dark:text-slate-400">
                Urutan mudah untuk petugas dan siswa: scan kartu siswa, lalu scan buku.
            </p>
        </div>
        <div class="hidden sm:block">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full 
                         bg-sky-50 dark:bg-slate-800 text-sky-700 dark:text-sky-200 
                         text-[11px] font-semibold border border-sky-100 dark:border-slate-700">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                Scanner siap digunakan
            </span>
        </div>
    </div>

    {{-- =============== ALERTS =============== --}}
    @if(session('ok'))
        <div class="mb-4 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/50 flex items-center gap-3">
            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-900/60"></div>
            <div class="flex-1">
                <p class="font-medium text-emerald-800 dark:text-emerald-300 text-sm">{{ session('ok') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-4 rounded-2xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800/50 flex items-start gap-3">
            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-rose-100 dark:bg-rose-900/60"></div>
            <div class="flex-1 text-sm">
                <p class="font-semibold text-rose-800 dark:text-rose-200 mb-1">
                    Terjadi kesalahan:
                </p>
                <ul class="list-disc list-inside text-rose-700 dark:text-rose-300 space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @if(session('err'))
        <div class="mb-4 p-4 rounded-2xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800/50 flex items-center gap-3">
            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-rose-100 dark:bg-rose-900/60"></div>
            <div class="flex-1">
                <p class="font-medium text-rose-800 dark:text-rose-300 text-sm">{{ session('err') }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- ========== KIRI: SCANNER + RIWAYAT ========== --}}
        <div class="lg:col-span-7 flex flex-col gap-6">

            {{-- 1. PANEL SCANNER --}}
            <section id="scanner-panel"
                     class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 shadow-sm border border-sky-100 dark:border-slate-800">
                {{-- Judul + Mode --}}
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-sky-500">
                            Langkah 1
                        </p>
                        <h2 class="text-lg md:text-xl font-bold text-slate-800 dark:text-white">
                            Scan barcode untuk transaksi cepat
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            Urutan: scan kartu siswa &rarr; scan buku. Sistem otomatis memilih pinjam / kembali sesuai mode.
                        </p>
                    </div>
                    <div class="w-full md:w-auto">
                        <div class="flex bg-slate-100 dark:bg-slate-800 rounded-full p-1">
                            <button id="modeBorrow" type="button"
                                    class="flex-1 text-center text-xs font-semibold rounded-full py-2
                                           bg-white text-sky-700 dark:bg-slate-700 dark:text-white shadow-sm">
                                Mode Pinjam
                            </button>
                            <button id="modeReturn" type="button"
                                    class="flex-1 text-center text-xs font-semibold rounded-full py-2
                                           text-slate-500 dark:text-slate-400">
                                Mode Kembali
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Input scanner --}}
                <div class="space-y-4">
                    <div class="rounded-2xl border border-sky-200 bg-sky-50/60 dark:bg-slate-900 dark:border-sky-800 px-3 py-3">
                        <input id="scanInput"
                               autocomplete="off"
                               autofocus
                               tabindex="1"
                               class="w-full rounded-xl bg-white dark:bg-slate-950/70 border-0 ring-1 ring-slate-200 dark:ring-slate-700
                                      px-4 py-3 text-base sm:text-lg font-mono tracking-[0.22em] text-slate-800 dark:text-slate-50
                                      placeholder:text-slate-400 focus:ring-2 focus:ring-sky-400 outline-none transition"
                               placeholder="Klik di sini lalu scan barcode siswa..."
                        >
                        <div class="mt-1 flex justify-between text-[11px] text-slate-400">
                            <span>Kode akan terbaca otomatis, tanpa klik apa pun.</span>
                            <span>[ESC] untuk menghapus</span>
                        </div>
                    </div>

                    {{-- error khusus scanner --}}
                    @if($errors->has('student_barcode') || $errors->has('book_barcode'))
                        <p class="text-xs font-medium text-rose-600 dark:text-rose-400">
                            {{ $errors->first('student_barcode') ?: $errors->first('book_barcode') }}
                        </p>
                    @endif

                    {{-- Tampilan terakhir terbaca --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-1">
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-700">
                            <label class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 block mb-1">
                                Barcode siswa
                            </label>
                            <input id="scanStudent"
                                   readonly
                                   class="w-full bg-transparent border-none p-0 text-sm font-semibold text-slate-700 dark:text-slate-200 focus:ring-0"
                                   placeholder="Belum ada">
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-700">
                            <label class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 block mb-1">
                                Barcode buku
                            </label>
                            <input id="scanBook"
                                   readonly
                                   class="w-full bg-transparent border-none p-0 text-sm font-semibold text-slate-700 dark:text-slate-200 focus:ring-0"
                                   placeholder="Belum ada">
                        </div>
                    </div>
                </div>

                {{-- HIDDEN FORMS (JANGAN DIUBAH ID-NYA) --}}
                <form id="formBorrow" action="{{ route('circulation.borrow') }}" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="student_barcode" id="fBorrowStudent">
                    <input type="hidden" name="book_barcode"    id="fBorrowBook">
                    <input type="hidden" name="days" value="7">
                </form>

                <form id="formReturn" action="{{ route('circulation.return') }}" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="student_barcode" id="fReturnStudent">
                    <input type="hidden" name="book_barcode"    id="fReturnBook">
                </form>

                <div class="mt-5 pt-3 border-t border-slate-100 dark:border-slate-700 text-center">
                    <span class="inline-block text-[11px] font-mono text-slate-400 bg-slate-100 dark:bg-slate-800 px-3 py-1 rounded-full">
                        Urutan scan: siswa → buku · Tekan ESC untuk mengulang
                    </span>
                </div>
            </section>

            {{-- 2. RIWAYAT SESI INI --}}
            <section class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col">
                <div class="px-5 sm:px-6 pt-5 pb-3 border-b border-slate-100 dark:border-slate-800">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">
                        Riwayat
                    </p>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white">
                        15 transaksi terakhir
                    </h2>
                </div>

                <div class="flex-1 overflow-y-auto max-h-[380px]">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 dark:bg-slate-900/60 sticky top-0 z-10 text-[11px] uppercase text-slate-500 font-semibold">
                            <tr>
                                <th class="px-5 sm:px-6 py-2 border-b border-slate-100 dark:border-slate-800 w-1/5">Waktu</th>
                                <th class="px-5 sm:px-6 py-2 border-b border-slate-100 dark:border-slate-800 w-3/5">Detail</th>
                                <th class="px-5 sm:px-6 py-2 border-b border-slate-100 dark:border-slate-800 text-right w-1/5">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                            @forelse($recent as $r)
                                @php
                                    $r = (object)$r;
                                    $time        = \Illuminate\Support\Carbon::parse($r->created_at ?? now())->format('H:i');
                                    $bookTitle   = $r->book_title   ?? 'Buku';
                                    $studentName = $r->student_name ?? 'Anggota';
                                    $isReturned  = $r->returned_at  ?? false;
                                @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors">
                                    <td class="px-5 sm:px-6 py-2.5 text-slate-500 whitespace-nowrap">
                                        {{ $time }}
                                    </td>
                                    <td class="px-5 sm:px-6 py-2.5">
                                        <div class="font-medium text-slate-800 dark:text-slate-200 truncate" title="{{ $bookTitle }}">
                                            {{ $bookTitle }}
                                        </div>
                                        <div class="text-xs text-slate-500 truncate">
                                            {{ $studentName }}
                                        </div>
                                    </td>
                                    <td class="px-5 sm:px-6 py-2.5 text-right">
                                        @if($isReturned)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                         bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">
                                                Kembali
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                         bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                                                Pinjam
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-10 text-center text-slate-400 text-sm italic">
                                        Belum ada transaksi baru di sesi ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        {{-- ========== KANAN: FORM MANUAL ========== --}}
        <div class="lg:col-span-5 flex flex-col gap-6">

            {{-- 3. PINJAM MANUAL --}}
            <section id="manual-panel-borrow"
                     class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 shadow-sm border border-rose-100 dark:border-rose-800/70">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-rose-500">
                    Form petugas
                </p>
                <h2 class="text-lg font-bold text-slate-800 dark:text-white mt-1">
                    Pinjam buku (manual)
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 mb-4">
                    Gunakan jika scan otomatis gagal atau kartu barcodenya rusak.
                </p>

                <form action="{{ route('circulation.borrow') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="manual-input-field">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1 block">
                            Barcode siswa
                        </label>
                        <input name="student_barcode"
                               placeholder="Scan / ketik barcode siswa"
                               tabindex="2"
                               class="w-full rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700
                                      focus:ring-2 focus:ring-rose-400 focus:border-rose-400 px-3 py-2.5 text-sm">
                        @error('student_barcode')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="manual-input-field">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1 block">
                            Barcode buku
                        </label>
                        <input name="book_barcode"
                               placeholder="Scan / ketik barcode buku"
                               tabindex="3"
                               class="w-full rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700
                                      focus:ring-2 focus:ring-rose-400 focus:border-rose-400 px-3 py-2.5 text-sm">
                        @error('book_barcode')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="manual-input-field">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1 block">
                            Durasi pinjam (hari)
                        </label>
                        <input type="number" name="days" value="7" min="1" max="30" title="Durasi (hari)" tabindex="4"
                               class="w-24 text-center rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700
                                      focus:ring-2 focus:ring-rose-400 focus:border-rose-400 px-3 py-2 text-sm">
                    </div>

                    <button type="submit" tabindex="5"
                            class="w-full bg-rose-500 hover:bg-rose-600 text-white font-semibold py-2.5 rounded-xl transition">
                        Proses pinjam manual
                    </button>
                </form>
            </section>

            {{-- 4. PENGEMBALIAN MANUAL --}}
            <section id="manual-panel-return"
                     class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 shadow-sm border border-emerald-100 dark:border-emerald-800/70 hidden">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-emerald-500">
                    Form petugas
                </p>
                <h2 class="text-lg font-bold text-slate-800 dark:text-white mt-1">
                    Pengembalian buku (manual)
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 mb-4">
                    Gunakan jika buku dikembalikan tanpa scan otomatis.
                </p>

                <form action="{{ route('circulation.return') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="manual-input-field">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1 block">
                            Barcode siswa
                        </label>
                        <input name="student_barcode"
                               placeholder="Scan / ketik barcode siswa"
                               tabindex="6"
                               class="w-full rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700
                                      focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 px-3 py-2.5 text-sm">
                        @error('student_barcode')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="manual-input-field">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1 block">
                            Barcode buku
                        </label>
                        <input name="book_barcode"
                               placeholder="Scan / ketik barcode buku"
                               tabindex="7"
                               class="w-full rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700
                                      focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 px-3 py-2.5 text-sm">
                        @error('book_barcode')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" tabindex="8"
                            class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-2.5 rounded-xl transition">
                        Proses kembali manual
                    </button>
                </form>
            </section>
        </div>
    </div>
</div>

{{-- =============== JAVASCRIPT SCANNER LOGIC =============== --}}
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

    const fBorrowS = document.getElementById('fBorrowStudent');
    const fBorrowB = document.getElementById('fBorrowBook');
    const fReturnS = document.getElementById('fReturnStudent');
    const fReturnB = document.getElementById('fReturnBook');

    const activeClass   = ['bg-white', 'text-sky-700', 'shadow-sm', 'dark:bg-slate-700', 'dark:text-white'];
    const inactiveClass = ['text-slate-500', 'dark:text-slate-400'];

    let mode   = 'borrow';
    let buffer = '';
    let lastTs = 0;
    const SCAN_GAP = 80;

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

    function resetScan() {
        buffer = '';
        if (scanStudent) scanStudent.value = '';
        if (scanBook)    scanBook.value    = '';
        if (scanInput) {
            scanInput.value = '';
            scanInput.placeholder = "Klik di sini lalu scan barcode siswa...";
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

    function pushCode(code) {
        if (!scanStudent.value) {
            scanStudent.value = code;
            scanInput.placeholder = "Sekarang scan barcode buku...";
        } else if (!scanBook.value) {
            scanBook.value = code;
            scanInput.placeholder = "Memproses transaksi...";

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
            resetScan();
            scanStudent.value = code;
            scanInput.placeholder = "Sekarang scan barcode buku...";
        }

        scanInput.value = '';
    }

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
