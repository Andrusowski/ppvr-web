@extends('layouts.main')

@section('content')
    <p class="uk-text-lead uk-text-center">
        <span class="reddit">/r/osugame</span>dle
    </p>
    <p class="uk-text-center uk-text-muted">
        Which scorepost has the higher score? Pick correctly 10 times in a row to win!
    </p>

    <game-widget
        :game-data='@json($gameData)'
        validate-url="{{ url('/game/validate') }}"
    ></game-widget>
@endsection
