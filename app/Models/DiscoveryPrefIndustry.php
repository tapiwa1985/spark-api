<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['industry_id','user_discovery_preference_id'])]
class DiscoveryPrefIndustry extends Model
{
    /** @use HasFactory<\Database\Factories\DiscoveryPrefIndustryFactory> */
    use HasFactory;
}
