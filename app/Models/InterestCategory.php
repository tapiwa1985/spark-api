<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['interest_category_name'])]
/**
 * Groups {@see Interest} rows for UI sections and optional icon metadata.
 */
class InterestCategory extends Model
{
    /** @use HasFactory<\Database\Factories\InterestCategoryFactory> */
    use HasFactory;

    /**
     * Child interest labels under this category.
     */
    public function interests(): HasMany
    {
        return $this->hasMany(Interest::class);
    }
}
