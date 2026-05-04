<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'matched_user_id'])]
class UserMatch extends Model
{
    /** @use HasFactory<\Database\Factories\UserMatchFactory> */
    use HasFactory;

    const USER_MATCH_STATUS_ACTIVE = 'ACTIVE';

    public function chatMessages(): HasMany 
    {
        return $this->hasMany(ChatMessage::class);
    }
}
