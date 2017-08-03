<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container-fluid">
    <div class="col-md-offset-1 col-md-10">
      <div id="navbar" class="navbar-collapse collapse">
        <div class="navbar-header">
          <a href="/"  id="logo">爱圈内定向越野管理平台</a>
        </div>
        <ul class="nav navbar-nav navbar-left">
            <li><a href="#">社区</a></li>
          <li><a href="#">问答</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
          @if (Auth::check())
            <li><a href="{{ route('users.index') }}">用户列表</a></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                {{ Auth::user()->name }} <b class="caret"></b>
              </a>
              <ul class="dropdown-menu">
                <li><a href="{{ route('users.show', Auth::user()->id) }}">个人中心</a></li>
                <li><a href="{{ route('users.edit', Auth::user()->id) }}">编辑资料</a></li>
                <li class="divider"></li>
                <li>
                  <a id="logout" href="#">
                    <form action="{{ route('logout') }}" method="POST">
                      {{ csrf_field() }}
                      {{ method_field('DELETE') }}
                      <button class="btn btn-block btn-danger" type="submit" name="button">退出</button>
                    </form>
                  </a>
                </li>
              </ul>
            </li>
          @else
            <li><a href="{{ route('help') }}">帮助</a></li>
            <li><a href="{{ route('login') }}">登录</a></li>
          @endif
        </ul>
          <form id="searching" class="nav navbar-form navbar-right" method="post">
            <input type="text"  class="form-control"  placeholder="search..."  >
          </form>
      </div>
    </div>
  </div>
</nav>
