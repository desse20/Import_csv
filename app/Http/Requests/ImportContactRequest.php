<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Autoriser tous les utilisateurs à faire cette requête
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return
            [
            'file' => [
                'required',
                'file',
                'mimetypes:text/csv,text/plain' // Validation du type MIME
            ]
        ];
    }
}
