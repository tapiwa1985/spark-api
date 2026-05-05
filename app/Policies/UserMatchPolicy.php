<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserMatch;

class UserMatchPolicy
{
    public function getMessages(User $user, UserMatch $userMatch): bool
    {
        $userIds = $this->getUserIds($user, $userMatch);

        return in_array($user->id, $userIds);
    }

    public function unmatchUser(User $user, UserMatch $userMatch): bool
    {
        $userIds = $this->getUserIds($user, $userMatch);

        return in_array($user->id, $userIds);
    }

    private function getUserIds(User $user, UserMatch $userMatch): array
    {
        $users = [];

         array_push($users, $userMatch->user_id);
         array_push($users, $userMatch->matched_user_id);

         return $users;
    }
}
