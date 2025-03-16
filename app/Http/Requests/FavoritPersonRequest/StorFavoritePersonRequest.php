<?php

namespace App\Http\Requests\FavoritPersonRequest;

use Illuminate\Foundation\Http\FormRequest;

class StorFavoritePersonRequest extends FormRequest
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
            'favorite_user_id' => 'required|exists:users,id|unique:favorite_people,favorite_user_id,NULL,id,user_id,' . auth()->id(),
        ];

    }
}
