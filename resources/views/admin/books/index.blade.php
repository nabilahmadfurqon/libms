{{-- resources/views/admin/books/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- HEADER + SEARCH --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-800 dark:text-white">
                Data Buku
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Kelola koleksi perpustakaan. Pilih input
                <span class="font-semibold text-slate-800 dark:text-slate-100">Manual</span>
                atau
                <span class="font-semibold text-slate-800 dark:text-slate-100">Impor CSV</span>.
            </p>
        </div>

        <form method="get" class="flex items-center gap-2">
            <input type="search" name="q" value="{{ $q }}" placeholder="Cari judul / penulis / ID…"
                class="w-52 md:w-64 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 px-3 py-2 text-sm text-slate-700 dark:text-slate-200 placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">

            <button
                class="inline-flex items-center justify-center rounded-lg px-3 py-2 text-sm font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-100 border border-slate-200 dark:border-slate-700 transition">
                Cari
            </button>

            {{-- Tombol Export CSV sesuai hasil pencarian --}}
            <a href="{{ route('admin.books.export', ['q' => $q]) }}"
   class="px-3 py-2 rounded-lg text-sm font-medium bg-indigo-600 text-white hover:bg-indigo-700">
   Export CSV
</a>
        </form>
    </div>

    {{-- FLASH --}}
    @if(session('ok'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 flex items-center gap-3 text-emerald-800 dark:text-emerald-200">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-medium text-sm">{{ session('ok') }}</span>
        </div>
    @endif

    @if(session('err'))
        <div class="mb-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-800 flex items-center gap-3 text-rose-800 dark:text-rose-200">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-medium text-sm">{{ session('err') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

        {{-- ============= TABEL BUKU + TOOLBAR BARCODE ============= --}}
        <section class="xl:col-span-8">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-4 md:p-6 h-full flex flex-col">

                {{-- Toolbar Barcode --}}
                <form id="barcodeForm" method="POST" action="{{ route('admin.barcodes.books.preview') }}" target="_blank" class="mb-4 flex flex-wrap items-center gap-2">
                    @csrf
                    <button id="btnPrintSelected" type="submit"
                        class="inline-flex items-center rounded-lg px-3 py-2 text-xs sm:text-sm font-medium
                               bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-40 disabled:cursor-not-allowed transition"
                        disabled>
                        Cetak Barcode (dipilih)
                    </button>

                    <a href="{{ route('admin.barcodes.books') }}{{ $q ? ('?q='.urlencode($q)) : '' }}"
                       target="_blank"
                       class="inline-flex items-center rounded-lg px-3 py-2 text-xs sm:text-sm font-medium
                              bg-slate-100 hover:bg-slate-200 text-slate-700
                              dark:bg-slate-700 dark:hover:bg-slate-600 dark:text-slate-100
                              border border-slate-200 dark:border-slate-600 transition">
                        Cetak Semua Hasil Pencarian
                    </a>

                    <span class="ml-auto text-xs text-slate-500 dark:text-slate-400">
                        Tips: centang beberapa baris lalu klik “Cetak Barcode (dipilih)”
                    </span>
                </form>

                <div class="flex-1 overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-900/50 text-xs uppercase text-slate-500 font-semibold">
                            <tr>
                                <th class="px-4 py-3 border-b border-slate-100 dark:border-slate-700">
                                    <input id="checkAll" type="checkbox" class="accent-indigo-600">
                                </th>
                                <th class="px-4 py-3 border-b border-slate-100 dark:border-slate-700">Book ID</th>
                                <th class="px-4 py-3 border-b border-slate-100 dark:border-slate-700">Judul</th>
                                <th class="px-4 py-3 border-b border-slate-100 dark:border-slate-700">Author</th>
                                <th class="px-4 py-3 border-b border-slate-100 dark:border-slate-700">Kategori</th>
                                <th class="px-4 py-3 border-b border-slate-100 dark:border-slate-700">ISBN</th>
                                <th class="px-4 py-3 border-b border-slate-100 dark:border-slate-700">Total</th>
                                <th class="px-4 py-3 border-b border-slate-100 dark:border-slate-700">Tersedia</th>
                                <th class="px-4 py-3 border-b border-slate-100 dark:border-slate-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse($books as $b)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                    <td class="px-4 py-2 align-top">
                                        <input type="checkbox" name="ids[]" value="{{ $b->book_id }}" class="row-check accent-indigo-600">
                                    </td>
                                    <td class="px-4 py-2 align-top text-slate-700 dark:text-slate-200 whitespace-nowrap">
                                        {{ $b->book_id }}
                                    </td>
                                    <td class="px-4 py-2 align-top text-slate-800 dark:text-slate-100">
                                        <div class="font-medium truncate max-w-[220px]" title="{{ $b->title }}">
                                            {{ $b->title }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 align-top text-slate-600 dark:text-slate-300">
                                        {{ $b->author }}
                                    </td>
                                    <td class="px-4 py-2 align-top text-slate-600 dark:text-slate-300">
                                        {{ $b->category }}
                                    </td>
                                    <td class="px-4 py-2 align-top text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                        {{ $b->isbn }}
                                    </td>
                                    <td class="px-4 py-2 align-top text-slate-700 dark:text-slate-200">
                                        {{ $b->total_copies }}
                                    </td>
                                    <td class="px-4 py-2 align-top text-slate-700 dark:text-slate-200">
                                        {{ $b->available_copies }}
                                    </td>
                                    <td class="px-4 py-2 align-top text-right whitespace-nowrap">
                                        <a href="{{ route('admin.books.edit',$b) }}"
                                           class="text-indigo-600 dark:text-indigo-400 hover:underline mr-3 text-xs font-medium">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.books.destroy',$b) }}" method="post" class="inline"
                                              onsubmit="return confirm('Hapus buku?')">
                                            @csrf @method('delete')
                                            <button type="submit"
                                                class="text-rose-600 dark:text-rose-400 hover:underline text-xs font-medium">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-slate-400 text-sm italic">
                                        Belum ada data buku.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-4 border-t border-slate-100 dark:border-slate-700 pt-3">
                    {{ $books->links('components.pagination') }}
                </div>
            </div>
        </section>

        {{-- ============= PANEL KANAN: MANUAL / IMPOR ============= --}}
        <section class="xl:col-span-4">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-4 md:p-6 h-full relative overflow-hidden">

                {{-- Hiasan --}}
                <div class="absolute -top-6 -right-6 w-28 h-28 rounded-full bg-indigo-50 dark:bg-indigo-900/10 pointer-events-none"></div>

                {{-- Tabs --}}
                <div class="mb-4 relative z-10">
                    <div class="inline-flex items-center gap-1 rounded-full bg-slate-100 dark:bg-slate-900/60 px-1 py-1 ring-1 ring-slate-200 dark:ring-slate-700">
                        <button id="tabManual"
                            class="text-xs sm:text-sm font-bold flex items-center gap-2 px-3 py-1.5 rounded-full transition-all duration-200 text-slate-500 hover:text-slate-700 dark:text-slate-400">
                            ✍️ Manual
                        </button>
                        <button id="tabImport"
                            class="text-xs sm:text-sm font-bold flex items-center gap-2 px-3 py-1.5 rounded-full transition-all duration-200 bg-white text-indigo-600 shadow-sm ring-1 ring-slate-200 dark:bg-slate-700 dark:text-white dark:ring-white/10">
                            📁 Impor CSV
                        </button>
                    </div>
                </div>

                {{-- FORM MANUAL --}}
                <div id="panelManual" class="hidden relative z-10">
                    <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100 mb-3">
                        Tambah Buku Manual
                    </h2>
                    <form method="post" action="{{ route('admin.books.store') }}" class="grid grid-cols-1 gap-3">
                        @csrf
                        <div>
                            <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Book ID</label>
                            <input name="book_id"
                                class="mt-1 w-full rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Judul</label>
                            <input name="title"
                                class="mt-1 w-full rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Author</label>
                                <input name="author"
                                    class="mt-1 w-full rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Kategori</label>
                                <input name="category"
                                    class="mt-1 w-full rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">ISBN</label>
                                <input name="isbn"
                                    class="mt-1 w-full rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Barcode</label>
                                <input name="barcode"
                                    class="mt-1 w-full rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total</label>
                                <input type="number" min="0" name="total_copies" value="0"
                                    class="mt-1 w-full rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Tersedia</label>
                                <input type="number" min="0" name="available_copies" value="0"
                                    class="mt-1 w-full rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>

                        <div class="pt-2">
                            <button
                                class="inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm shadow-indigo-200 dark:shadow-none transition">
                                Simpan Manual
                            </button>
                        </div>
                    </form>
                </div>

                {{-- FORM IMPOR --}}
                <div id="panelImport" class="relative z-10">
                    <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100 mb-3">
                        Impor Buku via CSV
                    </h2>

                    <div class="mb-3 text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                        Header wajib (urutan kolom):
                        <div class="mt-1 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 px-3 py-2 overflow-x-auto">
                            <code class="whitespace-nowrap select-all text-xs">
                                book_id,title,author,category,isbn,barcode,total_copies,available_copies
                            </code>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 mb-4">
                        <a href="{{ route('admin.books.template') }}"
                           class="inline-flex items-center rounded-lg px-3 py-2 text-xs sm:text-sm font-medium bg-slate-100 hover:bg-slate-200 text-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-100 border border-slate-200 dark:border-slate-700 transition">
                            Download Template
                        </a>
                        <a href="{{ route('admin.books.sample') }}"
                           class="inline-flex items-center rounded-lg px-3 py-2 text-xs sm:text-sm font-medium bg-slate-100 hover:bg-slate-200 text-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-100 border border-slate-200 dark:border-slate-700 transition">
                            Download Contoh
                        </a>
                    </div>

                    <form method="POST" action="{{ route('admin.books.import') }}" enctype="multipart/form-data"
                          class="flex flex-wrap items-center gap-3">
                        @csrf
                        <input type="file" name="file" accept=".csv"
                               class="text-xs sm:text-sm text-slate-700 dark:text-slate-200">
                        <button
                            class="inline-flex items-center justify-center rounded-lg px-4 py-2 text-xs sm:text-sm font-medium bg-amber-600 hover:bg-amber-700 text-white shadow-sm transition">
                            Impor CSV
                        </button>
                    </form>
                </div>

            </div>
        </section>
    </div>
</div>

<script>
(() => {
    // Tabs
    const tabManual  = document.getElementById('tabManual');
    const tabImport  = document.getElementById('tabImport');
    const panelManual = document.getElementById('panelManual');
    const panelImport = document.getElementById('panelImport');

    const activeClasses = ['bg-white','text-indigo-600','shadow-sm','ring-1','ring-slate-200','dark:bg-slate-700','dark:text-white','dark:ring-white/10'];
    const inactiveClasses = ['text-slate-500','hover:text-slate-700','dark:text-slate-400'];

    function setTab(which) {
        const manual = which === 'manual';
        panelManual.classList.toggle('hidden', !manual);
        panelImport.classList.toggle('hidden', manual);
        if (manual) {
            tabManual.classList.add(...activeClasses);
            tabManual.classList.remove(...inactiveClasses);
            tabImport.classList.remove(...activeClasses);
            tabImport.classList.add(...inactiveClasses);
        } else {
            tabImport.classList.add(...activeClasses);
            tabImport.classList.remove(...inactiveClasses);
            tabManual.classList.remove(...activeClasses);
            tabManual.classList.add(...inactiveClasses);
        }
    }
    setTab('import');
    tabManual?.addEventListener('click', () => setTab('manual'));
    tabImport?.addEventListener('click', () => setTab('import'));

    // ===== Checkbox & tombol Cetak (dipilih)
    const checkAll = document.getElementById('checkAll');
    const rowChecks = Array.from(document.querySelectorAll('.row-check'));
    const btnPrint = document.getElementById('btnPrintSelected');

    function refreshButton() {
        const any = rowChecks.some(c => c.checked);
        btnPrint.disabled = !any;
    }
    checkAll?.addEventListener('change', e => {
        rowChecks.forEach(c => c.checked = e.target.checked);
        refreshButton();
    });
    rowChecks.forEach(c => c.addEventListener('change', refreshButton));
    refreshButton();
})();
</script>
@endsection
