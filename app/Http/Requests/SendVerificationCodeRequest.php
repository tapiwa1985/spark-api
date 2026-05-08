<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class SendVerificationCodeRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'mobile_phone' => ['required', 'phone:ZA']
        ];
    }
}
