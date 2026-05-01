<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
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
            'bio' => $this->bio,
            'dob' => (string)$this->dob,
            'gender' => $this->gender,
            'job_title' => $this->job_title,
            'created_at' => (string)$this->created_at,
            'updated_at' => (string)$this->updated_at,
            'user' => new UserResource($this->user),
            'industry' => new IndustryResource($this->industry),
            'interests' => InterestResource::collection($this->whenLoaded('interests')),
            'profile_images' => new ProfileImageResourceCollection($this->profileImages),
        ];
    }
}
