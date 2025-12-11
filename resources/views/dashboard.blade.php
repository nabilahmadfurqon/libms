@extends('layouts.app')

@section('content')
  <div class="max-w-7xl mx-auto px-6">

    <div class="mb-6">
      <h1 class="text-2xl font-semibold">Dashboard</h1>
      <p class="text-slate-600 dark:text-slate-400">Halo, {{ $user->name }} 👋</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      @foreach ($cards as $c)
        <div class="rounded-2xl bg-white/85 dark:bg-slate-900/80 backdrop-blur-xl
                    ring-1 ring-slate-200/70 dark:ring-white/10 p-5 shadow">
          <div class="text-sm text-slate-600 dark:text-slate-400">{{ $c['label'] }}</div>
          <div class="mt-2 text-2xl font-semibold">{{ $c['value'] }}</div>
        </div>
      @endforeach
    </div>

    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
      <a href="{{ route('circulation.index') }}"
         class="rounded-2xl bg-white/85 dark:bg-slate-900/80 backdrop-blur-xl
                ring-1 ring-slate-200/70 dark:ring-white/10 p-5 shadow hover:shadow-md transition">
        <div class="text-lg font-semibold">Peminjaman</div>
        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Transaksi pinjam/kembali</p>
      </a>
      <a href="{{ route('visits.index') }}"
         class="rounded-2xl bg-white/85 dark:bg-slate-900/80 backdrop-blur-xl
                ring-1 ring-slate-200/70 dark:ring-white/10 p-5 shadow hover:shadow-md transition">
        <div class="text-lg font-semibold">Kunjungan</div>
        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Check-in/out pengunjung</p>
      </a>
      @if($user->isAdmin())
      <a href="{{ route('admin.reports') }}"
         class="rounded-2xl bg-white/85 dark:bg-slate-900/80 backdrop-blur-xl
                ring-1 ring-slate-200/70 dark:ring-white/10 p-5 shadow hover:shadow-md transition">
        <div class="text-lg font-semibold">Laporan</div>
        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Rekap & insight</p>
      </a>
      @endif
    </div>

  </div>
@endsection
