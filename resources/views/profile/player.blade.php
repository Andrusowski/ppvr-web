@extends('layouts.main')

@section('content')

    <h1 class="uk-heading-medium">
        {{ $player->name }}

        @if (isset($alias))
            <span class="uk-text-meta uk-text-nowrap">
                also known as {{ $alias->alias }}
            </span>
        @endif
        <!-- Badges -->
        <p class="uk-text-meta">
            @if ((time() - strtotime($player->created_at)) < 48*60*60)
                <span class="uk-label uk-label-default">new</span>
            @endif
            @if ((time() - $posts_new[0]->created_utc) < 48*60*60)
                <span class="uk-label uk-label-success">recent activity</span>
            @endif
        </p>
    </h1>

    <div uk-grid>
        <div class="uk-width-1-4@m">
            <div class="uk-card uk-card-default">
                <div class="uk-card-media-top">
                    <img class="card-image" src="{{ 'https://a.ppy.sh/'.$player->id }}" alt="osu! profile picture" style="background-color:#333333;">
                </div>
                <div class="uk-card-body">
                    <h5 class="uk-card-title">Stats</h5>

                    <table class="uk-table uk-table-small uk-table-justify">
                        <tbody>
                            <tr>
                                <td>rank:</td>
                                <td>#{{ $rank }}</td>
                            </tr>
                            <tr>
                                <td>score:</td>
                                <td>{{ round($player->score) }}</td>
                            </tr>
                            <tr>
                                <td>spicy:</td>
                                <td>{{ round($player_stats->controversy) }}%</td>
                            </tr>
                        </tbody>
                    </table>

                    <table class="uk-table uk-table-small uk-table-justify uk-table-middle">
                        <thead>
                            <th>
                                <object data="{{ URL::asset('/icons/medal-f.svg') }} " type="image/svg+xml" class="medal-silver" title="Silver"></object>
                            </th>
                            <th>
                                <object data="{{ URL::asset('/icons/medal-f.svg') }} " type="image/svg+xml" class="medal-gold" title="Gold"></object>
                            </th>
                            <th>
                                <object data="{{ URL::asset('/icons/medal-f.svg') }} " type="image/svg+xml" class="medal-platinum" title="Platinum"></object>
                            </th>
                        </thead>
                        <tbody>
                            <tr class="uk-text-center@m">
                                <td>
                                    {{ $awards->silver }}
                                </td>
                                <td>
                                    {{ $awards->gold }}
                                </td>
                                <td>
                                    {{ $awards->platinum }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <a class="uk-button uk-button-default uk-width-1-1 btn-osu uk-text-nowrap uk-margin-top" href="{{ 'https://osu.ppy.sh/users/'.$player->id }}">osu! Profile</a>
        </div>
        <div class="uk-width-expand@m">
            <p class="uk-text-lead">Rank History</p>
            <chart
                posts="{{ $ranks }}"
                name="rankHistory"
                value-index="rank"
                unit="Rank"
                v-bind:reverse="true">
            </chart>

            <p class="uk-text-lead">Top posts</p>
            <table class="uk-table uk-table-small uk-table-justify">
                <thead>
                    <tr>
                        <th class="uk-padding-remove-vertical">Map</th>
                        <th class="uk-padding-remove-vertical">Score</th>
                        <th class="uk-padding-remove-vertical">spicy</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($posts as $post)
                        <tr>
                            <td class="uk-padding-remove-vertical">
                                <a href="{{ url('/post/'.$post->id) }}" class="text-body" style="text-decoration: none">
                                    {{ $post->map_artist }} - {{ $post->map_title }} [{{ $post->map_diff }}]
                                    <a href="{{ 'https://www.reddit.com/r/osugame/comments/'.$post->id }}" class="fab fa-reddit-alien reddit" style="text-decoration: none"></a>
                                </a>
                            </td>
                            <td class="uk-padding-remove-vertical">{{ round($post->score) }}</td>
                            <td class="uk-padding-remove-vertical">{{ round($post->controversy) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if (count($posts) >= 10)
                <p class="uk-text-lead">Newest posts</p>
                <table class="uk-table uk-table-small uk-table-justify">
                    <thead>
                        <tr>
                            <th class="uk-padding-remove-vertical">Map</th>
                            <th class="uk-padding-remove-vertical">Score</th>
                            <th class="uk-padding-remove-vertical">spicy</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($posts_new as $post)
                            <tr>
                                <td class="uk-padding-remove-vertical">
                                    <a href="{{ url('/post/'.$post->id) }}" class="text-body" style="text-decoration: none">
                                        {{ $post->map_artist }} - {{ $post->map_title }} [{{ $post->map_diff }}]
                                        <a href="{{ 'https://www.reddit.com/r/osugame/comments/'.$post->id }}" class="fab fa-reddit-alien reddit" style="text-decoration: none"></a>
                                    </a>
                                </td>
                                <td class="uk-padding-remove-vertical">{{ round($post->score) }}</td>
                                <td class="uk-padding-remove-vertical">{{ round($post->controversy) }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection
