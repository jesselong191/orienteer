<!DOCTYPE html>
<html>
  <head>
    <title>@yield('title', '定向越野') - 爱圈内</title>
    <link rel="stylesheet" href="/css/app.css">
  </head>
  <body>
    @include('layouts._header')

    <div class="container-fluid">
      <div class="col-md-offset-1 col-md-10">
        @include('shared.message')
        @yield('content')
      </div>
      @include('layouts._footer')
    </div>

    <script src="/js/app.js"></script>
  </body>
</html>
