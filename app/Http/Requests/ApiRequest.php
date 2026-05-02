<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Base API {@see FormRequest} that returns validation errors as JSON (`422`) instead of redirecting.
 */
class ApiRequest extends FormRequest
{
    /**
     * @throws HttpResponseException Always; carries `errors` keyed by field.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json(
            [
                'errors' => $validator->errors()
            ],
            422
        ));
    }
}
