<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDetailsRequest extends FormRequest
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
            'book_id' => 'required',
            'isbn' => 'required',
            'number_of_pages' => 'sometimes',
            // 'number_of_pages' => 'sometimes|min:200|max:2000',
            'publication_date' => 'required|date'
        ];
    }
    public function messages()
    {
        return [
            'isbn.required' => 'Enter isbn',
            'number_of_pages.min' => 'number_of_pages must not be less than 200',
            'publication_date.required' => 'Enter publication_date',
            'publication_date.date' => 'Enter a valid date'
        ];
    }
}
