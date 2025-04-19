<?php

namespace App\Http\Requests;

use App\Helpers\ResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CategoryRequest extends FormRequest
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
    public function rules()
    {
        if ($this->isMethod('POST')) {
            return $this->createRules();
        }
        return $this->updateRules();
    }
    public function createRules()
    {
        return [
            'name' => 'required|string|min:4|regex:/^[A-Z].*/'
        ];
    }
    public function updateRules()
    {
        return [
            'name' => 'sometimes|string|min:4|regex:/^[A-Z].*/'
        ];
    }
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(ResponseHelper::returnValidationError($validator));
    }
}
