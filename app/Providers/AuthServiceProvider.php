<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

//自己写的授权策略 需要在 AuthServiceProvider 中对授权策略进行配置才能正常使用
use App\Models\User;
use App\Models\Status;
use App\Policies\UserPolicy;
use App\Policies\StatusPolicy;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        //用户模型 User 指定授权策略 UserPolicy
         User::class  => UserPolicy::class,
         Status::class  => StatusPolicy::class,
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);

        //
    }
}
