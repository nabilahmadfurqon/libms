@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-10 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 p-6">
    <h1 class="text-lg font-bold text-slate-900 dark:text-white mb-4">
        Konfirmasi Logout Kiosk
    </h1>

    <p class="text-sm text-slate-600 dark:text-slate-300 mb-4">
        Masukkan <span class="font-semibold">password akun pengunjung ini</span> untuk logout dari mode kiosk.
        <br>
        <span class="text-xs text-slate-500">
            Hanya guru/petugas yang mengetahui password ini.
        </span>
    </p>

    @if($errors->any())
        <div class="mb-4 text-sm text-rose-600 dark:text-rose-400">
            {{ $errors->first('password') }}
        </div>
    @endif

    <form method="POST" action="{{ route('pengunjung.logout.confirm') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Password
            </label>
            <input
                type="password"
                name="password"
                required
                autofocus
                class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            >
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('pengunjung.dashboard') }}"
               class="px-3 py-2 text-sm rounded-lg border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-200">
                Batal
            </a>
            <button
                type="submit"
                class="px-4 py-2 text-sm font-semibold rounded-lg bg-rose-600 hover:bg-rose-700 text-white">
                Logout
            </button>
        </div>
    </form>
</div>
@endsection
