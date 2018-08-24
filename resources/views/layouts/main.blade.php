<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://bootswatch.com/4/litera/bootstrap.min.css">

    <!-- my CSS -->
    <link rel="stylesheet" href="{!! asset('css/app.css') !!}">

    <title>PPVR</title>
  </head>
  <body>
    <div id="particles-js"></div>
    <div id="app">
      <div class="container">
        <nav class="navbar">
          <a class="navbar-brand">PPVR</a>
          <form class="form-inline">
            <div class="input-group">
              <div class="input-group-prepend">
                <div class="input-group-text" id="btnGroupAddon2">@</div>
              </div>
              <input type="text" class="form-control" placeholder="Player Search" aria-label="Input group example" aria-describedby="btnGroupAddon2">
            </div>
          </form>
        </nav>
        @yield('content')
      </div>
    </div>

    <script type="text/javascript" src="{!! asset('js/app.js') !!}" defer></script>
  </body>
</html>
