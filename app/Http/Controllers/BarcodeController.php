<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Student;

class BarcodeController extends Controller
{
    // ===== CETAK BUKU =====
    // - Code akan diambil dari kolom book_id
    // - Bisa filter via ?q= atau pilih specific melalui ?ids=BK-1,BK-2
    public function books(Request $r)
    {
        $q   = trim((string)$r->query('q', ''));
        $ids = trim((string)$r->query('ids', '')); // comma separated book_id

        $items = Book::query()
            ->when($q, function($w) use ($q){
                $w->where(function($s) use ($q){
                    $s->where('title','like',"%{$q}%")
                      ->orWhere('book_id','like',"%{$q}%")
                      ->orWhere('author','like',"%{$q}%")
                      ->orWhere('isbn','like',"%{$q}%");
                });
            })
            ->when($ids, function($w) use ($ids){
                $list = array_values(array_filter(array_map('trim', explode(',', $ids))));
                if (!empty($list)) {
                    $w->whereIn('book_id', $list);
                }
            })
            ->orderBy('title')
            ->limit(2000) // safety limit
            ->get([
                'book_id','title','author','category','isbn'
            ]);

        return view('barcodes.books', [
            'items' => $items,
            'q'     => $q,
            'ids'   => $ids,
        ]);
    }

    // Terima checkbox pilihan dari tabel books
    public function booksPreview(Request $r)
    {
        $ids = (array)$r->input('ids', []);
        $ids = array_values(array_filter(array_map('trim', $ids)));
        if (empty($ids)) {
            return back()->with('err','Pilih minimal satu buku dulu.');
        }
        return redirect()->route('admin.barcodes.books', ['ids' => implode(',', $ids)]);
    }

    // ===== CETAK SISWA =====
    // - Code akan diambil dari kolom barcode (students.barcode)
    // - Bisa filter via ?q= atau pilih specific via ?ids=BAR0001,BAR0002 (berdasarkan barcode)
    public function students(Request $r)
    {
        $q   = trim((string)$r->query('q', ''));
        $ids = trim((string)$r->query('ids', '')); // comma separated student barcode

        $items = Student::query()
            ->when($q, function($w) use ($q){
                $w->where(function($s) use ($q){
                    $s->where('name','like',"%{$q}%")
                      ->orWhere('student_id','like',"%{$q}%")
                      ->orWhere('kelas','like',"%{$q}%")
                      ->orWhere('barcode','like',"%{$q}%");
                });
            })
            ->when($ids, function($w) use ($ids){
                $list = array_values(array_filter(array_map('trim', explode(',', $ids))));
                if (!empty($list)) {
                    $w->whereIn('barcode', $list);
                }
            })
            ->orderBy('name')
            ->limit(2000)
            ->get([
                'student_id','name','grade','kelas','status','barcode'
            ]);

        return view('barcodes.students', [
            'items' => $items,
            'q'     => $q,
            'ids'   => $ids,
        ]);
    }

    // Terima checkbox pilihan dari tabel students
    public function studentsPreview(Request $r)
    {
        $ids = (array)$r->input('ids', []);
        $ids = array_values(array_filter(array_map('trim', $ids)));
        if (empty($ids)) {
            return back()->with('err','Pilih minimal satu siswa dulu.');
        }
        return redirect()->route('admin.barcodes.students', ['ids' => implode(',', $ids)]);
    }
}
