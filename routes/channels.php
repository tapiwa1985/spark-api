<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{matchId}', function ($user, $matchId) {
    return \App\Models\UserMatch::where('id', $matchId)
        ->where(function($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhere('matched_user_id', $user->id);
        })->exists();
});

