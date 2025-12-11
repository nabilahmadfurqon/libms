<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BookRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Aman untuk linter & runtime
        return Auth::check() && Auth::user()->role === 'admin';
    }

    public function rules(): array
    {
        $id = $this->route('book')?->id;

        return [
            'book_id'          => ['required','string','max:100', Rule::unique('books','book_id')->ignore($id)],
            'title'            => ['required','string','max:255'],
            'author'           => ['nullable','string','max:255'],
            'category'         => ['nullable','string','max:100'],
            'isbn'             => ['nullable','string','max:50'],
            'barcode'          => ['nullable','string','max:100', Rule::unique('books','barcode')->ignore($id)],
            'total_copies'     => ['required','integer','min:0'],
            'available_copies' => ['required','integer','min:0','lte:total_copies'],
        ];
    }
}
