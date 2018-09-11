<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://bootswatch.com/4/litera/bootstrap.min.css">

    <!-- my CSS -->
    <link rel="stylesheet" href="{!! asset('css/app.css') !!}" type="text/css">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">

    <title>PPVR</title>
  </head>
  <body>
    <div id="particles-js"></div>
    <div id="app">
      <div class="container">
        <nav class="navbar">
          <a class="navbar-brand text-body" href="{{url('/')}}">PPV<span class="reddit">R</span></a>
          <form class="form-inline">
            <div class="input-group">
              <div class="input-group-prepend">
                <div class="input-group-text" id="btnGroupAddon2">@</div>
              </div>
              <input type="text" class="form-control" placeholder="Player Search" aria-describedby="btnGroupAddon2">
            </div>
          </form>
        </nav>
        <br><br>
        @yield('content')
      </div>
    </div>

    <script type="text/javascript">
        var base_url = {!! json_encode(url('/')) !!}
    </script>
    <script type="text/javascript" src="{!! asset('js/app.js') !!}" defer></script>
  </body>
</html>
