<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'auther_id' => 'required',
            'title' => 'required|string',
            'image' => 'required|image|mimes:png,jpg,jpeg|max:2048',
            'pdf_link' => 'file|mimes:pdf|max:2048|required_without:doc_link',
            'doc_link' => 'file|mimes:doc,docx|max:2048|required_without:pdf_link'
        ];
    }
}
