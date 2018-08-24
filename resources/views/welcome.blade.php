@extends('layouts.main')

@section('content')

<div class="text-center welcometext">
  <h1 class="display-3">PPVR</h1>
  <p class="lead">
    The only pp system where peppy is in the top 50
  </p>
</div>
<br><br>

<div class="row">

  <div class="col-md-3">
  </div>

  <div class="col-md-6">
    <h1 class="display-5 text-center">top pp</h1>
    <table class="table table-sm">
    	<thead>
        <tr>
          <th>Rank</th>
    			<th>Name</th>
          <th>posts</th>
    			<th>total pp</th>
          <th>avg pp</th>
    		</tr>
    	</thead>

    	<tbody>
        @foreach($posts as $post)
        <tr>
          <td>{{ ++$rank }}</td>
          <td>{{ $post->name }}</td>
          <td>{{ $post->posts }}</td>
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
              <span aria-hidden="true">&laquo;</span>
              <span class="sr-only">Previous</span>
          </a>
        </li>
        <li class="page-item">
          <a class="page-link" href="{{ $posts->nextPageUrl() }}" aria-label="Next">
              <span aria-hidden="true">&raquo;</span>
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
