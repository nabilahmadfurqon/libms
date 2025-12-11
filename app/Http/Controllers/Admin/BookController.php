<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BookRequest;
use App\Models\Book;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BookController extends Controller
{
    /** =========================
     *  List & CRUD
     *  ========================= */
    public function index()
    {
        $q = request('q');
        $books = Book::when($q, fn ($w) => $w->where(function ($s) use ($q) {
                    $s->where('title', 'like', "%{$q}%")
                      ->orWhere('book_id', 'like', "%{$q}%")
                      ->orWhere('author', 'like', "%{$q}%");
                }))
                ->orderBy('title')
                ->paginate(15)
                ->withQueryString();

        return view('admin.books.index', compact('books', 'q'));
    }

    public function create()
    {
        $book = new Book(['total_copies' => 0, 'available_copies' => 0]);
        return view('admin.books.form', compact('book'));
    }

    public function store(BookRequest $request)
    {
        Book::create($request->validated());
        return redirect()->route('admin.books.index')->with('ok', 'Buku dibuat.');
    }

    public function edit(Book $book)
    {
        return view('admin.books.form', compact('book'));
    }

    public function update(BookRequest $request, Book $book)
    {
        $book->update($request->validated());
        return redirect()->route('admin.books.index')->with('ok', 'Buku diperbarui.');
    }

    public function destroy(Book $book)
    {
        $book->delete();
        return back()->with('ok', 'Buku dihapus.');
    }

    /** =========================
     *  Impor CSV
     *  ========================= */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:20480'],
        ]);

        $expected = ['book_id','title','author','category','isbn','barcode','total_copies','available_copies'];

        $path = $request->file('file')->getRealPath();
        if (!is_readable($path)) {
            return back()->with('err', 'File tidak bisa dibaca.');
        }

        $fh = fopen($path, 'r');
        if ($fh === false) {
            return back()->with('err', 'Gagal membuka file.');
        }

        // baca header & validasi persis
        $header = fgetcsv($fh);
        $normalizeHeader = fn(array $arr) => array_map(fn($h) => strtolower(trim((string)$h)), $arr ?: []);
        if ($normalizeHeader($header) !== $expected) {
            fclose($fh);
            return back()->with('err', 'Header CSV harus persis: '.implode(',', $expected));
        }

        $ok = 0; $skip = 0; $errors = [];

        $rowIndex = 1; // header = baris 1
        while (($data = fgetcsv($fh)) !== false) {
            $rowIndex++;

            // pastikan jumlah kolom sesuai
            if (count($data) !== count($expected)) {
                $skip++; $errors[] = "Baris {$rowIndex}: jumlah kolom tidak sesuai";
                continue;
            }

            // map ke key sesuai expected
            $payload = array_combine($expected, $data);

            // normalisasi nilai kosong: nan/NaN/null/None/-/""
            foreach ($payload as $k => $v) {
                $payload[$k] = $this->normalizeCsvValue($v);
            }

            // minimal wajib
            if (empty($payload['book_id']) || empty($payload['title'])) {
                $skip++; $errors[] = "Baris {$rowIndex}: book_id/title kosong";
                continue;
            }

            // kalau barcode kosong → pakai book_id (hindari bentrok UNIQUE karena string 'nan')
            if (empty($payload['barcode'])) {
                $payload['barcode'] = $payload['book_id'];
            }

            // angka & minimal 0
            $payload['total_copies']     = max(0, (int) ($payload['total_copies'] ?? 0));
            $payload['available_copies'] = max(0, (int) ($payload['available_copies'] ?? 0));

            try {
                // upsert by book_id
                Book::updateOrCreate(
                    ['book_id' => $payload['book_id']],
                    [
                        'title'            => $payload['title'],
                        'author'           => $payload['author'],
                        'category'         => $payload['category'],
                        'isbn'             => $payload['isbn'],
                        'barcode'          => $payload['barcode'],
                        'total_copies'     => $payload['total_copies'],
                        'available_copies' => $payload['available_copies'],
                    ]
                );
                $ok++;
            } catch (\Throwable $e) {
                $skip++; $errors[] = "Baris {$rowIndex}: ".$e->getMessage();
            }
        }

        fclose($fh);

        $msg = "Impor selesai: {$ok} sukses, {$skip} dilewati.";
        if (!empty($errors)) {
            $msg .= ' Detail: '.implode(' | ', $errors);
        }

        return back()->with($ok ? 'ok' : 'err', $msg);
    }

    /** =========================
     *  Template & Sample CSV
     *  ========================= */
    public function template(): StreamedResponse
    {
        $headers = ['book_id','title','author','category','isbn','barcode','total_copies','available_copies'];

        return response()->streamDownload(function () use ($headers) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers);
            fclose($out);
        }, 'template_books.csv', ['Content-Type' => 'text/csv']);
    }

    public function sample(): StreamedResponse
    {
        $headers = ['book_id','title','author','category','isbn','barcode','total_copies','available_copies'];
        $sample  = ['BK-0001','Bahasa Indonesia 5','Tim Penulis','Pelajaran','9786020000001','BR-BK-0001',10,10];

        return response()->streamDownload(function () use ($headers, $sample) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers);
            fputcsv($out, $sample);
            fclose($out);
        }, 'sample_books.csv', ['Content-Type' => 'text/csv']);
    }

    /** =========================
     *  EXPORT DATA BUKU (LIST) KE CSV
     *  ========================= */
    public function export(Request $request): StreamedResponse
    {
        $q = $request->query('q');

        $filename = 'books_'.now()->format('Ymd_His').'.csv';
        $headersHttp = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->streamDownload(function () use ($q) {
            $out = fopen('php://output', 'w');

            // header kolom
            fputcsv($out, [
                'book_id',
                'title',
                'author',
                'category',
                'isbn',
                'barcode',
                'total_copies',
                'available_copies',
            ]);

            // query sama dengan index (tapi tanpa paginate)
            $query = Book::when($q, fn ($w) => $w->where(function ($s) use ($q) {
                            $s->where('title', 'like', "%{$q}%")
                              ->orWhere('book_id', 'like', "%{$q}%")
                              ->orWhere('author', 'like', "%{$q}%");
                        }))
                        ->orderBy('title');

            // supaya hemat memori, pakai chunk
            $query->chunk(500, function ($chunk) use ($out) {
                foreach ($chunk as $b) {
                    fputcsv($out, [
                        $b->book_id,
                        $b->title,
                        $b->author,
                        $b->category,
                        $b->isbn,
                        $b->barcode,
                        $b->total_copies,
                        $b->available_copies,
                    ]);
                }
            });

            fclose($out);
        }, $filename, $headersHttp);
    }

    /** =========================
     *  Helper
     *  ========================= */
    private function normalizeCsvValue($v): ?string
    {
        if (is_null($v)) return null;

        $v = trim((string) $v);
        if ($v === '') return null;

        $lower = strtolower($v);
        if (in_array($lower, ['nan','null','none','-','n/a','na'])) {
            return null;
        }

        return $v;
    }
}
