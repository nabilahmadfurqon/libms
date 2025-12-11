<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Loan extends Model
{
    protected $fillable = [
        'student_id','book_id','days','loaned_at','due_at','returned_at',
    ];

    protected $casts = [
        'days'        => 'integer',
        'loaned_at'   => 'datetime',
        'due_at'      => 'datetime',
        'returned_at' => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    public function student()
{
    return $this->belongsTo(Student::class, 'student_id', 'student_id');
}

public function book()
{
    return $this->belongsTo(Book::class, 'book_id', 'book_id');
}
    // Masih dipinjam
    public function scopeActive($q){ return $q->whereNull('returned_at'); }

    // Telat (SQLite)
    public function scopeOverdue($q){
        return $q->whereNull('returned_at')
                 ->whereRaw("julianday(created_at, '+' || days || ' days') < julianday('now')");
    }

    // Accessors bantu di Blade
    protected $appends = ['due_at','is_overdue','days_left'];

    public function getDueAtAttribute(): ?Carbon
    {
        if (!$this->created_at) return null;
        return $this->created_at->copy()->addDays((int)($this->days ?? 7));
    }

    public function getIsOverdueAttribute(): bool
    {
        return is_null($this->returned_at) && $this->due_at && now()->gt($this->due_at);
    }

    public function getDaysLeftAttribute(): ?int
    {
        if ($this->returned_at || !$this->due_at) return null;
        return now()->startOfDay()->diffInDays($this->due_at->startOfDay(), false);
    }
}
