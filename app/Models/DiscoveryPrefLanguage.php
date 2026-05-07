<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['language_id','user_discovery_preference_id'])]
class DiscoveryPrefLanguage extends Model
{
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
