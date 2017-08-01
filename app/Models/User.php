<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

use Auth;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    public function gravatar($size = '100')
     {
         $hash = md5(strtolower(trim($this->attributes['email'])));
         return "http://www.gravatar.com/avatar/$hash?s=$size";
     }

     //boot 方法会在用户模型类完成初始化之后进行加载
     public static function boot()
     {
         parent::boot();

         static::creating(function ($user) {
             $user->activation_token = str_random(30);
         });
     }

     // 用户动态
     public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    //feed 方法，该方法将当前用户发布过的所有动态从数据库中取出，并根据创建时间来倒序排序。
    public function feed()
     {
       //通过 followings 方法取出所有关注用户的信息，再借助 pluck 方法将 id 进行分离并赋值给 user_ids；
       $user_ids = Auth::user()->followings->pluck('id')->toArray();
       //将当前用户的 id 加入到 user_ids 数组中；
       array_push($user_ids, Auth::user()->id);
       //使用 Laravel 提供的 查询构造器 whereIn 方法取出所有用户的微博动态并进行倒序排序；
       return Status::whereIn('user_id', $user_ids)
                            ->with('user')
                            ->orderBy('created_at', 'desc');
     }

     //followers 来获取粉丝关系列表
     public function followers()
     {
         return $this->belongsToMany(User::Class, 'followers', 'user_id', 'follower_id');
     }

     //followings 来获取用户关注人列表
     public function followings()
     {
         return $this->belongsToMany(User::Class, 'followers', 'follower_id', 'user_id');
     }

     //关注
     public function follow($user_ids)
    {
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->sync($user_ids, false);
    }

    //取消关注
    public function unfollow($user_ids)
    {
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->detach($user_ids);
    }

    //是否关注
    public function isFollowing($user_id)
    {
        return $this->followings->contains($user_id);
    }
}
