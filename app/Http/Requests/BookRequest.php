<?php

namespace App\Http\Requests;

use App\Helpers\ResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BookRequest extends FormRequest
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
        if ($this->isMethod('POST')) {
            return $this->createRules();
        }
        return $this->updateRules();
    }
    public function createRules()
    {
        return [
            'auther_id' => 'required|exists:authers,id',
            'title' => 'required|string|unique:books,title',
            'image' => 'required|image|mimes:png,jpg,jpeg|max:2048',
            'file_path' => 'file|mimes:pdf,doc,docx|max:2048|required',
        ];
    }
    public function updateRules()
    {
        return [
            'title' => 'sometimes|string',
            'image' => 'sometimes|image|mimes:png,jpg,jpeg|max:2048',
        ];
    }
    
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(ResponseHelper::returnValidationError($validator));
    }
}
