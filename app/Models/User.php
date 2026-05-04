<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'email', 'password', 'linkedin_id', 'linkedin_token', 'linkedin_refresh_token'])]
#[Hidden(['password', 'remember_token'])]
/**
 * Authenticatable account with optional LinkedIn OAuth fields and a single {@see UserProfile}.
 */
class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use Notifiable;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, mixed>
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * One extended dating profile row keyed by `users.id`.
     *
     * @return HasOne
     */
    public function userProfile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * A user has many received likes
     *
     * @return HasMany
     */
    public function receivedLikes(): HasMany
    {
        return $this->hasMany(Like::class, 'liked_user_id');
    }

    /**
     * A user has many likes
     *
     * @return HasMany
     */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class, 'user_id');
    }

    /**
     * A user has multiple matches
     *
     * @return HasMany
     */
    public function matches(): HasMany
    {
        return $this->hasMany(UserMatch::class, 'user_id');
    }
}
