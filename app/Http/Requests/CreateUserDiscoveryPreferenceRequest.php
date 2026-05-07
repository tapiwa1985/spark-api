<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class CreateUserDiscoveryPreferenceRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'min_age' => ['required', 'integer', 'min:18'],
            'max_distance_radius_km' => ['required', 'integer'],
            'gender' => ['required', 'in:male,female,both'],
            'interestIds' => ['array', 'min:1'],
            'languageIds' => ['array', 'min:1'],
            'industryIds' => ['array', 'min:1']
        ];
    }
}
