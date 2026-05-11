<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Policies\ChatMessagePolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['sender_id', 'user_match_id', 'message', 'read_at'])]
#[UsePolicy(ChatMessagePolicy::class)]
class ChatMessage extends Model
{
    /** @use HasFactory<\Database\Factories\ChatMessageFactory> */
    use HasFactory;
    use SoftDeletes;

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function userMatch(): BelongsTo
    {
        return $this->belongsTo(UserMatch::class, 'user_match_id');
    }
}
