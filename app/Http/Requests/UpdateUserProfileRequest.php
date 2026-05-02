<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Partial profile update for job title, industry foreign key, and gender enumeration.
 */
class UpdateUserProfileRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'job_title' => ['required', 'string'],
            'industry_id' => ['required', 'exists:industries,id'],
            'gender' => ['required', 'string', 'in:male,female']
        ];
    }
}
