<?php

namespace App\Http\Requests\TripRequest;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class FilteringTripsData extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {

        if ($this->has('startTime')) {
            $this->merge([
                'startTime' => $this->convertTimeFormat($this->input('startTime'))
            ]);
        }
    }
    private function convertTimeFormat($time)
    {
        try {
            return Carbon::createFromFormat('g:ia', $time)->format('H:i:s');
        } catch (\Exception $e) {
            return $time;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [

           'startDate' => 'nullable|date',
            'startTime' => 'nullable|date_format:H:i:s',
            'from' => 'nullable|exists:cities,id',
            'to' => 'nullable|exists:cities,id|different:from',
            'status' => 'nullable|string',
            'seat_price' => 'nullable|integer|min:0',
            'available_seats' => 'nullable|integer|min:0',
            'order_desc'=>'nullable|string',
            "order_asc"=>'nullable|string',

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
