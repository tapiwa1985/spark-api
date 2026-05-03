<?php

namespace App\Http\Requests;

use App\Rules\UniqueLike;
use Illuminate\Contracts\Validation\ValidationRule;

class LikeUserRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'liked_user_id' => [
                'required',
                'integer',
                'exists:users,id',
                new UniqueLike(auth()->id())
            ]
        ];
    }
}
