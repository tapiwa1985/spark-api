<?php

namespace App\Rules;

use Closure;
use App\Models\Like;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class UniqueLike implements ValidationRule
{
    /**
     * @param int
     */
    protected int $userId;

    /**
     * @param int
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $exists = Like::where('user_id', $this->userId)
            ->where('liked_user_id', (int)$value)
            ->first();

        if ($exists) {
            $fail('You have already liked this user.');
        }
    }
}
