@extends('layouts.main')

@section('content')

    <p class="uk-text-lead">
        {{ $player->name.' | '.$post->map_artist.' - '.$post->map_title.' ['.$post->map_diff.']' }}
    </p>

    <p>
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

    <div uk-grid>
        <div class="uk-width-1-3@m">
            <div class="uk-card uk-card-default uk-card-hover">
                @if ($img != '')
                    <div class="uk-card-media-top" uk-lightbox="animation: slide">
                        <a href="{{ $img }}">
                            <img href="{{ $img }}"><img class="card-image" src="{{ $img }}" alt="screenshot" /></img>
                        </a>
                    </div>
                @endif
                <div class="uk-card-body">
                    <h5 class="uk-card-title">Stats</h5>

                    <table class="uk-table uk-table-small uk-table-justify">
                        <tbody>
                            <tr>
                                <td>Score:</td>
                                <td>{{ $post->ups - $post->downs }}</td>
                            </tr>
                            <tr>
                                <td>Upvotes:</td>
                                <td>{{ $post->ups }}</td>
                            </tr>
                            <tr>
                                <td>Downvotes:</td>
                                <td>{{ $post->downs }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <a class="uk-button uk-button-small uk-button-default uk-width-1-1 btn-reddit uk-text-nowrap uk-margin-top" href="{{ 'https://www.reddit.com/r/osugame/comments/'.$post->id }}">view on Reddit</a>
        </div>
        <div class="uk-width-expand@m">
            @if ($top_comment ?? null)
                <blockquote class="uk-text-center">
                  <p class="uk-text-secondary">{!! html_entity_decode($top_comment) !!}</p>
                  <footer><cite>{{ $top_comment_author }}</cite></footer>
                </blockquote>

                <br>
            @endif

            <p class="uk-text-lead ">Top posts for the same map</p>
            <div class="uk-card uk-card-default uk-card-hover uk-padding-small">
                <table class="uk-table uk-table-small uk-table-justify">
                    <thead>
                    <tr>
                        <th class="uk-padding-remove-vertical"></th>
                        <th class="uk-padding-remove-vertical">Played by</th>
                        <th class="uk-padding-remove-vertical">Posted by</th>
                        <th class="uk-padding-remove-vertical">Score</th>
                        <th class="uk-padding-remove-vertical">spicy</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($posts_other as $post)
                        <tr>
                            <td class="uk-padding-remove-vertical">
                                <a href="{{ url('/post/'.$post->id) }}" class="text-body" style="text-decoration: none"><i class="fas fa-link"></i></a>
                            </td>
                            <td class="uk-padding-remove-vertical">
                                <a href="{{ url('/player/'.$post->player_id) }}" class="text-body" style="text-decoration: none">
                                    {{ App\Models\Player::find($post->player_id)->name }}
                                </a>
                            </td>
                            <td class="uk-padding-remove-vertical">
                                <a href="{{ url('/author/'.$post->author) }}" class="text-body" style="text-decoration: none">
                                    {{ $post->author }}
                                </a>
                            </td>
                            <td class="uk-padding-remove-vertical">{{ round($post->ups - $post->downs) }}</td>
                            <td class="uk-padding-remove-vertical">{{ ($post->downs == 0) ? 0 : round(($post->downs / $post->ups) * 100) }}%</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            @if (count($posts_other) >= 10)
                <br>

                <p class="uk-text-lead">Newest posts for the same map</p>

                <div class="uk-card uk-card-default uk-card-hover uk-padding-small">
                    <table class="uk-table uk-table-small uk-table-justify">
                        <thead>
                        <tr>
                            <th class="uk-padding-remove-vertical"></th>
                            <th class="uk-padding-remove-vertical">Played by</th>
                            <th class="uk-padding-remove-vertical">Posted by</th>
                            <th class="uk-padding-remove-vertical">Score</th>
                            <th class="uk-padding-remove-vertical">spicy</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($posts_other_new as $post)
                            <tr>
                                <td class="uk-padding-remove-vertical">
                                    <a href="{{ url('/post/'.$post->id) }}" class="text-body" style="text-decoration: none"><i class="fas fa-link"></i></a>
                                </td>
                                <td class="uk-padding-remove-vertical">
                                    <a href="{{ url('/player/'.$post->player_id) }}" class="text-body" style="text-decoration: none">
                                        {{ App\Models\Player::find($post->player_id)->name }}
                                    </a>
                                </td>
                                <td class="uk-padding-remove-vertical">
                                    @if ($post->author != '[deleted]')
                                        <a href="{{ url('/author/'.$post->author) }}" class="text-body" style="text-decoration: none">{{ $post->author }}</a>
                                    @else
                                        <a class="text-body">{{ $post->author }}</a>
                                    @endif
                                </td>
                                <td class="uk-padding-remove-vertical">{{ round($post->score) }}</td>
                                <td class="uk-padding-remove-vertical">{{ ($post->downs == 0) ? 0 : round(($post->downs / $post->ups) * 100) }}%</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
