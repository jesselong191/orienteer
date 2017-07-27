<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;
use App\Models\Status;
//用户动态授权策略来对用户进行授权删除的操作，只有当被删除的动态作者为当前用户或者为管理员时，授权才能通过。
class StatusPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function destroy(User $user, Status $status)
    {
        return $user->id === $status->user_id || $user -> is_admin === 1;
    }
}
