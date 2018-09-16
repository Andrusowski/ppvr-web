@extends('layouts.main')

@section('content')

    <h1>
        {{ $author_stats->author }}
    </h1>

    <!-- Badges -->
    <h5 class="d-inline-block pt-3 align-top">
        @if ((time() - $posts_new[0]->created_utc) < 48*60*60)
            <span class="badge badge-success">recent activity</span>
        @endif
    </h5>

    <div class="row pt-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Stats</h5>

                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td>score ranking:</td>
                                <td>#{{ $rank }}</td>
                            </tr>
                            <tr>
                                <td>total score:</td>
                                <td>{{ round($author_stats->score) }}</td>
                            </tr>
                            <tr>
                                <td>average score:</td>
                                <td>{{ round($author_stats->score_avg) }}</td>
                            </tr>
                            <tr>
                                <td>posts created:</td>
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

                    <a class="btn btn-reddit btn-lg btn-block" href="{{ 'https://www.reddit.com/u/'.$author_stats->author }}">view on Reddit</a>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <h4 class="pt-2 pb-2">Top posts</h4>
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th class="table-width">Map</th>
                        <th>Score</th>
                        <th>spicy</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($posts as $post)
                        <tr>
                            <td>
                                <a href="{{ url('/post/'.$post->id) }}" class="text-body" style="text-decoration: none">
                                    {{ $post->map_artist }} - {{ $post->map_title }} [{{ $post->map_diff }}]
                                    <a href="{{ 'https://www.reddit.com/r/osugame/comments/'.$post->id }}" class="fab fa-reddit-alien reddit" style="text-decoration: none"></a>
                                </a>
                            </td>
                            <td>{{ round($post->score) }}</td>
                            <td>{{ round($post->controversy) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if (count($posts) >= 10)
                <h4 class="pt-2 pb-2">Newest posts</h4>
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th class="table-width">Map</th>
                            <th>Score</th>
                            <th>spicy</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($posts_new as $post)
                            <tr>
                                <td>
                                    <a href="{{ url('/post/'.$post->id) }}" class="text-body" style="text-decoration: none">
                                        {{ $post->map_artist }} - {{ $post->map_title }} [{{ $post->map_diff }}]
                                        <a href="{{ 'https://www.reddit.com/r/osugame/comments/'.$post->id }}" class="fab fa-reddit-alien reddit" style="text-decoration: none"></a>
                                    </a>
                                </td>
                                <td>{{ round($post->score) }}</td>
                                <td>{{ round($post->controversy) }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection
