@extends('layouts.main')

@section('content')

    <h1 class="uk-heading-medium">
        {{ $author->name }}

        <!-- Badges -->
        <p class="uk-text-meta">
            @if ((time() - $posts_new[0]->created_utc) < 48*60*60)
                <span class="uk-label uk-label-success">recent activity</span>
            @endif
        </p>
    </h1>

    <div uk-grid>
        <div class="uk-width-1-4@m">
            <div class="uk-card uk-card-default uk-card-hover">
                <div class="uk-card-body uk-padding-small">
                    <h5 class="uk-card-title">Stats</h5>

                    <table class="uk-table uk-table-small uk-table-justify">
                        <tbody>
                            <tr>
                                <td>rank:</td>
                                <td>#{{ $rank }}</td>
                            </tr>
                            <tr>
                                <td>score:</td>
                                <td>{{ round($author->score) }}</td>
                            </tr>
                            <tr>
                                <td>score (w):</td>
                                <td>{{ round($author->score_weighted) }}</td>
                            </tr>
                            <tr>
                                <td>posts:</td>
                                <td>{{ $author_stats->posts }}</td>
                            </tr>
                            <!--
                            <tr>
                                <td>spicy:</td>
                                <td>{{ round($author_stats->controversy) }}%</td>
                            </tr>
                            -->
                        </tbody>
                    </table>
                </div>
            </div>

            <a class="uk-button uk-button-default uk-width-1-1 btn-reddit uk-text-nowrap uk-margin-top" href="{{ 'https://www.reddit.com/u/'.$author_stats->author }}">Reddit Profile</a>
        </div>
        <div class="uk-width-expand@m">
            <p class="uk-text-lead">Top posts</p>
            <div class="uk-card uk-card-default uk-card-hover uk-padding-small">
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
            </div>

            @if (count($posts) >= 10)
                <p class="uk-text-lead">Newest posts</p>
                <div class="uk-card uk-card-default uk-card-hover uk-padding-small">
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
                </div>
            @endif
        </div>
    </div>
@endsection
