<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserMatch;

class UserMatchPolicy
{
    public function getMessages(User $user, UserMatch $userMatch): bool
    {
         $users = [];

         array_push($users, $userMatch->user_id);
         array_push($users, $userMatch->matched_user_id);

         return in_array($user->id, $users);
    }
}
