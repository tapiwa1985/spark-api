<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['industry_id','user_discovery_preference_id'])]
class DiscoveryPrefIndustry extends Model
{
    /** @use HasFactory<\Database\Factories\DiscoveryPrefIndustryFactory> */
    use HasFactory;

    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }
}
