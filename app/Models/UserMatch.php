<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Policies\UserMatchPolicy;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\CascadeSoftDeletes;

#[Fillable(['user_id', 'matched_user_id'])]
#[UsePolicy(UserMatchPolicy::class)]
class UserMatch extends Model
{
    /** @use HasFactory<\Database\Factories\UserMatchFactory> */
    use HasFactory;
    use SoftDeletes;
    use CascadeSoftDeletes;

    const USER_MATCH_STATUS_ACTIVE = 'ACTIVE';
    const USER_MATCH_STATUS_UNMATCHED = 'UNMATCHED';

    protected $cascadeDeletes = ['chatMessages'];

    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'user_match_id');
    }
}
