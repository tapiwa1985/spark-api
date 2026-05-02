<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class AddLanguagesRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'language_ids' => ['required', 'array'],
            'language_ids.*' => 'exists:languages,id',
        ];
    }
}
