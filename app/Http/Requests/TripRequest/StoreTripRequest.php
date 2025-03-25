<?php

namespace App\Http\Requests\TripRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTripRequest extends FormRequest
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

            'description' => 'required|string|max:100',
            'trip_start' => 'required|date_format:d/m/Y h:i A|after:now',
            'from' => 'required|exists:cities,id',
            'to' => 'required|exists:cities,id|different:from',
            'status' => 'required|string',
            'seat_price' => 'required|integer|min:0',
            'available_seats' => 'required|integer|min:0',

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
