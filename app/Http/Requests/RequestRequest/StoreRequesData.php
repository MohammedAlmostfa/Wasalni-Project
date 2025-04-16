<?php

namespace App\Http\Requests\RequestRequest;

use App\Rules\CheckImage;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequesData extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'about_user' => 'required|string|max:500',
            'car_type' => 'required|string|max:50',

            'User_image' => ['required', 'image', new CheckImage],


            'car_images' => ['required', 'array'],
            'car_images.*' => ['required', 'image', new CheckImage],
        ];
    }
}
