<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\User;
use Auth;
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
        'password' => 'required'
      ]);
      $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => bcrypt($request->password),
      ]);
      Auth::login($user);
      session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
      return redirect()->route('users.show', [$user]);
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
        return view('users.show', compact('user'));
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
}
