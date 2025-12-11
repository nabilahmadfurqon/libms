<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Student;

class Visit extends Model
{
    use HasFactory;

    protected $table = 'visits';

    protected $fillable = [
        'student_id',
        'class_name',     // ⬅️ baru
        'student_count',
        'purpose',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    // Relasi ke siswa (opsional, tapi berguna)
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    // Scope untuk kunjungan hari ini
    public function scopeToday($query)
    {
        return $query->whereDate('visited_at', today());
    }

    // Scope untuk rentang tanggal (dipakai di history)
    public function scopeDateRange($query, $from, $to)
    {
        return $query
            ->whereDate('visited_at', '>=', $from)
            ->whereDate('visited_at', '<=', $to);
    }
}
