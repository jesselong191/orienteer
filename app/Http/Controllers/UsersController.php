<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\User;
use Auth,Mail;
class UsersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', [
            'only' => ['edit', 'update']
        ]);
        //只让未登录用户访问注册页面：
        $this->middleware('guest', [
           'only' => ['create']
       ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::paginate(20);
       return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
           return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $this->validate($request, [
          'name' => 'required|max:50',
          'email' => 'required|email|unique:users|max:255',
          'password' => 'required|confirmed|min:6'
      ]);

      $user = User::create([
          'name' => $request->name,
          'email' => $request->email,
          'password' => bcrypt($request->password),
      ]);

      $this->sendEmailConfirmationTo($user);
      session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
      return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $user = User::findOrFail($id);
        //获取用户动态
        $statuses = $user->statuses()
                           ->orderBy('created_at', 'desc')
                           ->paginate(10);
        //compact 方法可以同时接收多个参数，在上面代码我们将用户数据 $user 和 动态数据 $statuses 同时传递给用户个人页面的视图上。
        return view('users.show', compact('user', 'statuses'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        //用户授权验证数据
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      $this->validate($request, [
           'name' => 'required|max:50',
           'password' => 'required|confirmed|min:6'
       ]);

       $user = User::findOrFail($id);
       $user->update([
           'name' => $request->name,
           'password' => bcrypt($request->password),
       ]);

       return redirect()->route('users.show', $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $user = User::findOrFail($id);
      $user->delete();
      session()->flash('success', '成功删除用户！');
      return back();
    }

//发送激活邮件
    protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $from = 'aufree@yousails.com';
        $name = 'Aufree';
        $to = $user->email;
        $subject = "感谢注册 Sample 应用！请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
            $message->from($from, $name)->to($to)->subject($subject);
        });
    }

    //完成激活
    public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }

    //获取关注的人
    public function followings($id)
    {
        $user = User::findOrFail($id);
        $users = $user->followings()->paginate(20);
        $title = '关注的人';
        return view('users.show_follow', compact('users', 'title'));
    }

    //获取我的粉丝
    public function followers($id)
    {
        $user = User::findOrFail($id);
        $users = $user->followers()->paginate(20);
        $title = '粉丝';
        return view('users.show_follow', compact('users', 'title'));
    }
}
