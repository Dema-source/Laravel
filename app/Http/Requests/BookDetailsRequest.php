<?php

namespace App\Http\Requests;

use App\Helpers\ResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BookDetailsRequest extends FormRequest
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
            'book_id' => 'required',
            'isbn' => 'required',
            'number_of_pages' => 'sometimes',
            // 'number_of_pages' => 'sometimes|min:200|max:2000',
            'publication_date' => 'required|date'
        ];
    }
    public function updateRules()
    {
        return [
            'isbn' => 'sometimes',
            'number_of_pages' => 'sometimes',
            // 'number_of_pages' => 'sometimes|min:200',
            'publication_date' => 'sometimes|date'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(ResponseHelper::returnValidationError($validator));
    }
}
