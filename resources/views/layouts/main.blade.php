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

    <!-- favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('/site.webmanifest') }}">
    <link rel="mask-icon" href="{{ asset('/safari-pinned-tab.svg') }}" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#ff4500">
    <meta name="theme-color" content="#ffffff">

    <!-- miscellaneous -->
    <meta name="theme-color" content="#ff4500"/>
    <title>PPVR</title>
</head>
<body>
    <div id="particles-js"></div>
    <div id="app">
        <div class="container">
            <nav class="navbar">
                <a class="navbar-brand text-body" href="{{url('/')}}">PPV<span class="reddit">R</span></a>
                <form class="form-inline searchbar" action="{{url('search/')}}">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text" id="btnGroupAddon2"><i class="fas fa-user"></i></div>
                        </div>
                        <input type="text" class="form-control" name="name" placeholder="Player Search" aria-describedby="btnGroupAddon2">
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
