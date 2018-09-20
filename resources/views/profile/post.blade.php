@extends('layouts.main')

@section('content')

    <h4 class="d-inline-block">
        {{ $player->name.' | '.$post->map_artist.' - '.$post->map_title.' ['.$post->map_diff.']' }}
    </h4>

    <p class="lead">
        posted
        @if (time() - $post->created_utc < (60 * 60))
            {{ round((time() - $post->created_utc) / 60) }} minutes ago
        @elseif (time() - $post->created_utc < (119 * 60))
            1 hour ago
        @elseif (time() - $post->created_utc < (24 * 60 * 60))
            {{ round((time() - $post->created_utc) / 60 / 60) }} hours ago
        @elseif (time() - $post->created_utc < (2 * 24 * 60 * 60))
            1 day ago
        @elseif (time() - $post->created_utc < (365 * 24 * 60 * 60))
            {{ round((time() - $post->created_utc) / 24 / 60 / 60) }} days ago
        @elseif (time() - $post->created_utc < (730 * 24 * 60 * 60))
            1 year ago
        @else
            {{ round((time() - $post->created_utc) / 365 / 24 / 60 / 60) }} years ago
        @endif
        by {{ $post->author }}
    </p>

    <div class="row pt-4">
        <div class="col-md-3">
            <div class="card">
                @if ($img != '')
                    <img class="card-img-top" src="{{ $img }}" alt="screenshot">
                @endif
                <div class="card-body">
                    <h5 class="card-title">Stats</h5>

                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td>Score:</td>
                                <td>{{ $post->score }}</td>
                            </tr>
                            <tr>
                                <td>Upvotes:</td>
                                <td>{{ $post->ups }}</td>
                            </tr>
                            <tr>
                                <td>Downvotes:</td>
                                <td>{{ $post->downs }}</td>
                            </tr>
                            <tr>
                                <td>Gold:</td>
                                <td>{{ $post->gilded }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <a class="btn btn-reddit btn-lg btn-block" href="{{ 'https://www.reddit.com/r/osugame/comments/'.$post->id }}">view on Reddit</a>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            @if ($top_comment != '')
                <blockquote class="blockquote text-center mt-5 mb-5">
                  <p class="mb-0">{!! html_entity_decode($top_comment) !!}</p>
                  <footer class="blockquote-footer"><cite title="Source Title">{{ $top_comment_author }}</cite></footer>
                </blockquote>
            @endif
            <h4 class="pt-2 pb-2">Top posts for the same map</h4>
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th></th>
                        <th>Played by</th>
                        <th>Posted by</th>
                        <th>Score</th>
                        <th>spicy</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($posts_other as $post)
                        <tr>
                            <td>
                                <a href="{{ url('/post/'.$post->id) }}" class="text-body" style="text-decoration: none"><i class="fas fa-link"></i></a>
                            </td>
                            <td>
                                <a href="{{ url('/player/'.$post->player_id) }}" class="text-body" style="text-decoration: none">
                                    {{ App\Player::find($post->player_id)->name }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ url('/author/'.$post->author) }}" class="text-body" style="text-decoration: none">
                                    {{ $post->author }}
                                </a>
                            </td>
                            <td>{{ round($post->score) }}</td>
                            <td>{{ ($post->downs == 0) ? 0 : round(($post->downs / $post->ups) * 100) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if (count($posts_other) >= 10)
                <h4 class="pt-2 pb-2">Newest posts for the same map</h4>
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Played by</th>
                            <th>Posted by</th>
                            <th>Score</th>
                            <th>spicy</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($posts_other_new as $post)
                            <tr>
                                <td>
                                    <a href="{{ url('/post/'.$post->id) }}" class="text-body" style="text-decoration: none"><i class="fas fa-link"></i></a>
                                </td>
                                <td>
                                    <a href="{{ url('/player/'.$post->player_id) }}" class="text-body" style="text-decoration: none">
                                        {{ App\Player::find($post->player_id)->name }}
                                    </a>
                                </td>
                                <td>
                                    @if ($post->author != '[deleted]')
                                        <a href="{{ url('/author/'.$post->author) }}" class="text-body" style="text-decoration: none">{{ $post->author }}</a>
                                    @else
                                        <a class="text-body">{{ $post->author }}</a>
                                    @endif
                                </td>
                                <td>{{ round($post->score) }}</td>
                                <td>{{ ($post->downs == 0) ? 0 : round(($post->downs / $post->ups) * 100) }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection
