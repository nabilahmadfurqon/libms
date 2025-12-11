<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'name',
        'grade',
        'kelas',
        'status',   // contoh nilai: 'aktif', 'nonaktif'
        'barcode',
    ];
    public function visits() {
    return $this->hasMany(Visit::class, 'student_id', 'student_id');
}
public function loans() {
    return $this->hasMany(Loan::class, 'student_id', 'student_id');
}
}
