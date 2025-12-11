<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Aman untuk linter & runtime
        return Auth::check() && Auth::user()->role === 'admin';
    }

    public function rules(): array
    {
        $id = $this->route('student')?->id;

        return [
            'student_id' => ['required','string','max:100', Rule::unique('students','student_id')->ignore($id)],
            'name'       => ['required','string','max:255'],
            'grade'      => ['nullable','string','max:20'],
            'kelas'      => ['nullable','string','max:20'],
            'status'     => ['required','string','max:20'], // aktif / nonaktif
            'barcode'    => ['nullable','string','max:100', Rule::unique('students','barcode')->ignore($id)],
        ];
    }
}
