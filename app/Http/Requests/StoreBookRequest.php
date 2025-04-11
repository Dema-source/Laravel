<?php

namespace App\Http\Requests;

use App\Helpers\ResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

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
            'auther_id' => 'required|exists:users,id',
            'title' => 'required|string|unique:books,title',
            'image' => 'required|image|mimes:png,jpg,jpeg|max:2048',
            'file' => 'file|mimes:pdf,doc,docx|max:2048|required',
            // 'doc_link' => 'file|mimes:doc,docx|max:2048|required_without:pdf_link'
        ];
    }
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(ResponseHelper::returnValidationError($validator));
    }
}
