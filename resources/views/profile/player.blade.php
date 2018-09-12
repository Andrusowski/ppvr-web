@extends('layouts.main')

@section('content')

    <h1 class="display-3 d-inline-block">
        {{ $player->name }}
    </h1>

    <!-- Badges -->
    <h5 class="d-inline-block pt-3 align-top">
        @if ((time() - strtotime($player->created_at)) < 48*60*60)
            <span class="badge badge-primary">new</span>
        @endif
        @if ((time() - $posts_new[0]->created_utc) < 48*60*60)
            <span class="badge badge-success">recent activity</span>
        @endif
    </h5>

    @if ($player->alias != null)
        <p class="lead">
            also known as {{ $player->alias }}
        </p>
    @endif

    <div class="row pt-4">
        <div class="col-md-3">
            <div class="card">
                <img class="card-img-top" src="{{ 'https://a.ppy.sh/'.$player->id }}" alt="Card image cap">
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
                              <td>{{ round($player_stats->score) }}</td>
                            </tr>
                            <tr>
                              <td>average score:</td>
                              <td>{{ round($player_stats->score_avg) }}</td>
                            </tr>
                            <tr>
                              <td>controversy:</td>
                              <td>{{ round($player_stats->controversy) }}%</td>
                            </tr>
                      </tbody>
                    </table>

                    <a class="btn btn-primary btn-lg btn-block" href="{{ 'https://osu.ppy.sh/users/'.$player->id }}">osu! Profile</a>
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
                            <th class="table-width">Map</th>
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
