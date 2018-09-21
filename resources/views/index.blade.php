@extends('layouts.main')

@section('content')

    <div class="text-center welcometext">
        <h1 class="display-3">PPV<span style="color: #ff4500;">R</span></h1>
        <p class="lead">
            Alternative osu! ranking based on posts from /r/osugame
        </p>
    </div>
    <br><br>

    <h5 class="text-center">Select Ranking</h5>

    <div class="row">

        <div class="col-md-3">
        </div>

        <div class="col-md-3 pt-2 pb-2">
            <a class="btn btn-primary btn-lg btn-block" href="{{ url('/ranking/player/') }}">Player</a>

            <table class="table table-sm table-hover mt-4">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th class="text-nowrap">
                            Posts
                        </th>
                        <th class="text-nowrap">
                            Score
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($posts_players as $post)
                        <tr>
                            <td>{{ ++$rank_players }}</td>
                            <td><a href="{{ url('/player/'.$post->player_id) }}" class="text-body" style="text-decoration: none">{{ $post->name }}</a></td>
                            <td>{{ $post->posts }}</td>
                            <td>{{ round($post->score) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="col-md-3 pt-2 pb-2">
            <a class="btn btn-primary btn-lg btn-block" href="{{ url('/ranking/author/') }}">Author</a>

            <table class="table table-sm table-hover mt-4">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th class="text-nowrap">
                            Posts
                        </th>
                        <th class="text-nowrap">
                            Score
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($posts_authors as $post)
                        <tr>
                            <td>{{ ++$rank_authors }}</td>
                            <td><a href="{{ url('/author/'.$post->author) }}" class="text-body" style="text-decoration: none">{{ $post->author }}</a></td>
                            <td>{{ $post->posts }}</td>
                            <td>{{ round($post->score) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="col-md-3">
        </div>

    </div>

    <div class="row pt-5 pb-5">
        <div class="col-md-2">
        </div>

        <div class="col-md-8">
            @if ($top_comment != '')
                <blockquote class="blockquote text-center pb-5">
                  <p class="mb-0">{!! html_entity_decode($top_comment) !!}</p>
                  <footer class="blockquote-footer"><cite title="Source Title">{{ $top_comment_author }}</cite></footer>
                </blockquote>
            @endif
            <h5 class="text-center pt-3">Newest Posts</h5>

            <table class="table table-sm table-hover">
                <tbody>
                    @php ($i = 0)
                    @foreach($posts_new as $post)
                        @if ($i < 5)
                        <tr>
                            <td>
                                <a href="{{ url('/post/'.$post->id) }}" class="text-body link">
                                    {{ $post->name.' | '.$post->map_artist.' - '.$post->map_title.' ['.$post->map_diff.']' }}
                                </a>
                            </td>
                            @if (time() - $post->created_utc < (60 * 60))
                                <td>{{ round((time() - $post->created_utc) / 60) }} minutes ago</td>
                            @elseif (time() - $post->created_utc < (119 * 60))
                                <td>1 hour ago</td>
                            @else
                                <td>{{ round((time() - $post->created_utc) / 60 / 60) }} hours ago</td>
                            @endif
                        </tr>
                        @endif

                        @php ($i++)
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="col-md-2">
        </div>
    </div>

    <div class="text-center text-secondary small pt-5 pb-2">
        made by Andrus
        <a href="https://discordapp.com/users/86760014068355072"><i class="fab fa-discord link text-secondary"></i></a>
        <a href="https://github.com/Andrusowski"><i class="fab fa-github link text-secondary"></i></a>
    </div>

@endsection
