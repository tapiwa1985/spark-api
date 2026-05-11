<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['user_id', 'blocked_user_id'])]
class BlockedUser extends Model
{
    /** @use HasFactory<\Database\Factories\BlockedUserFactory> */
    use HasFactory;
}
