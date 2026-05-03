<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['user_id', 'matched_user_id'])]
class UserMatch extends Model
{
    /** @use HasFactory<\Database\Factories\UserMatchFactory> */
    use HasFactory;
}
