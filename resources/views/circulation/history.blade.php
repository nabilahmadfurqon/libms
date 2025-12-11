@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- HEADER + RINGKASAN --}}
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black text-slate-800 dark:text-white">
                Riwayat Sirkulasi
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Periode: {{ $from }} s/d {{ $to }}
            </p>
        </div>

        {{-- FILTER FORM --}}
        <form method="GET" class="flex flex-wrap items-end gap-2">
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Tipe</label>
                <select name="type"
                        class="rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-sm px-2.5 py-1.5">
                    <option value="loans"   {{ $type=='loans'?'selected':'' }}>Pinjam</option>
                    <option value="returns" {{ $type=='returns'?'selected':'' }}>Kembali</option>
                    <option value="overdue" {{ $type=='overdue'?'selected':'' }}>Overdue (aktif)</option>
                    <option value="all"     {{ $type=='all'?'selected':'' }}>Semua (pinjam)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Dari</label>
                <input type="date" name="from" value="{{ $from }}"
                       class="rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-sm px-2.5 py-1.5">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Sampai</label>
                <input type="date" name="to" value="{{ $to }}"
                       class="rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-sm px-2.5 py-1.5">
            </div>

            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 mt-4 md:mt-0">
                Terapkan
            </button>
        </form>
    </div>

    {{-- BADGE RINGKASAN --}}
    <div class="mb-5 flex flex-wrap gap-2 text-xs">
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-50 dark:bg-sky-900/40 text-sky-700 dark:text-sky-200">
            <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
            Pinjam: <strong>{{ $stats['loans'] }}</strong>
        </span>
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-200">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            Kembali: <strong>{{ $stats['returns'] }}</strong>
        </span>
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-rose-50 dark:bg-rose-900/40 text-rose-700 dark:text-rose-200">
            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
            Overdue aktif: <strong>{{ $stats['overdue'] }}</strong>
        </span>
    </div>

    {{-- CARD TABEL --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-slate-50 dark:bg-slate-900/40 text-xs uppercase text-slate-500 font-semibold">
                    <tr>
                        <th class="px-6 py-3 border-b border-slate-100 dark:border-slate-700">Waktu</th>
                        <th class="px-6 py-3 border-b border-slate-100 dark:border-slate-700">Siswa</th>
                        <th class="px-6 py-3 border-b border-slate-100 dark:border-slate-700">Buku</th>
                        <th class="px-6 py-3 border-b border-slate-100 dark:border-slate-700">Jatuh Tempo</th>
                        <th class="px-6 py-3 border-b border-slate-100 dark:border-slate-700">Kembali</th>
                        <th class="px-6 py-3 border-b border-slate-100 dark:border-slate-700">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($rows as $r)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            {{-- WAKTU --}}
                            <td class="px-6 py-3 text-slate-500 whitespace-nowrap">
                                @if($type === 'returns')
                                    {{ $r->returned_at ? \Carbon\Carbon::parse($r->returned_at)->format('d/m H:i') : '—' }}
                                @elseif($type === 'overdue')
                                    {{ $r->due_at ? \Carbon\Carbon::parse($r->due_at)->format('d/m') : '—' }}
                                @else
                                    {{ $r->created_at ? \Carbon\Carbon::parse($r->created_at)->format('d/m H:i') : '—' }}
                                @endif
                            </td>

                            {{-- SISWA --}}
                            <td class="px-6 py-3">
                                <div class="font-medium text-slate-800 dark:text-slate-200 truncate max-w-[220px]">
                                    {{ $r->student_name ?? '-' }}
                                </div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $r->student_id ?? '-' }}
                                </div>
                            </td>

                            {{-- BUKU --}}
                            <td class="px-6 py-3">
                                <div class="font-medium text-slate-800 dark:text-slate-200 truncate max-w-[260px]" title="{{ $r->book_title }}">
                                    {{ $r->book_title ?? '-' }}
                                </div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $r->book_id ?? '-' }}
                                </div>
                            </td>

                            {{-- JATUH TEMPO --}}
                            <td class="px-6 py-3 text-slate-600 dark:text-slate-300">
                                {{ $r->due_at ? \Carbon\Carbon::parse($r->due_at)->format('d/m') : '—' }}
                            </td>

                            {{-- TANGGAL KEMBALI --}}
                            <td class="px-6 py-3 text-slate-600 dark:text-slate-300">
                                {{ $r->returned_at ? \Carbon\Carbon::parse($r->returned_at)->format('d/m') : '—' }}
                            </td>

                            {{-- STATUS --}}
                            <td class="px-6 py-3">
                                @if($r->returned_at)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                 bg-emerald-50 text-emerald-700 border border-emerald-200
                                                 dark:bg-emerald-900/30 dark:text-emerald-200 dark:border-emerald-800">
                                        kembali
                                    </span>
                                @elseif($r->due_at && \Carbon\Carbon::parse($r->due_at)->isPast())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                 bg-rose-50 text-rose-700 border border-rose-200
                                                 dark:bg-rose-900/30 dark:text-rose-200 dark:border-rose-800">
                                        overdue
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                 bg-indigo-50 text-indigo-700 border border-indigo-200
                                                 dark:bg-indigo-900/30 dark:text-indigo-200 dark:border-indigo-800">
                                        pinjam
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-slate-400">
                                Tidak ada data pada rentang ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rows->hasPages())
            <div class="px-4 py-3 border-t border-slate-100 dark:border-slate-700">
                {{ $rows->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
