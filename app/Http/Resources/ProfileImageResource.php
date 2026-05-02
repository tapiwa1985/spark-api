<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * JSON for a single {@see \App\Models\ProfileImage}; delegates to the model’s array form so all fillable / visible fields follow Eloquent’s output.
 */
class ProfileImageResource extends JsonResource
{
    /**
     * @return array<string, mixed> Default model serialization (parent) for a profile image record.
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
