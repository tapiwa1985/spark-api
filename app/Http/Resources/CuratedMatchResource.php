<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API view model for a curated match candidate.
 */
class CuratedMatchResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->id,
            'rank_score' => (float) $this->rank_score,
            'curated_matches_window_id' => (int) $this->curated_matches_window_id,
            'user_id' => (int) $this->user_id,
            'profile' => $this->when(
                $this->user !== null && $this->user->userProfile !== null,
                fn () => new UserProfileResource($this->user->userProfile)
            ),
        ];
    }
}
