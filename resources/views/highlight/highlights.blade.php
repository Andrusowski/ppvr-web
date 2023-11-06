<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- UIkit CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.2.0/css/uikit.min.css" />

    <!-- my CSS -->
    <link rel="stylesheet" href="{!! mix('/css/app.css') !!}" type="text/css">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
</head>
<body>
<div class="uk-padding uk-light highlights highlights-bg" id="app">
    <h1>/r/<span class="osu">osu</span>game Scorepost Recap of <span class="osu">{{ $date }}</span></h1>
    <div class="uk-grid">
        <div class="uk-width-2-3">
            <h2>Top players</h2>
            @php($rank = 0)
            @foreach($top_players as $top_player)
                <div class="uk-card uk-card-body uk-card-secondary uk-margin">
                    <h3>#{{++$rank}} {{ $top_player->name }}</h3>
                    <div>
                        <span class="uk-margin-right">Total Score: {{ $top_player->score }}</span>
                        <span class="uk-margin-right">Average Score: {{ round($top_player->avg_score) }}</span>
                        <span>Posts: {{ $top_player->posts }}</span>
                    </div>
                    @foreach($top_posts_per_player[$top_player->name] as $post)
                        <div class="uk-flex uk-margin-small uk-margin-left">
                            <span class="uk-badge">{{$post->score}}</span>
                            <p class="uk-padding-remove uk-margin-remove highlights-maptitle-container">
                                <span class="highlights-maptitle">{{ $post->map_artist }} - {{ $post->map_title }}</span>&nbsp;[{{ $post->map_diff}}]
                            </p>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <div class="uk-width-1-3">
            <h2>Top posters</h2>
            @php($rank_author = 0)
            @foreach($top_authors as $top_author)
                <div class="uk-card uk-card-body uk-card-secondary uk-margin ">
                    <h5>#{{++$rank_author}} {{ $top_author->author }}</h5>
                    <div>
                        <span class="uk-margin-right">Total Score: {{ $top_author->score }}</span>
                        <span>Posts: {{ $top_author->posts }}</span>
                    </div>
                </div>
            @endforeach

            <h2>Stats</h2>
            <div class="uk-card uk-card-body uk-card-secondary uk-margin ">
                <h5 class="uk-margin-small">Total Scoreposts</h5>
                <p class="uk-margin-small">{{ $posts_count }}</p>

                <h5 class="uk-margin-small uk-margin-medium-top">Total Score</h5>
                <p class="uk-margin-small">{{ $posts_total_score }}</p>

                <h5 class="uk-margin-small uk-margin-medium-top">Unique Players</h5>
                <p class="uk-margin-small">{{ $unique_players->count }}</p>

                <h5 class="uk-margin-small uk-margin-medium-top">Posts per Day</h5>
                <bar-chart
                    posts="{{ $score_per_day }}"
                    name="scoreHistory"
                    value-index="score_daily"
                    height="170"
                    v-bind:y-axes-display="true"
                >
                </bar-chart>
            </div>
        </div>
    </div>

    <h2>Top Scoreposts</h2>
    <div class="uk-card uk-card-body uk-card-secondary uk-margin">
        @foreach($top_posts as $post)
            <p class="uk-margin-small"><span class="uk-badge uk-margin-small-right">{{$post->score}}</span> {{ $post->player_name }} | {{ $post->map_artist }} - {{ $post->map_title }} [{{ $post->map_diff}}]</p>
        @endforeach
    </div>

    <div class="uk-align-right footer">
        <p>Powered by PPv<span class="reddit">R</span>.andrus.io</p>
    </div>

    <div class="uk-hidden">
        <pre>{{ $text }}</pre>
    </div>
</div>

<script type="text/javascript" src="{!! asset('js/app.js') !!}" defer></script>

</body>
