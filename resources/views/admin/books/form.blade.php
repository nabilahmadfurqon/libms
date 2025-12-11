@extends('layouts.app')

@section('content')
  <h1 class="text-2xl font-semibold mb-1">{{ $book->exists ? 'Edit Buku' : 'Tambah Buku' }}</h1>
  <p class="text-slate-600 dark:text-slate-400 mb-4">Kolom wajib: Book ID & Title.</p>

  <form method="post" action="{{ $book->exists ? route('admin.books.update',$book) : route('admin.books.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @csrf
    @if($book->exists) @method('put') @endif

    <div>
      <label class="text-sm">Book ID</label>
      <input name="book_id" value="{{ old('book_id',$book->book_id) }}" class="mt-1 w-full rounded-xl bg-white/80 dark:bg-slate-900/70 border border-slate-200/70 dark:border-white/10 px-3 py-2">
      @error('book_id')<div class="text-xs text-rose-500 mt-1">{{ $message }}</div>@enderror
    </div>

    <div>
      <label class="text-sm">Judul</label>
      <input name="title" value="{{ old('title',$book->title) }}" class="mt-1 w-full rounded-xl bg-white/80 dark:bg-slate-900/70 border border-slate-200/70 dark:border-white/10 px-3 py-2">
      @error('title')<div class="text-xs text-rose-500 mt-1">{{ $message }}</div>@enderror
    </div>

    <div>
      <label class="text-sm">Author</label>
      <input name="author" value="{{ old('author',$book->author) }}" class="mt-1 w-full rounded-xl bg-white/80 dark:bg-slate-900/70 border border-slate-200/70 dark:border-white/10 px-3 py-2">
    </div>

    <div>
      <label class="text-sm">Kategori</label>
      <input name="category" value="{{ old('category',$book->category) }}" class="mt-1 w-full rounded-xl bg-white/80 dark:bg-slate-900/70 border border-slate-200/70 dark:border-white/10 px-3 py-2">
    </div>

    <div>
      <label class="text-sm">ISBN</label>
      <input name="isbn" value="{{ old('isbn',$book->isbn) }}" class="mt-1 w-full rounded-xl bg-white/80 dark:bg-slate-900/70 border border-slate-200/70 dark:border-white/10 px-3 py-2">
    </div>

    <div>
      <label class="text-sm">Barcode</label>
      <input name="barcode" value="{{ old('barcode',$book->barcode) }}" class="mt-1 w-full rounded-xl bg-white/80 dark:bg-slate-900/70 border border-slate-200/70 dark:border-white/10 px-3 py-2">
    </div>

    <div>
      <label class="text-sm">Total</label>
      <input type="number" min="0" name="total_copies" value="{{ old('total_copies',$book->total_copies) }}" class="mt-1 w-full rounded-xl bg-white/80 dark:bg-slate-900/70 border border-slate-200/70 dark:border-white/10 px-3 py-2">
      @error('total_copies')<div class="text-xs text-rose-500 mt-1">{{ $message }}</div>@enderror
    </div>

    <div>
      <label class="text-sm">Tersedia</label>
      <input type="number" min="0" name="available_copies" value="{{ old('available_copies',$book->available_copies) }}" class="mt-1 w-full rounded-xl bg-white/80 dark:bg-slate-900/70 border border-slate-200/70 dark:border-white/10 px-3 py-2">
      @error('available_copies')<div class="text-xs text-rose-500 mt-1">{{ $message }}</div>@enderror
    </div>

    <div class="md:col-span-2 flex items-center gap-2">
      <a href="{{ route('admin.books.index') }}" class="rounded-xl px-3 py-2 text-sm bg-slate-900/10 dark:bg-white/10 hover:bg-slate-900/20 dark:hover:bg-white/20 transition">Batal</a>
      <button class="rounded-xl px-3 py-2 text-sm bg-indigo-600 text-white hover:bg-indigo-500 transition">{{ $book->exists ? 'Simpan' : 'Buat' }}</button>
    </div>
  </form>
@endsection
