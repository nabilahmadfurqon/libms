@extends('layouts.app')

@section('content')
  <h1 class="text-2xl font-semibold mb-1">{{ $student->exists ? 'Edit Siswa' : 'Tambah Siswa' }}</h1>
  <p class="text-slate-600 dark:text-slate-400 mb-4">Kolom wajib: Student ID & Nama.</p>

  <form method="post" action="{{ $student->exists ? route('admin.students.update',$student) : route('admin.students.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @csrf
    @if($student->exists) @method('put') @endif

    <div>
      <label class="text-sm">Student ID</label>
      <input name="student_id" value="{{ old('student_id',$student->student_id) }}" class="mt-1 w-full rounded-xl bg-white/80 dark:bg-slate-900/70 border border-slate-200/70 dark:border-white/10 px-3 py-2">
      @error('student_id')<div class="text-xs text-rose-500 mt-1">{{ $message }}</div>@enderror
    </div>

    <div>
      <label class="text-sm">Nama</label>
      <input name="name" value="{{ old('name',$student->name) }}" class="mt-1 w-full rounded-xl bg-white/80 dark:bg-slate-900/70 border border-slate-200/70 dark:border-white/10 px-3 py-2">
      @error('name')<div class="text-xs text-rose-500 mt-1">{{ $message }}</div>@enderror
    </div>

    <div>
      <label class="text-sm">Grade</label>
      <input name="grade" value="{{ old('grade',$student->grade) }}" class="mt-1 w-full rounded-xl bg-white/80 dark:bg-slate-900/70 border border-slate-200/70 dark:border-white/10 px-3 py-2">
    </div>

    <div>
      <label class="text-sm">Kelas</label>
      <input name="kelas" value="{{ old('kelas',$student->kelas) }}" class="mt-1 w-full rounded-xl bg-white/80 dark:bg-slate-900/70 border border-slate-200/70 dark:border-white/10 px-3 py-2">
    </div>

    <div>
      <label class="text-sm">Status</label>
      <select name="status" class="mt-1 w-full rounded-xl bg-white/80 dark:bg-slate-900/70 border border-slate-200/70 dark:border-white/10 px-3 py-2">
        <option value="aktif" {{ old('status',$student->status)=='aktif'?'selected':'' }}>Aktif</option>
        <option value="nonaktif" {{ old('status',$student->status)=='nonaktif'?'selected':'' }}>Nonaktif</option>
      </select>
      @error('status')<div class="text-xs text-rose-500 mt-1">{{ $message }}</div>@enderror
    </div>

    <div>
      <label class="text-sm">Barcode</label>
      <input name="barcode" value="{{ old('barcode',$student->barcode) }}" class="mt-1 w-full rounded-xl bg-white/80 dark:bg-slate-900/70 border border-slate-200/70 dark:border-white/10 px-3 py-2">
    </div>

    <div class="md:col-span-2 flex items-center gap-2">
      <a href="{{ route('admin.students.index') }}" class="rounded-xl px-3 py-2 text-sm bg-slate-900/10 dark:bg-white/10 hover:bg-slate-900/20 dark:hover:bg-white/20 transition">Batal</a>
      <button class="rounded-xl px-3 py-2 text-sm bg-indigo-600 text-white hover:bg-indigo-500 transition">{{ $student->exists ? 'Simpan' : 'Buat' }}</button>
    </div>
  </form>
@endsection
