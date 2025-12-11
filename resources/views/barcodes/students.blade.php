@extends('layouts.app')

@section('content')
<div class="max-w-[297mm] mx-auto px-4 py-6 print:p-0 print:m-0 print:w-full">

  {{-- TOOLBAR (Tidak ikut di-print) --}}
  <div class="mb-6 print:hidden flex flex-wrap items-center justify-between gap-4 bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
    <div>
      <h1 class="text-2xl font-black text-slate-800">Cetak Kartu LibMS</h1>
      <p class="text-slate-500 text-sm mt-1">
        SDI Al Azhar Rawamangun 13 | Total: {{ $items->count() }} Siswa
      </p>
    </div>
    <div class="flex items-center gap-3">
      <a href="{{ route('admin.students.index') }}" class="text-slate-600 hover:text-slate-900 font-medium text-sm">
        Batal
      </a>
      <button onclick="window.print()" class="flex items-center gap-2 px-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-lg shadow-emerald-200 transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        Cetak Sekarang
      </button>
    </div>
  </div>

  {{-- GRID KARTU (fix 2 kolom di desktop/print) --}}
  <div id="cardGrid" class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @forelse($items as $it)
      @php
        $code = $it->barcode ?: $it->student_id;
      @endphp

      {{-- WRAPPER KARTU --}}
      <div class="id-card-wrapper relative">

        {{-- DESAIN KARTU (Landscape) --}}
        <div class="id-card bg-white relative flex flex-col justify-between">
            {{-- BACKGROUND PATTERN --}}
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-emerald-100 rounded-full opacity-60"></div>
            <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-sky-100 rounded-full opacity-50"></div>

            {{-- HEADER --}}
            <div class="relative z-10 flex items-center justify-between gap-3 px-5 pt-2.5 pb-1.5 border-b border-emerald-100">
                <div class="flex items-center gap-2">
                    {{-- Logo YAPI --}}
                    <div class="w-9 h-9 rounded-full bg-white/90 border border-emerald-100 flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('assets/logo-yapi.png') }}"
                             class="w-full h-full object-contain"
                             alt="Logo YAPI">
                    </div>
                    {{-- Logo Al Azhar --}}
                    <div class="w-9 h-9 -ml-2 rounded-full bg-white/90 border border-emerald-100 flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('assets/logo-alazhar13.png') }}"
                             class="w-full h-full object-contain"
                             alt="Logo Al Azhar 13">
                    </div>
                </div>

                <div class="flex-grow text-right">
                    <h2 class="text-[8px] font-semibold text-emerald-900 tracking-[0.12em] uppercase">
                        Yayasan Pesantren Islam Al Azhar
                    </h2>
                    <h1 class="text-[10px] font-black text-emerald-900 leading-tight tracking-wide">
                        SDI AL AZHAR RAWAMANGUN 13
                    </h1>
                    <p class="text-[8px] text-emerald-700 font-medium mt-0.5">
                        Kartu Anggota Perpustakaan
                    </p>
                </div>
            </div>

            {{-- KONTEN UTAMA --}}
            <div class="relative z-10 px-5 py-2 flex-grow flex flex-col justify-center">
                <div class="mb-1">
                    <span class="block text-[8px] uppercase tracking-[0.16em] text-slate-400 font-semibold">
                        Nama Siswa
                    </span>
                    <h3 class="text-[15px] font-extrabold text-slate-900 uppercase leading-tight tracking-tight truncate">
                        {{ $it->name }}
                    </h3>
                </div>

                <div class="flex gap-6 mt-1 text-[9px]">
                    <div>
                        <span class="block uppercase tracking-[0.16em] text-slate-400 font-semibold">
                            Nomor Induk
                        </span>
                        <span class="text-[11px] font-bold text-emerald-700 font-mono">
                            {{ $it->student_id }}
                        </span>
                    </div>
                    <div>
                        <span class="block uppercase tracking-[0.16em] text-slate-400 font-semibold">
                            Kelas
                        </span>
                        <span class="text-[11px] font-bold text-slate-700">
                            {{ $it->kelas ?? $it->grade }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- AREA BARCODE (lebih pendek, tetap jelas) --}}
            <div class="relative z-10 bg-white px-4 pb-2.5 pt-1.5 flex flex-col items-center justify-center border-t border-slate-100">
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

                <div class="mt-1 text-[9px] font-mono text-slate-600 tracking-[0.16em] whitespace-nowrap">
                    {{ $code }}
                </div>
            </div>

            {{-- STRIP SAMPING --}}
            <div class="absolute left-0 top-3 bottom-3 w-1.5 bg-gradient-to-b from-emerald-600 via-sky-500 to-emerald-500 rounded-r-md"></div>
        </div>

        <div class="cut-guide border-dashed border-slate-300"></div>
      </div>

    @empty
      <div class="col-span-2 text-center py-10 text-slate-400 bg-slate-50 rounded-lg border border-dashed border-slate-300">
        Tidak ada data siswa untuk dicetak.
      </div>
    @endforelse
  </div>
</div>

{{-- SCRIPTS --}}
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script>
  JsBarcode(".barcode").init();
</script>

{{-- STYLES --}}
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&family=JetBrains+Mono:wght@500&display=swap');

    .id-card-wrapper {
        page-break-inside: avoid;
        break-inside: avoid;
        position: relative;
        margin-top: -2px;
        margin-bottom: -2px;
    }

    /* Kartu landscape, lebih PERSEGI PANJANG (tinggi lebih pendek) */
    .id-card {
        width: 100%;
        aspect-ratio: 86/40;      /* <--- di sini: semaking panjang, tidak kotak */
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.08);
        font-family: 'Inter', sans-serif;
        background-color: #ffffff;
        overflow: visible;        /* jangan memotong barcode / teks */
    }

    .font-mono { font-family: 'JetBrains Mono', monospace; }

    .cut-guide {
        position: absolute;
        top: -2px; left: -2px; right: -2px; bottom: -2px;
        border-width: 1px;
        border-radius: 12px;
        pointer-events: none;
        z-index: 0;
    }

    @media print {
        @page {
            margin: 8mm;
            size: A4;
        }

        /* SEMUA NAV/HEADER/ASIDE DISEMBUNYIKAN SAAT PRINT */
        header, nav, aside, footer {
            display: none !important;
        }

        body {
            background: white;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .print\:hidden { display: none !important; }

        #cardGrid {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 8px !important;
        }

        .id-card {
            border: 1px solid #cbd5e1;
            box-shadow: none;
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .cut-guide { display: none; }
    }
</style>
@endsection
