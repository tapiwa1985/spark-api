<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class UserDiscoveryPreference
 *
 * Represents a user's discovery preference settings for matching and filtering other users.
 * Stores criteria such as age range, distance radius, gender preference, and verification status.
 *
 * @package App\Models
 *
 * @property int $id
 * @property int $user_id The ID of the user these preferences belong to
 * @property int|null $min_age Minimum age of users to discover
 * @property int|null $max_age Maximum age of users to discover
 * @property float|null $max_distance_radius_km Maximum distance in kilometers for discovering users
 * @property string|null $gender Gender preference for discovery ('male', 'female', 'other', or null for any)
 * @property bool|null $verified_only Whether to only show verified users
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
#[Fillable(['user_id', 'min_age', 'max_age','max_distance_radius_km', 'gender', 'verified_only'])]
class UserDiscoveryPreference extends Model
{
    /** @use HasFactory<\Database\Factories\UserDiscoveryPreferenceFactory> */
    use HasFactory;

    /**
     * Get the languages associated with this discovery preference.
     * Defines which languages the user wants to discover.
     *
     * @return HasMany<DiscoveryPrefLanguage>
     */
    public function discoveryPrefLanguages(): HasMany
    {
        return $this->hasMany(DiscoveryPrefLanguage::class);
    }

    /**
     * Get the interests associated with this discovery preference.
     * Defines which interests the user wants to discover in others.
     *
     * @return HasMany<Interest>
     */
    public function discoveryPrefInterests(): HasMany
    {
        return $this->hasMany(DiscoveryPrefInterest::class);
    }

    /**
     * Get the industries associated with this discovery preference.
     * Defines which industries the user wants to discover.
     * Note: Returns Language model relation - may need verification if this is correct.
     *
     * @return HasMany<Language>
     */
    public function discoveryPrefIndustries(): HasMany
    {
        return $this->hasMany(DiscoveryPrefIndustry::class);
    }
}
