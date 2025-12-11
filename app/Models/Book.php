<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'title',
        'author',
        'category',
        'isbn',
        'barcode',
        'total_copies',
        'available_copies',
    ];

    protected $casts = [
        'total_copies'     => 'integer',
        'available_copies' => 'integer',
    ];
}
