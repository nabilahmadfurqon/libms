@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Manajemen User</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Kelola admin dan petugas perpustakaan.
            </p>
        </div>

        <a href="{{ route('admin.users.create') }}"
           class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm hover:bg-indigo-700 flex items-center gap-2">
            Tambah User
        </a>
    </div>

    {{-- Alert --}}
    @if(session('ok'))
        <div class="mb-4 bg-emerald-50 border border-emerald-300 text-emerald-700 px-3 py-2 rounded-lg">
            {{ session('ok') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-rose-50 border border-rose-300 text-rose-700 px-3 py-2 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filter --}}
    <form class="flex flex-wrap items-center gap-3 mb-4">
        <input type="text" name="q" value="{{ $q }}"
               placeholder="Cari nama atau email..."
               class="px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800">

        <select name="role"
                class="px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800">
            <option value="">Semua Role</option>
            <option value="admin" @selected($role === 'admin')>Admin</option>
            <option value="petugas" @selected($role === 'petugas')>Petugas</option>
        </select>

        <select name="active"
                class="px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800">
            <option value="">Status</option>
            <option value="1" @selected($active === '1' || $active === 1)>Aktif</option>
            <option value="0" @selected($active === '0' || $active === 0)>Nonaktif</option>
        </select>

        <button class="px-4 py-2 bg-slate-800 text-white rounded-xl text-sm">
            Filter
        </button>
    </form>

    {{-- Tabel --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow border border-slate-200 dark:border-slate-700 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="border-b border-slate-200 dark:border-slate-700">
                <tr class="text-left text-slate-500 dark:text-slate-400">
                    <th class="py-3 px-4">Nama</th>
                    <th class="py-3 px-4">Email</th>
                    <th class="py-3 px-4">Role</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr class="border-b border-slate-100 dark:border-slate-700/50 hover:bg-slate-50 dark:hover:bg-slate-700/30">
                    <td class="py-2 px-4">{{ $user->name }}</td>
                    <td class="py-2 px-4">{{ $user->email }}</td>
                    <td class="py-2 px-4">
                        @if($user->role === 'admin')
                            <span class="px-2 py-1 rounded-full text-xs bg-indigo-100 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300">Admin</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-300">Petugas</span>
                        @endif
                    </td>
                    <td class="py-2 px-4">
                        @if($user->active)
                            <span class="px-2 py-1 rounded-full text-xs bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-300">Aktif</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs bg-slate-300 dark:bg-slate-700 text-slate-700 dark:text-slate-200">Nonaktif</span>
                        @endif
                    </td>
                    <td class="py-2 px-4 text-right">
                        <a href="{{ route('admin.users.edit', $user) }}"
                           class="px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-700 text-xs text-slate-700 dark:text-slate-200">
                            Edit
                        </a>

                        @if(auth()->id() !== $user->id)
                        <form action="{{ route('admin.users.destroy', $user) }}"
                              method="POST"
                              class="inline"
                              onsubmit="return confirm('Hapus user ini?');">
                            @csrf
                            @method('DELETE')
                            <button class="px-2 py-1 rounded-lg bg-rose-100 dark:bg-rose-500/10 text-xs text-rose-700 dark:text-rose-300">
                                Hapus
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-6 text-center text-slate-400">Tidak ada user.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-slate-100 dark:border-slate-700/50">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
