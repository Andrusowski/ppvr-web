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
    <div id="app">
        <div class="container">
            <nav class="navbar">
                <!-- menu -->
                <div class="dropdown">
                    <a class="navbar-brand text-body dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="{{url('/')}}">
                        PPV<span class="reddit">R</span>
                    </a>

                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="{{url('/')}}">Home</a>
                        <!--<a class="dropdown-item" href="{{url('/about')}}">About</a>-->
                        <a class="dropdown-item" href="{{url('/changelog')}}">Changelog</a>
                    </div>
                </div>

                <!-- search -->
                <form class="form-inline searchbar" action="{{url('search/')}}">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text" id="btnGroupAddon2"><i data-eva="search-outline"></i></div>
                        </div>
                        <input type="text" class="form-control" name="name" placeholder="Player Search" aria-describedby="btnGroupAddon2">
                    </div>
                </form>
            </nav>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> {{$errors->first()}}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            <br><br>
            @yield('content')
        </div>
    </div>

    <script type="text/javascript">
    var base_url = {!! json_encode(url('/')) !!}
    </script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script type="text/javascript" src="{!! asset('js/app.js') !!}" defer></script>
</body>
</html>
