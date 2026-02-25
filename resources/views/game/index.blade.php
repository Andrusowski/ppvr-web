@extends('layouts.main')

@section('content')
    <p class="uk-text-lead uk-text-center">
        <span class="reddit">/r/osugame</span>dle
    </p>
    <p class="uk-text-center uk-text-muted">
        Which scorepost has the higher score? Pick correctly 10 times in a row to win!
    </p>

    @if($noGame)
        <div class="uk-text-center uk-margin-large-top">
            <div class="uk-card uk-card-default uk-card-body" style="max-width: 500px; margin: 0 auto;">
                <h2 class="uk-card-title">No Game Available</h2>
                <p>Today's game has not been created yet.</p>
                <p class="uk-text-muted">Please check back soon!</p>
            </div>
        </div>
    @else
        <game-widget
            :game-data='@json($gameData)'
            validate-url="{{ url('/game/validate') }}"
            auth-me-url="{{ route('auth.me') }}"
            login-url="{{ route('auth.osu') }}"
            logout-url="{{ route('logout') }}"
            sync-url="{{ route('game.stats.sync') }}"
            initial-sync-url="{{ route('game.stats.initial-sync') }}"
            :auth-success="{{ session('osu_auth_success') ? 'true' : 'false' }}"
            :is-new-user="{{ session('is_new_user') ? 'true' : 'false' }}"
            csrf-token="{{ csrf_token() }}"
        ></game-widget>
    @endif
@endsection
