<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['rank_score', 'user_id', 'curated_matches_window_id'])]
class CuratedMatch extends Model
{
    /** @use HasFactory<\Database\Factories\CuratedMatchFactory> */
    use HasFactory;
}
