<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['user_id', 'rejected_user_id', 'expires_at'])]
class UserRejection extends Model
{
    /** @use HasFactory<\Database\Factories\UserRejectionFactory> */
    use HasFactory;

    const USER_REJECTION_TYPE_SOFT = 'SOFT';
    const USER_REJECTION_TYPE_HARD = 'HARD';
}
