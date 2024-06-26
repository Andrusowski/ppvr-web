<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- UIkit CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.2.0/css/uikit.min.css" />

    <!-- my CSS -->
    <link rel="stylesheet" href="{!! mix('/css/app.css') !!}" type="text/css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">

    <!-- favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('/site.webmanifest') }}">
    <link rel="mask-icon" href="{{ asset('/safari-pinned-tab.svg') }}" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#ff4500">

    <!-- miscellaneous -->
    <meta name="theme-color" content="#ff4500"/>
    <title>PPvR</title>
</head>
<body>
    <div id="app">
        @if (config('motd.message'))
            <div class="uk-alert-{{ config('motd.type') }}" uk-alert>
                <div class="uk-container uk-container-small">
                    <p>{!! config('motd.message') !!}</p>
                </div>
            </div>
        @endif

        <!-- Player not found -->
        @if ($errors->any())
            <div class="uk-alert-warning" uk-alert>
                <div class="uk-container uk-container-small">
                    <a class="uk-alert-close" uk-close></a>
                    <strong>Error!</strong> {{$errors->first()}}
                </div>
            </div>
        @endif

        <div class="uk-container uk-container-small">
            <!-- Navbar -->
            <nav class="uk-navbar-container uk-navbar-transparent" uk-navbar>
                <div class="uk-navbar-left">
                    <button class="uk-navbar-toggle uk-hidden@s" type="button" data-uk-navbar-toggle-icon></button>
                    <div uk-dropdown>
                        <ul class="uk-nav uk-dropdown-nav">
                            <!-- menu -->
                            <li><a href="{{url('/stats')}}">Stats</a></li>
                            <li><a href="{{url('/faq')}}">FAQ</a></li>
                            <li><a href="{{url('/docs')}}">API</a></li>
                            <li><a href="https://github.com/Andrusowski/ppvr-web/blob/master/CHANGELOG.md">Changelog</a></li>
                        </ul>
                    </div>

                    <a class="uk-navbar-item uk-logo nunito" href="{{url('/')}}">
                        PPv<span class="reddit">R</span>
                    </a>

                    <ul class="uk-navbar-nav uk-visible@s">
                        <!-- menu -->
                        <li><a href="{{url('/stats')}}">Stats</a></li>
                        <li><a href="{{url('/faq')}}">FAQ</a></li>
                        <li><a href="{{url('/docs')}}">API</a></li>
                        <li><a href="https://github.com/Andrusowski/ppvr-web/blob/master/CHANGELOG.md">Changelog</a></li>
                    </ul>
                </div>


                <div class="uk-navbar-right">
                    <!-- search -->
                    <Search search-url="{{url('search/')}}"></Search>
                </div>
            </nav>

            @yield('content')

            <br>
        </div>

        <br><br>

        <p class="uk-text-center uk-margin-remove-bottom uk-padding-small" id="footertext">
            <span class="uk-text-meta">made by Andrus</span>
            <!--<a class="uk-margin-small-right uk-margin-small-left" href="https://osu.ppy.sh/users/2924006"><i class="fab fa-discord link text-secondary"></i></a>-->
            <a class="uk-margin-small-right uk-margin-small-left footer-link uk-text-meta" href="https://osu.ppy.sh/users/2924006" target="_blank" rel="noopener noreferrer">
                <object data="{{ URL::asset('/icons/osu-logo-white.svg') }} " type="image/svg+xml" class="link text-secondary footer-icon" title="osu! logo"></object>
            </a>
            <a class="uk-margin-small-right uk-margin-small-left footer-link uk-text-meta" href="https://discord.gg/HjtcbrEB" target="_blank" rel="noopener noreferrer">
                <i class="fab fa-discord link footer-link-fab"></i>
            </a>
            <a href="{{url('/legal/notice')}}" class="link text-secondary uk-margin-small-left uk-text-meta">Legal Notice</a>
            <a href="{{url('/legal/privacy')}}" class="link text-secondary uk-margin-small-left uk-text-meta">Privacy Policy</a>
        </p>
    </div>

    @yield('javascript')

    <script type="text/javascript">
        let base_url = {!! json_encode(url('/')) !!}
    </script>
    <script type="text/javascript" src="{!! mix('js/app.js') !!}" defer></script>

    <!-- UIkit JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.2.0/js/uikit.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.2.0/js/uikit-icons.min.js"></script>

    @env(['production'])
        <!-- Ackee -->
        <script async src="https://ackee.gymir.andrus.io/cooltrack.js" data-ackee-server="https://ackee.gymir.andrus.io" data-ackee-domain-id="{{ env('ACKEE_DOMAIN_ID') }}"></script>
    @endenv
</body>
</html>
