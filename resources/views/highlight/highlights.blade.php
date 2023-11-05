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
<div class="uk-padding uk-light highlights highlights-bg">
    <h1 class="uk-heading-medium">/r/<span class="osu">osu</span>game Score Post Recap of <span class="osu">{{ $date }}</span></h1>
    <div class="uk-grid">
        <div class="uk-width-3-5">
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
                    @foreach($top_post_per_player[$top_player->name] as $post)
                        <p class="uk-margin-small"><span class="uk-badge uk-margin-small-right">{{$post->score}}</span> {{ $post->map_artist }} - {{ $post->map_title }} [{{ $post->map_diff}}]</p>
                    @endforeach
                </div>
            @endforeach
        </div>

        <div class="uk-width-2-5">
            <h2>Top posters</h2>
            @foreach($top_authors as $top_author)
                <div class="uk-card uk-card-body uk-card-secondary uk-margin ">
                    <h4>{{ $top_author->author }}</h4>
                    <div>
                        <span class="uk-margin-right">Total Score: {{ $top_author->score }}</span>
                        <span>Posts: {{ $top_author->posts }}</span>
                    </div>
                </div>
            @endforeach

            <h2>Stats</h2>
            <div class="uk-card uk-card-body uk-card-secondary uk-margin ">
                <h5>Total Scoreposts</h5>
                <p>{{ $posts_count }}</p>

                <h5>Total Score</h5>
                <p>{{ $posts_total_score }}</p>
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
</div>

</body>
