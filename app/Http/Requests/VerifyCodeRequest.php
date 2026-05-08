<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class VerifyCodeRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'mobile_phone' => ['required', 'phone:ZA'],
            'verification_code' => ['required', 'string', 'min:4', 'max:4']
        ];
    }
}
