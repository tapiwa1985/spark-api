<?php

namespace App\Http\Resources;

use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * JSON shape for a {@see \App\Models\UserProfile}, including nested user, industry, optional interests, gallery images, and GeoJSON-like location when set.
 */
class UserProfileResource extends JsonResource
{
    /**
     * @return array<string, mixed> Attributes for the API “data” envelope; location is encoded via Magellan’s GeoJSON generator when present.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int)$this->id,
            'bio' => $this->bio,
            'dob' => (string)$this->dob,
            'gender' => $this->gender,
            'job_title' => $this->job_title,
            'location' => $this->when(
                $this->location !== null,
                fn () => $this->location instanceof Point
                    ? json_decode(json_encode($this->location), true)
                    : $this->location
            ),
            'created_at' => (string)$this->created_at,
            'updated_at' => (string)$this->updated_at,
            'user' => new UserResource($this->user),
            'industry' => new IndustryResource($this->industry),
            'interests' => InterestResource::collection($this->whenLoaded('interests')),
            'languages' => new LanguageResourceCollection($this->whenLoaded('languages')),
            'profile_images' => new ProfileImageResourceCollection($this->profileImages),
        ];
    }
}
