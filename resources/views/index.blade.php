@extends('layouts.main')

@section('content')


    <h1 class="uk-heading-medium uk-text-center">PPV<span style="color: #ff4500;">R</span></h1>
    <p class="uk-text-center">
        Alternative osu! ranking based on posts from /r/osugame
    </p>

    <br><br>

    <p class="uk-text-center uk-text-lead">Select Ranking</p>

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
          <a class="uk-text-secondary" href="{{ $top_comment_link }}">{!! html_entity_decode($top_comment) !!}</a>
          <footer><cite>{{ $top_comment_author }}</cite></footer>
        </blockquote>
        <br><br>
    @endif

    <p class="uk-text-center">Newest Posts</p>

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
                        <td class="uk-text-nowrap">{{ round((time() - $post->created_utc) / 60) }}min ago</td>
                    @else
                        <td class="uk-text-nowrap">{{ round((time() - $post->created_utc) / 60 / 60) }}h ago</td>
                    @endif
                </tr>
                @endif

                @php ($i++)
            @endforeach
        </tbody>
    </table>
@endsection
