<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDiscoveryPreferenceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int)$this->id,
            'min_age' => (int)$this->min_age,
            'max_age' => (int)$this->max_age,
            'max_distance_radius_km' => $this->max_distance_radius_km,
            'gender' => $this->gender,
            'verified_only' => (bool)$this->verified_only,
            'interests' => new DiscoveryPrefInterestResourceCollection($this->discoveryPrefInterests),
            'languages' => new DiscoveryPrefLanguageResourceCollection($this->discoveryPrefLanguages),
            'industries' => new DiscoveryPrefIndustryResourceCollection($this->discoveryPrefIndustries),
        ];
    }
}
