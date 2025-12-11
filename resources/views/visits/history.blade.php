@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <div class="mb-6 flex items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black text-slate-800 dark:text-white">Riwayat Kunjungan</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Laporan kunjungan perpustakaan berdasarkan rentang tanggal.
            </p>
        </div>
    </div>

    {{-- Filter tanggal --}}
    <form method="GET" class="mb-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Dari</label>
            <input type="date" name="from" value="{{ $from }}"
                   class="rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-sm px-3 py-2">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Sampai</label>
            <input type="date" name="to" value="{{ $to }}"
                   class="rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-sm px-3 py-2">
        </div>
        <button type="submit"
                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2">
            Tampilkan
        </button>
    </form>

    {{-- Ringkasan --}}
    <div class="mb-6 flex flex-wrap gap-2 text-xs">
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200">
            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
            Total kunjungan: <strong>{{ $count }}</strong>
        </span>
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-200">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            Kunjungan kelas: <strong>{{ $classVisitCount }}</strong> (± {{ $classStudentSum }} siswa)
        </span>
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-50 dark:bg-sky-900/40 text-sky-700 dark:text-sky-200">
            <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
            Kunjungan perorangan: <strong>{{ $individualVisitCount }}</strong>
        </span>
    </div>

    {{-- Tabel --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-slate-50 dark:bg-slate-900/40 text-xs uppercase text-slate-500 font-semibold">
                    <tr>
                        <th class="px-6 py-3 border-b border-slate-100 dark:border-slate-700">Waktu</th>
                        <th class="px-6 py-3 border-b border-slate-100 dark:border-slate-700">Siswa / Kelas</th>
                        <th class="px-6 py-3 border-b border-slate-100 dark:border-slate-700">Jenis</th>
                        <th class="px-6 py-3 border-b border-slate-100 dark:border-slate-700">Tujuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($rows as $v)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            {{-- Waktu --}}
                            <td class="px-6 py-3 text-slate-500 whitespace-nowrap">
                                {{ \Illuminate\Support\Carbon::parse($v->visited_at ?? $v->created_at)->format('Y-m-d H:i') }}
                            </td>

                            {{-- Siswa / Kelas --}}
                            <td class="px-6 py-3">
                                @if($v->class_name)
                                    <div class="font-medium text-slate-800 dark:text-slate-200">
                                        Kelas {{ $v->class_name }}
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ $v->student_count ?? 0 }} siswa (kunjungan kelas)
                                    </div>
                                @else
                                    <div class="font-medium text-slate-800 dark:text-slate-200 truncate max-w-[240px]">
                                        {{ $v->student_name ?? '-' }}
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        ID: {{ $v->student_id ?? '-' }} · {{ $v->student_class ?? '-' }}
                                    </div>
                                @endif
                            </td>

                            {{-- Jenis --}}
                            <td class="px-6 py-3">
                                @if($v->class_name)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                 bg-emerald-50 text-emerald-600 border border-emerald-200
                                                 dark:bg-emerald-900/30 dark:text-emerald-200 dark:border-emerald-800">
                                        Kunjungan Kelas
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                 bg-sky-50 text-sky-600 border border-sky-200
                                                 dark:bg-sky-900/30 dark:text-sky-200 dark:border-sky-800">
                                        Perorangan
                                    </span>
                                @endif
                            </td>

                            {{-- Tujuan --}}
                            <td class="px-6 py-3">
                                @php $purpose = $v->purpose ?? '—'; @endphp

                                @if($purpose === 'baca')
                                    <span class="text-xs px-2 py-1 rounded-full bg-sky-50 text-sky-600 border border-sky-200 dark:bg-sky-900/30 dark:text-sky-200 dark:border-sky-800">
                                        baca
                                    </span>
                                @elseif($purpose === 'pinjam')
                                    <span class="text-xs px-2 py-1 rounded-full bg-indigo-50 text-indigo-600 border border-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-200 dark:border-indigo-800">
                                        pinjam
                                    </span>
                                @elseif($purpose === 'kembali')
                                    <span class="text-xs px-2 py-1 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-200 dark:border-emerald-800">
                                        kembali
                                    </span>
                                @elseif($purpose === 'tugas')
                                    <span class="text-xs px-2 py-1 rounded-full bg-amber-50 text-amber-600 border border-amber-200 dark:bg-amber-900/30 dark:text-amber-200 dark:border-amber-800">
                                        tugas
                                    </span>
                                @elseif($purpose === 'kunjungan_kelas')
                                    <span class="text-xs px-2 py-1 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-200 dark:border-emerald-800">
                                        kunjungan kelas
                                    </span>
                                @elseif($purpose === 'kartu_rusak')
                                    <span class="text-xs px-2 py-1 rounded-full bg-rose-50 text-rose-500 border border-rose-200 dark:bg-rose-900/30 dark:text-rose-200 dark:border-rose-800">
                                        kartu rusak
                                    </span>
                                @else
                                    <span class="text-xs px-2 py-1 rounded-full bg-slate-50 text-slate-500 border border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700">
                                        {{ $purpose }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-slate-400">
                                Tidak ada kunjungan pada rentang tanggal ini.
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
