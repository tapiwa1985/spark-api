<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['rank_score', 'user_id', 'curated_matches_window_id'])]
class CuratedMatch extends Model
{
    /** @use HasFactory<\Database\Factories\CuratedMatchFactory> */
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function curatedMatchesWindow(): BelongsTo
    {
        return $this->belongsTo(CuratedMatchesWindow::class);
    }
}
