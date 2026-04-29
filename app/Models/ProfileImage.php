<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_profile_id', 'image_url', 'caption', 'display_order', 'is_display'])]
class ProfileImage extends Model
{
    /** @use HasFactory<\Database\Factories\ProfileImageFactory> */
    use HasFactory;
}
