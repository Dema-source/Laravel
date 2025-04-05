<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDetailsRequest extends FormRequest
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
            'isbn' => 'sometimes',
            'number_of_pages' => 'sometimes',
            // 'number_of_pages' => 'sometimes|min:200',
            'publication_date' => 'sometimes|date'
        ];
    }
    public function messages()
    {
        return [
            'number_of_pages.min' => 'number_of_pages must not be less than 200',
            'publication_date.date' => 'Enter a valid date'
        ];
    }
}
