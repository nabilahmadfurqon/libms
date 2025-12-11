<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StudentRequest;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentController extends Controller
{
    /**
     * Menampilkan daftar siswa
     */
    public function index()
    {
        $q = request('q');
        $kelas = request('kelas');
        $grade = request('grade');
        $status = request('status');

        $students = Student::when($q, fn ($w) => $w->where(function ($s) use ($q) {
                            $s->where('name', 'like', "%$q%")
                              ->orWhere('student_id', 'like', "%$q%")
                              ->orWhere('kelas', 'like', "%$q%")
                              ->orWhere('barcode', 'like', "%$q%");
                        }))
                        ->when($kelas, fn ($w) => $w->where('kelas', $kelas))
                        ->when($grade, fn ($w) => $w->where('grade', $grade))
                        ->when($status, fn ($w) => $w->where('status', $status))
                        ->orderBy('grade')
                        ->orderBy('kelas')
                        ->orderBy('name')
                        ->paginate(20)
                        ->withQueryString();

        $kelasList = Student::distinct()->pluck('kelas')->sort();
        $gradeList = Student::distinct()->pluck('grade')->sort();
        $statusList = Student::distinct()->pluck('status')->sort();

        return view('admin.students.index', compact('students', 'q', 'kelas', 'grade', 'status', 'kelasList', 'gradeList', 'statusList'));
    }

    /**
     * Menampilkan form untuk membuat siswa baru
     */
    public function create()
    {
        $student = new Student(['status' => 'aktif']);
        $kelasList = Student::distinct()->pluck('kelas')->sort();
        $gradeList = Student::distinct()->pluck('grade')->sort();
        
        return view('admin.students.form', compact('student', 'kelasList', 'gradeList'));
    }

    /**
     * Menyimpan siswa baru
     */
    public function store(StudentRequest $request)
    {
        $data = $request->validated();
        
        // Generate barcode jika kosong
        if (empty($data['barcode'])) {
            $data['barcode'] = $data['student_id'];
        }
        
        Student::create($data);
        
        return redirect()->route('admin.students.index')
            ->with('success', 'Siswa berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit siswa
     */
    public function edit(Student $student)
    {
        $kelasList = Student::distinct()->pluck('kelas')->sort();
        $gradeList = Student::distinct()->pluck('grade')->sort();
        
        return view('admin.students.form', compact('student', 'kelasList', 'gradeList'));
    }

    /**
     * Memperbarui data siswa
     */
    public function update(StudentRequest $request, Student $student)
    {
        $data = $request->validated();
        
        // Generate barcode jika kosong
        if (empty($data['barcode'])) {
            $data['barcode'] = $data['student_id'];
        }
        
        $student->update($data);
        
        return redirect()->route('admin.students.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Menghapus siswa
     */
    public function destroy(Student $student)
    {
        $student->delete();
        
        return back()->with('success', 'Siswa berhasil dihapus.');
    }

    /**
     * Menghapus beberapa siswa sekaligus
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:students,id',
        ]);
        
        Student::whereIn('id', $request->ids)->delete();
        
        return back()->with('success', count($request->ids) . ' siswa berhasil dihapus.');
    }

    /**
     * Import data siswa dari CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:51200'], // 50MB
        ]);

        // Set auto detect line endings untuk handle Mac/Windows/Linux
        ini_set('auto_detect_line_endings', true);

        $expected = ['student_id', 'name', 'grade', 'kelas', 'status', 'barcode'];
        
        $path = $request->file('file')->getRealPath();
        
        // Validasi: Pastikan file readable
        if (!is_readable($path)) {
            return back()->with('error', 'File tidak bisa dibaca.');
        }

        // Baca seluruh file untuk deteksi encoding
        $content = file_get_contents($path);
        
        // Deteksi dan konversi encoding jika perlu
        $encoding = mb_detect_encoding($content, ['UTF-8', 'UTF-16LE', 'UTF-16BE', 'ISO-8859-1', 'WINDOWS-1252'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
            file_put_contents($path, $content);
        }

        // Handle BOM (Byte Order Mark)
        $bom = pack('H*', 'EFBBBF');
        $content = preg_replace("/^$bom/", '', $content);
        file_put_contents($path, $content);

        $fh = fopen($path, 'r');
        if ($fh === false) {
            return back()->with('error', 'Gagal membuka file.');
        }

        // Baca dan validasi header
        $header = fgetcsv($fh);
        
        // Normalize header - lebih fleksibel
        $normalizeHeader = function (array $arr) {
            return array_map(function ($h) {
                $h = trim((string) $h);
                $h = strtolower($h);
                // Hapus BOM jika ada
                if (strpos($h, "\xEF\xBB\xBF") === 0) {
                    $h = substr($h, 3);
                }
                // Normalize nama kolom dalam berbagai bahasa
                $h = str_replace(['id siswa', 'nomor induk', 'nis', 'nidn'], 'student_id', $h);
                $h = str_replace(['nama siswa', 'nama lengkap', 'nama'], 'name', $h);
                $h = str_replace(['tingkat', 'kelas tingkat', 'tingkat kelas'], 'grade', $h);
                $h = str_replace(['ruang kelas', 'ruang', 'rombel'], 'kelas', $h);
                $h = str_replace(['status siswa', 'keaktifan'], 'status', $h);
                $h = str_replace(['kode batang', 'barcode siswa', 'kode'], 'barcode', $h);
                return $h;
            }, $arr ?: []);
        };

        $normalizedHeader = $normalizeHeader($header);
        
        // Validasi header
        if (empty(array_intersect($expected, $normalizedHeader))) {
            fclose($fh);
            return back()->with('error', 
                'Header CSV tidak sesuai. Kolom yang diperlukan: ' . 
                implode(', ', $expected) . 
                '. Kolom yang ditemukan: ' . 
                implode(', ', $normalizedHeader)
            );
        }

        // Mapping kolom dari file ke expected columns
        $columnMapping = [];
        foreach ($expected as $col) {
            $index = array_search($col, $normalizedHeader);
            if ($index !== false) {
                $columnMapping[$col] = $index;
            }
        }

        $successCount = 0;
        $skipCount = 0;
        $errors = [];
        $rowIndex = 1; // header = baris 1
        $batchData = [];
        $maxRows = 5000; // Batasi jumlah baris untuk menghindari memory overload

        while (($data = fgetcsv($fh)) !== false && $rowIndex <= $maxRows + 1) {
            $rowIndex++;

            // Skip baris kosong
            if ($data === null || (count($data) === 1 && trim($data[0]) === '')) {
                continue;
            }

            // Prepare payload
            $payload = [];
            foreach ($columnMapping as $field => $index) {
                $value = isset($data[$index]) ? trim((string) $data[$index]) : '';
                
                // Convert encoding jika perlu
                if (!mb_check_encoding($value, 'UTF-8')) {
                    $value = mb_convert_encoding($value, 'UTF-8', 'auto');
                }
                
                $payload[$field] = $value;
            }

            // Validasi data wajib
            if (empty($payload['student_id']) || empty($payload['name'])) {
                $skipCount++;
                $errors[] = "Baris {$rowIndex}: student_id atau name kosong";
                continue;
            }

            // Clean and format data
            $payload['student_id'] = trim($payload['student_id']);
            $payload['name'] = trim($payload['name']);
            $payload['grade'] = strtoupper(trim($payload['grade'] ?? ''));
            $payload['kelas'] = strtoupper(trim($payload['kelas'] ?? ''));
            
            // Set default status jika kosong
            if (empty($payload['status'])) {
                $payload['status'] = 'aktif';
            }
            
            // Validasi status
            $validStatuses = ['aktif', 'nonaktif', 'pindah', 'lulus', 'keluar'];
            if (!in_array(strtolower($payload['status']), $validStatuses)) {
                $payload['status'] = 'aktif';
            }
            
            // Generate barcode jika kosong
            if (empty($payload['barcode'])) {
                $payload['barcode'] = $payload['student_id'];
            }

            // Tambahkan ke batch
            $batchData[] = [
                'student_id' => $payload['student_id'],
                'name' => $payload['name'],
                'grade' => $payload['grade'],
                'kelas' => $payload['kelas'],
                'status' => strtolower($payload['status']),
                'barcode' => $payload['barcode'],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Batch insert setiap 100 record
            if (count($batchData) >= 100) {
                $this->processBatch($batchData, $successCount, $skipCount, $errors);
                $batchData = [];
            }
        }

        // Process sisa data
        if (!empty($batchData)) {
            $this->processBatch($batchData, $successCount, $skipCount, $errors);
        }

        fclose($fh);

        // Prepare response message
        $messageType = 'success';
        $message = "Import selesai. Berhasil: {$successCount}, Gagal: {$skipCount}.";
        
        if ($skipCount > 0 && !empty($errors)) {
            $messageType = 'warning';
            
            // Limit error display
            $displayErrors = array_slice($errors, 0, 10);
            $message .= "\n\nBeberapa error:\n- " . implode("\n- ", $displayErrors);
            
            if (count($errors) > 10) {
                $message .= "\n... dan " . (count($errors) - 10) . " error lainnya.";
            }
            
            // Save detailed errors to log file
            $logFilename = 'import_errors_' . date('Y-m-d_H-i-s') . '.txt';
            $logPath = storage_path('logs/' . $logFilename);
            file_put_contents($logPath, "Import Errors:\n" . implode("\n", $errors));
            
            $message .= "\n\nDetail error lengkap disimpan di: " . $logFilename;
        }

        if ($rowIndex > $maxRows + 1) {
            $message .= "\n\nPeringatan: Hanya " . $maxRows . " baris pertama yang diproses.";
        }

        return back()->with($messageType, $message);
    }

    /**
     * Helper untuk proses batch insert/update
     */
    private function processBatch(array $batchData, int &$successCount, int &$skipCount, array &$errors): void
    {
        try {
            // Gunakan transaksi untuk consistency
            DB::beginTransaction();
            
            foreach ($batchData as $data) {
                try {
                    // Cek duplikasi student_id
                    $existing = Student::where('student_id', $data['student_id'])->first();
                    
                    if ($existing) {
                        // Update existing record
                        $existing->update([
                            'name' => $data['name'],
                            'grade' => $data['grade'],
                            'kelas' => $data['kelas'],
                            'status' => $data['status'],
                            'barcode' => $data['barcode'],
                        ]);
                    } else {
                        // Insert new record
                        Student::create($data);
                    }
                    
                    $successCount++;
                } catch (\Exception $e) {
                    $skipCount++;
                    $errors[] = "Error data: {$data['student_id']} - {$data['name']}: " . $e->getMessage();
                }
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $skipCount += count($batchData);
            $errors[] = "Batch error: " . $e->getMessage();
        }
    }

    /**
     * Download template CSV
     */
    public function template(): StreamedResponse
    {
        $headers = ['student_id', 'name', 'grade', 'kelas', 'status', 'barcode'];
        
        return response()->streamDownload(function () use ($headers) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers);
            
            // Contoh data
            $examples = [
                ['4213-2526001', 'Abgary Kula Tristanto', 'I', 'MEKKAH', 'aktif', '4213-2526001'],
                ['4213-2526002', 'Airlangga Ibrahim Saputro', 'I', 'MEKKAH', 'aktif', '4213-2526002'],
                ['4213-2526003', 'Alesha Claire Arkadewi', 'I', 'MEKKAH', 'aktif', '4213-2526003'],
            ];
            
            foreach ($examples as $example) {
                fputcsv($out, $example);
            }
            
            fclose($out);
        }, 'template_siswa.csv', [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="template_siswa.csv"',
        ]);
    }

    /**
     * Download sample CSV
     */
    public function sample(): StreamedResponse
    {
        $headers = ['student_id', 'name', 'grade', 'kelas', 'status', 'barcode'];
        
        return response()->streamDownload(function () use ($headers) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers);
            
            // Sample data
            $samples = [
                ['STD-0001', 'Nadia Aulia', '5', '5A', 'aktif', 'STD-0001'],
                ['STD-0002', 'Budi Santoso', '6', '6B', 'aktif', 'STD-0002'],
                ['STD-0003', 'Siti Aminah', '4', '4C', 'nonaktif', 'STD-0003'],
                ['STD-0004', 'Rizky Pratama', '3', '3A', 'aktif', 'STD-0004'],
                ['STD-0005', 'Maya Indah', '2', '2B', 'pindah', 'STD-0005'],
            ];
            
            foreach ($samples as $sample) {
                fputcsv($out, $sample);
            }
            
            fclose($out);
        }, 'sample_siswa.csv', [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="sample_siswa.csv"',
        ]);
    }

    /**
     * Export data siswa ke CSV
     */
    public function export(Request $request): StreamedResponse
    {
        $q = $request->query('q');
        $kelas = $request->query('kelas');
        $grade = $request->query('grade');
        $status = $request->query('status');
        $format = $request->query('format', 'csv'); // csv atau excel

        $students = Student::when($q, fn ($w) => $w->where(function ($s) use ($q) {
                                $s->where('name', 'like', "%$q%")
                                  ->orWhere('student_id', 'like', "%$q%")
                                  ->orWhere('kelas', 'like', "%$q%")
                                  ->orWhere('barcode', 'like', "%$q%");
                            }))
                            ->when($kelas, fn ($w) => $w->where('kelas', $kelas))
                            ->when($grade, fn ($w) => $w->where('grade', $grade))
                            ->when($status, fn ($w) => $w->where('status', $status))
                            ->orderBy('grade')
                            ->orderBy('kelas')
                            ->orderBy('name')
                            ->get();

        $filename = 'data_siswa_' . now()->format('Ymd_His') . '.' . $format;
        
        if ($format === 'excel') {
            return $this->exportExcel($students, $filename);
        }

        return $this->exportCsv($students, $filename);
    }

    /**
     * Export ke CSV
     */
    private function exportCsv($students, $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($students) {
            $out = fopen('php://output', 'w');
            
            // Tambahkan BOM untuk kompatibilitas Excel
            fwrite($out, "\xEF\xBB\xBF");
            
            // Header CSV
            fputcsv($out, ['student_id', 'name', 'grade', 'kelas', 'status', 'barcode']);

            foreach ($students as $student) {
                fputcsv($out, [
                    $student->student_id,
                    $student->name,
                    $student->grade,
                    $student->kelas,
                    $student->status,
                    $student->barcode,
                ]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=utf-8',
        ]);
    }

    /**
     * Export ke Excel (CSV dengan format khusus)
     */
    private function exportExcel($students, $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($students) {
            $out = fopen('php://output', 'w');
            
            // Tambahkan BOM untuk kompatibilitas Excel
            fwrite($out, "\xEF\xBB\xBF");
            
            // Header dengan format Excel
            fputcsv($out, ['NO', 'ID SISWA', 'NAMA', 'TINGKAT', 'KELAS', 'STATUS', 'BARCODE'], ';');

            $no = 1;
            foreach ($students as $student) {
                fputcsv($out, [
                    $no++,
                    $student->student_id,
                    $student->name,
                    $student->grade,
                    $student->kelas,
                    $student->status,
                    $student->barcode,
                ], ';');
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=utf-8',
        ]);
    }

    /**
     * Export data untuk QR Code printing
     */
    public function exportForPrinting(Request $request): StreamedResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:students,id',
        ]);

        $students = Student::whereIn('id', $request->ids)
            ->orderBy('kelas')
            ->orderBy('name')
            ->get(['student_id', 'name', 'grade', 'kelas', 'barcode']);

        $filename = 'siswa_qrcode_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($students) {
            $out = fopen('php://output', 'w');
            
            // Header untuk printing
            fputcsv($out, ['NO', 'NAMA', 'KELAS', 'ID SISWA', 'BARCODE', 'QR CODE DATA']);

            $no = 1;
            foreach ($students as $student) {
                // Data untuk QR Code (bisa berupa URL atau ID)
                $qrData = route('student.show', $student->student_id);
                
                fputcsv($out, [
                    $no++,
                    $student->name,
                    $student->kelas,
                    $student->student_id,
                    $student->barcode,
                    $qrData,
                ]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=utf-8',
        ]);
    }

    /**
     * Update status siswa secara massal
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:students,id',
            'status' => 'required|in:aktif,nonaktif,pindah,lulus,keluar',
        ]);

        $count = Student::whereIn('id', $request->ids)
            ->update(['status' => $request->status]);

        return back()->with('success', "Status {$count} siswa berhasil diperbarui.");
    }

    /**
     * Statistik siswa
     */
    public function statistics()
    {
        $total = Student::count();
        $aktif = Student::where('status', 'aktif')->count();
        $nonaktif = Student::where('status', 'nonaktif')->count();
        
        $byGrade = Student::select('grade', DB::raw('count(*) as total'))
            ->groupBy('grade')
            ->orderBy('grade')
            ->get()
            ->pluck('total', 'grade');
            
        $byKelas = Student::select('kelas', DB::raw('count(*) as total'))
            ->groupBy('kelas')
            ->orderBy('kelas')
            ->get()
            ->pluck('total', 'kelas');
            
        $byStatus = Student::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->orderBy('status')
            ->get()
            ->pluck('total', 'status');

        return view('admin.students.statistics', compact(
            'total', 'aktif', 'nonaktif', 'byGrade', 'byKelas', 'byStatus'
        ));
    }

    /**
     * Cari siswa untuk autocomplete
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $students = Student::where('name', 'like', "%{$query}%")
            ->orWhere('student_id', 'like', "%{$query}%")
            ->orWhere('barcode', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'student_id', 'name', 'kelas', 'grade']);
            
        return response()->json($students);
    }

    /**
     * Validasi duplikasi student_id
     */
    public function checkDuplicate(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
            'id' => 'sometimes', // untuk update, exclude current student
        ]);

        $query = Student::where('student_id', $request->student_id);
        
        if ($request->has('id')) {
            $query->where('id', '!=', $request->id);
        }
        
        $exists = $query->exists();
        
        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'ID Siswa sudah digunakan' : 'ID Siswa tersedia'
        ]);
    }

    /**
     * Generate barcode untuk siswa yang belum punya
     */
    public function generateBarcodes()
    {
        $students = Student::whereNull('barcode')
            ->orWhere('barcode', '')
            ->get();
            
        $updated = 0;
        
        foreach ($students as $student) {
            $student->update(['barcode' => $student->student_id]);
            $updated++;
        }
        
        return back()->with('success', "Berhasil generate barcode untuk {$updated} siswa.");
    }
}