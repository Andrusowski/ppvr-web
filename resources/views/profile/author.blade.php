@extends('layouts.main')

@section('content')

    <h1 class="display-3 d-inline-block">
        {{ $author_stats->author }}
    </h1>

    <!-- Badges -->
    <h5 class="d-inline-block pt-3 align-top">
        @if ($author_stats->controversy >= 0)
            <span class="badge badge-primary">new</span>
        @endif
        @if ($author_stats->controversy >= 0)
            <span class="badge badge-success">recent activity</span>
        @endif
        @if ($author_stats->controversy >= 0)
            <span class="badge badge-danger">controversial</span>
        @endif
    </h5>

    <div class="row pt-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Stats</h5>

                    <p class="card-text">total score: {{ round($author_stats->score) }}</p>
                    <p class="card-text">average score: {{ round($author_stats->score_avg) }}</p>
                    <p class="card-text">controversy: {{ round($author_stats->controversy) }}%</p>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <h4 class="pt-2 pb-2">Top posts</h4>
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Map</th>
                        <th>Score</th>
                        <th>Controversy</th>
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
                            <th>Map</th>
                            <th>Score</th>
                            <th>Controversy</th>
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
