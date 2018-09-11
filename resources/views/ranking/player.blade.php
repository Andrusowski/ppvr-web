@extends('layouts.main')

@section('content')

<div class="text-center welcometext">
  <h1 class="display-4">player Score Ranking</h1>
  <p class="lead">
    Total scores of all posts about each player, without any weighting.
  </p>
</div>
<br><br>

<div class="row">

  <div class="col-md-3">
  </div>

  <div class="col-md-6">
    <table class="table table-sm table-hover">
    	<thead>
            <tr>
                <th>#</th>
        		<th>Name</th>
                <th class="text-nowrap">
                    posts
                    @if ($sort == "posts")
                    <a href="{{ url('/ranking/player/posts') }}" class="fas fa-sort-down text-body" alt="sort descending" style="text-decoration: none"></a>
                    @else
                    <a href="{{ url('/ranking/player/posts') }}" class="fas fa-sort-down text-muted" alt="sort descending" style="text-decoration: none"></a>
                    @endif
                </th>
                <th class="text-nowrap">
                    ratio
                    @if ($sort == "controversy")
                    <a href="{{ url('/ranking/player/controversy') }}" class="fas fa-sort-down text-body" alt="sort descending" style="text-decoration: none"></a>
                    @else
                    <a href="{{ url('/ranking/player/controversy') }}" class="fas fa-sort-down text-muted" alt="sort descending" style="text-decoration: none"></a>
                    @endif
                </th>
        		<th class="text-nowrap">
                    score
                    @if ($sort == "score")
                    <a href="{{ url('/ranking/player/score') }}" class="fas fa-sort-down text-body" alt="sort descending" style="text-decoration: none"></a>
                    @else
                    <a href="{{ url('/ranking/player/score') }}" class="fas fa-sort-down text-muted" alt="sort descending" style="text-decoration: none"></a>
                    @endif
                </th>
                <th class="text-nowrap">
                    avg
                    @if ($sort == "score_avg")
                    <a href="{{ url('/ranking/player/score_avg') }}" class="fas fa-sort-down text-body" alt="sort descending" style="text-decoration: none"></a>
                    @else
                    <a href="{{ url('/ranking/player/score_avg') }}" class="fas fa-sort-down text-muted" alt="sort descending" style="text-decoration: none"></a>
                    @endif
                </th>
    		</tr>
    	</thead>

    	<tbody>
        @foreach($posts as $post)
        <tr>
          <td>{{ ++$rank }}</td>
          <td><a href="{{ url('/player/'.$post->player_id) }}" class="text-body" style="text-decoration: none">{{ $post->name }}</a></td>
          <td>{{ $post->posts }}</td>
          <td>{{ number_format($post->controversy, 2) }}%</td>
          <td>{{ round($post->score) }}</td>
          <td>{{ round($post->score_avg) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <nav>
      <ul class="pagination justify-content-center">
        <li class="page-item">
          <a class="page-link" href="{{ $posts->previousPageUrl() }}" aria-label="Previous">
              <span aria-hidden="true"><i class="fas fa-chevron-left"></i></span>
              <span class="sr-only">Previous</span>
          </a>
        </li>
        <li class="page-item">
          <a class="page-link" href="{{ $posts->nextPageUrl() }}" aria-label="Next">
              <span aria-hidden="true"><i class="fas fa-chevron-right"></i></span>
              <span class="sr-only">Next</span>
          </a>
        </li>
      </ul>
    </nav>

  </div>

  <div class="col-md-3">
  </div>

</div>

@endsection
