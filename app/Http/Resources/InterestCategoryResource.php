<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Category header plus nested `interests` relation for discovery UI blocks.
 */
class InterestCategoryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int)$this->id,
            'icon' => $this->icon,
            'interest_category_name' => $this->interest_category_name,
            'interests' => $this->interests,
        ];
    }
}
