<?php

namespace App\Http\Requests;

use App\Helpers\ResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateNameRequest extends FormRequest
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
            'name'=>'sometimes|string|min:4|regex:/^[A-Z].*/'
        ];
    }
    public function messages()
    {
        return[
            'name.min' => 'name must not be less than 4',
            'name.regex' => ' name must start with capitale letter'
        ];
    }


    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(ResponseHelper::returnValidationError($validator));
    }
}
