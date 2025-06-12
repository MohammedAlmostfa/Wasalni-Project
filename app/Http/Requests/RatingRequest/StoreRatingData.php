<?php

namespace App\Http\Requests\RatingRequest;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRatingData extends FormRequest
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
            'rate'          => 'required|integer|min:1|max:5',
            'review'        => 'required|string|max:1000',
            'rated_user_id' => 'required|exists:users,id',
        ];
    }
    /**
    * Handle a failed validation attempt.
    * This method is called when validation fails.
    * Logs failed attempts and throws validation exception.
    * @param \Illuminate\Validation\Validator $validator
    * @return void
    *
    */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'status'  => 'error',
            'message' => 'Validation failed.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
