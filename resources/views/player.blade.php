@extends('layouts.main')

@section('content')

<h1 class="display-3 d-inline-block">
    {{ $player->name }}
</h1>

<!-- Badges -->
<h5 class="d-inline-block pt-3 align-top">
@if ($player_stats->controversy >= 0)
    <span class="badge badge-primary">new</span>
@endif
@if ($player_stats->controversy >= 0)
    <span class="badge badge-success">recent activity</span>
@endif
@if ($player_stats->controversy >= 0)
    <span class="badge badge-danger">controversial</span>
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

            <p class="card-text">total score: {{ round($player_stats->score) }}</p>
            <p class="card-text">average score: {{ round($player_stats->score_avg) }}</p>
            <p class="card-text">controversy: {{ round($player_stats->controversy) }}%</p>
          </div>
        </div>
    </div>
    <div class="col-md-9">
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
                  <a href="{{ 'https://www.reddit.com/r/osugame/comments/'.$post->id }}" class="text-body" style="text-decoration: none">
                      {{ $post->map_artist }} - {{ $post->map_title }} [{{ $post->map_diff }}]
                  </a>
              </td>
              <td>{{ round($post->score) }}</td>
              <td>{{ round($post->controversy) }}%</td>
            </tr>
            @endforeach
          </tbody>
        </table>
    </div>
</div>


@endsection
