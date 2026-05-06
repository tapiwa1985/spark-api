<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['interest_id','user_discovery_preference_id'])]
class DiscoveryPrefInterest extends Model
{
    /** @use HasFactory<\Database\Factories\DiscoveryPrefInterestFactory> */
    use HasFactory;
}
