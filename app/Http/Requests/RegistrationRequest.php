<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rules\Date;
use Carbon\Carbon;

/**
 * Sign-up fields including password confirmation and minimum age (`dob` before 18 years ago).
 */
class RegistrationRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->numbers()
                    ->symbols()
                    ->mixedCase()
            ],
            'dob' => [
                'required',
                'date',
                (new Date())->beforeToday(),
                'before:' . Carbon::now()->subYears(18)->format('Y-m-d'),
            ],
        ];
    }
}
