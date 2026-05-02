<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Policies\UserProfilePolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Clickbar\Magellan\Data\Geometries\Point;

/**
 * Extended profile for a {@see User}: demographics, optional industry, interests pivot, gallery images, and a PostGIS geography {@see Point} for the `location` attribute.
 */
#[Fillable(['user_id', 'bio', 'dob', 'gender', 'job_title', 'industry_id', 'location'])]
#[UsePolicy(UserProfilePolicy::class)]
class UserProfile extends Model
{
    /** @use HasFactory<\Database\Factories\UserProfileFactory> */
    use HasFactory;

    /**
     * {@inheritdoc}
     *
     * @return array<string, class-string|string>
     */
    protected function casts(): array
    {
        return [
            'location' => Point::class,
        ];
    }

    /**
     * Owning user account (one profile per user).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Ordered gallery images stored for this profile (each row holds URL on object storage and display flags).
     */
    public function profileImages(): HasMany
    {
        return $this->hasMany(ProfileImage::class);
    }

    /**
     * Optional industry taxonomy link for job context.
     */
    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }

    /**
     * Interest tags attached through the `interest_user_profile` pivot table.
     */
    public function interests(): BelongsToMany
    {
        return $this->belongsToMany(Interest::class, 'interest_user_profile', 'user_profile_id', 'interest_id');
    }

    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class, 'language_user_profile', 'user_profile_id', 'language_id');
    }
}
