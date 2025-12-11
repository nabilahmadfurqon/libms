@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100 mb-4">
        {{ $title }}
    </h1>

    @if ($errors->any())
        <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc pl-4">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $action }}" class="space-y-4">
        @csrf
        @if($method !== 'POST')
            @method($method)
        @endif

        {{-- Nama --}}
        <div>
            <label class="block text-sm text-slate-600 dark:text-slate-300">Nama</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                   class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                   required>
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-sm text-slate-600 dark:text-slate-300">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                   class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                   required>
        </div>

        {{-- Role --}}
        <div>
            <label class="block text-sm text-slate-600 dark:text-slate-300">Role</label>
            <select name="role"
                class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800" required>
                <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                <option value="petugas" @selected(old('role', $user->role) === 'petugas')>Petugas</option>
            </select>
        </div>

        {{-- Status --}}
        <div class="flex items-center gap-2">
            <input type="checkbox" name="active" id="active" value="1"
                   @checked(old('active', $user->active ?? true))>
            <label for="active" class="text-sm text-slate-600 dark:text-slate-300">
                Aktif
            </label>
        </div>

        {{-- Password --}}
        <div>
            <label class="block text-sm text-slate-600 dark:text-slate-300">Password</label>
            <input type="password" name="password"
                   class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                   @if($mode === 'create') required @endif>

            @if($mode === 'edit')
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Kosongkan jika tidak ingin mengubah password.
                </p>
            @endif
        </div>

        <div class="flex justify-between items-center pt-4">
            <a href="{{ route('admin.users.index') }}"
               class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                Batal
            </a>
            <button class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">
                Simpan
            </button>
        </div>
    </form>

</div>
@endsection
