<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'min_age', 'max_age','max_distance_radius_km', 'gender', 'verified_only'])]
class UserDiscoveryPreference extends Model
{
    /** @use HasFactory<\Database\Factories\UserDiscoveryPreferenceFactory> */
    use HasFactory;

    public function discoveryPrefLanguages(): HasMany
    {
        return $this->hasMany(DiscoveryPrefLanguage::class);
    }
}
