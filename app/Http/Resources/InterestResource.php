<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Compact interest tag (`id`, `interest_name`) for nested lists under categories or profiles.
 */
class InterestResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->id,
            'interest_name' => $this->interest_name,
        ];
    }
}
