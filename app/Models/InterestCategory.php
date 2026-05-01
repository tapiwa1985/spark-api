<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['interest_category_name'])]
class InterestCategory extends Model
{
    /** @use HasFactory<\Database\Factories\InterestCategoryFactory> */
    use HasFactory;

    public function interests(): HasMany
    {
        return $this->hasMany(Interest::class);
    }
}
