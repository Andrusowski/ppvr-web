@extends('layouts.main')

@section('content')


    <h1 class="uk-heading-medium uk-text-center nunito">PPV<span style="color: #ff4500;">R</span></h1>
    <p class="uk-text-center">
        Alternative osu! ranking based on posts from /r/osugame
    </p>

    <br><br>

    <h5 class="uk-text-center">Select Ranking</h5>

    <div class="uk-column-1-2@m">
        <a class="uk-button uk-button-primary uk-width-1-1 uk-margin-small-bottom" href="{{ url('/ranking/player/') }}">Player</a>

        <table class="uk-table uk-table-small uk-table-divider uk-table-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Posts</th>
                    <th>Score</th>
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

        <a class="uk-button uk-button-primary uk-width-1-1 uk-margin-small-bottom" href="{{ url('/ranking/author/') }}">Author</a>

        <table class="uk-table uk-table-small uk-table-divider uk-table-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Posts</th>
                    <th>Score</th>
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
    <br><br>


    @if ($top_comment != '')
        <blockquote class="uk-text-center">
          <p class="mb-0">{!! html_entity_decode($top_comment) !!}</p>
          <footer class="blockquote-footer"><cite title="Source Title">{{ $top_comment_author }}</cite></footer>
        </blockquote>
        <br><br>
    @endif

    <h5 class="uk-text-center">Newest Posts</h5>

    <table class="uk-table uk-table-small uk-table-divider uk-table-middle">
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
    <br>

    <div class="uk-text-center">
        made by Andrus
        <a href="https://discordapp.com/users/86760014068355072"><i class="fab fa-discord link text-secondary"></i></a>
        <a href="https://github.com/Andrusowski"><i class="fab fa-github link text-secondary"></i></a>
    </div>

@endsection
