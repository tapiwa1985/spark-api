<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['user_profile_id', 'image_url', 'caption', 'display_order', 'is_display'])]
/**
 * Gallery photo for a {@see UserProfile}; `image_url` points at object storage; supports soft delete.
 */
class ProfileImage extends Model
{
    /** @use HasFactory<\Database\Factories\ProfileImageFactory> */
    use HasFactory;
    use SoftDeletes;
}
