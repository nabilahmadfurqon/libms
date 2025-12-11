{{-- resources/views/admin/barcodes/books.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-[297mm] mx-auto px-4 py-6 print:p-0 print:m-0 print:w-full">

    {{-- TOOLBAR (tidak ikut di-print) --}}
    <div class="mb-6 print:hidden flex flex-wrap items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-2xl font-black text-slate-800">Cetak Kartu LibMS</h1>
            <p class="text-slate-500 text-sm mt-1">
                Barcode Buku | Total: {{ $items->count() }} Buku
            </p>
        </div>

        <div class="flex items-center gap-4">
            <a href="{{ route('admin.books.index') }}"
               class="text-slate-600 hover:text-slate-900 font-medium text-sm">
                Batal
            </a>
            <button onclick="window.print()"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-lg shadow-emerald-200 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak Sekarang
            </button>
        </div>
    </div>

    {{-- GRID LABEL (2 kolom) --}}
    <div id="labelGrid" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($items as $it)
            @php
                $code = $it->barcode ?: $it->book_id;
                $longTitle = mb_strlen($it->title ?? '') > 40;
            @endphp

            <div class="book-card-wrapper relative">
                <div class="book-card bg-white relative flex flex-col justify-between">
                    {{-- BACKGROUND PATTERN --}}
                    <div class="pointer-events-none">
                        <div class="absolute -top-8 -right-10 w-24 h-24 bg-emerald-100 rounded-full opacity-60"></div>
                        <div class="absolute -bottom-8 -left-10 w-24 h-24 bg-sky-100 rounded-full opacity-35"></div>
                    </div>

                    {{-- HEADER ala kartu siswa --}}
                    <div class="relative z-10 flex items-center justify-between gap-2 px-4 pt-2 pb-1.5 border-b border-emerald-100 bg-gradient-to-r from-emerald-50 via-white to-sky-50">
                        <div class="flex items-center gap-1">
                            <div class="card-logo">
                                <img src="{{ asset('assets/logo-yapi.png') }}" alt="YAPI">
                            </div>
                            <div class="card-logo overlap">
                                <img src="{{ asset('assets/logo-alazhar13.png') }}" alt="Al Azhar 13">
                            </div>
                        </div>
                        <div class="flex-grow text-right">
                            <p class="text-[8px] font-semibold text-emerald-900 tracking-[0.14em] uppercase">
                                Yayasan Pesantren Islam Al Azhar
                            </p>
                            <p class="text-[9px] font-black text-emerald-900 leading-tight tracking-wide">
                                SDI AL AZHAR RAWAMANGUN 13
                            </p>
                            <p class="text-[8px] text-emerald-700 font-medium mt-0.5">
                                Perpustakaan Sekolah
                            </p>
                        </div>
                    </div>

                    {{-- BODY: Info Buku --}}
                    <div class="relative z-10 px-4 py-2 flex flex-col gap-1.5">
                        <div>
                            <span class="block text-[8px] uppercase tracking-[0.16em] text-slate-400 font-semibold">
                                Nama Buku
                            </span>
                            <p class="book-title {{ $longTitle ? 'long' : '' }}" title="{{ $it->title }}">
                                {{ $it->title }}
                            </p>
                        </div>

                        <div class="flex gap-6 text-[9px] mt-0.5">
                            <div>
                                <span class="block uppercase tracking-[0.16em] text-slate-400 font-semibold">
                                    Kode Buku
                                </span>
                                <span class="text-[11px] font-bold text-emerald-700 font-mono">
                                    {{ $code }}
                                </span>
                            </div>
                            <div>
                                <span class="block uppercase tracking-[0.16em] text-slate-400 font-semibold">
                                    ISBN
                                </span>
                                <span class="text-[11px] font-semibold text-slate-700">
                                    {{ $it->isbn ?: '—' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- AREA BARCODE (persegi panjang, jelas) --}}
                    <div class="relative z-10 bg-white px-4 pb-2.5 pt-1.5 border-t border-slate-100 flex flex-col items-center justify-center">
                        <svg
                            class="barcode"
                            jsbarcode-format="code128"
                            jsbarcode-value="{{ $code }}"
                            jsbarcode-displayValue="false"
                            jsbarcode-height="52"
                            jsbarcode-width="2"
                            jsbarcode-margin="0"
                            jsbarcode-background="#ffffff"
                            jsbarcode-lineColor="#000000">
                        </svg>
                        <div class="book-code-text font-mono text-slate-700 mt-1 whitespace-nowrap">
                            {{ $code }}
                        </div>
                    </div>
                </div>

                <div class="cut-guide border-dashed border-slate-300"></div>
            </div>
        @empty
            <div class="col-span-2 text-center py-10 text-slate-400 bg-slate-50 rounded-lg border border-dashed border-slate-300">
                Tidak ada data buku untuk dicetak.
            </div>
        @endforelse
    </div>
</div>

{{-- JsBarcode --}}
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script>
    JsBarcode(".barcode").init();
</script>

{{-- STYLE --}}
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&family=JetBrains+Mono:wght@500&display=swap');

    .book-card-wrapper{
        page-break-inside: avoid;
        break-inside: avoid;
        position: relative;
        margin-top: -2px;
        margin-bottom: -2px;
    }

    /* Kartu buku: persegi panjang, mirip kartu siswa */
    .book-card{
        width: 100%;
        aspect-ratio: 86/40;          /* 👉 lebih panjang & agak tipis, tidak kotak */
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 3px 4px -1px rgba(15,23,42,0.08);
        font-family: 'Inter', sans-serif;
        background: #ffffff;
        overflow: visible;           /* jangan potong barcode / teks */
    }

    .card-logo{
        width: 1.7rem;
        height: 1.7rem;
        border-radius: 999px;
        background:#fff;
        border:1px solid rgba(16,185,129,.35);
        overflow:hidden;
        display:flex;
        align-items:center;
        justify-content:center;
    }
    .card-logo.overlap{ margin-left:-0.4rem; }
    .card-logo img{ width:100%; height:100%; object-fit:contain; }

    .book-title{
        font-weight: 800;
        font-size: 0.78rem;
        line-height: 1.1;
        color:#0f172a;
        text-transform: uppercase;
        white-space: normal;
        word-break: break-word;
        hyphens: auto;
    }
    .book-title.long{
        font-size: 0.72rem;
    }

    .font-mono{ font-family: 'JetBrains Mono', monospace; }

    .book-code-text{
        font-size: 0.55rem;
        letter-spacing: 0.08em;
    }

    .cut-guide{
        position:absolute;
        top:-2px;left:-2px;right:-2px;bottom:-2px;
        border-radius:10px;
        pointer-events:none;
        z-index:0;
    }

    @media print {
        @page {
            margin: 8mm;
            size: A4;
        }

        /* Sembunyikan layout utama saat print → hanya kartu yang muncul */
        header, nav, aside, footer {
            display: none !important;
        }

        body{
            background:#fff;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .print\:hidden{ display:none !important; }

        #labelGrid{
            display:grid !important;
            grid-template-columns: 1fr 1fr !important;
            column-gap: 10px !important;
            row-gap: 4px !important;
        }

        .book-card{
            box-shadow:none;
            border:1px solid #cbd5e1;
        }

        .cut-guide{ display:none; }
    }
</style>
@endsection
