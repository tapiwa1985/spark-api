<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class BlockUserRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'blocked_user_id' => ['required', 'integer']
        ];
    }
}
