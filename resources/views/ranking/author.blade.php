@extends('layouts.main')

@section('content')

    <div class="text-center welcometext">
        <h1 class="display-4">Author Ranking</h1>
        <p class="lead">
            Top Scorepost Authors
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
                        <th>Rank</th>
                        <th>Name</th>
                        <th class="text-nowrap">
                            posts
                            @if ($sort == "posts")
                                <a href="{{ url('/ranking/author/posts') }}" class="fas fa-sort-down text-body" alt="sort descending" style="text-decoration: none"></a>
                            @else
                                <a href="{{ url('/ranking/author/posts') }}" class="fas fa-sort-down text-muted" alt="sort descending" style="text-decoration: none"></a>
                            @endif
                        </th>
                        <th class="text-nowrap">
                            spicy
                            @if ($sort == "controversy")
                                <a href="{{ url('/ranking/author/controversy') }}" class="fas fa-sort-down text-body" alt="sort descending" style="text-decoration: none"></a>
                            @else
                                <a href="{{ url('/ranking/author/controversy') }}" class="fas fa-sort-down text-muted" alt="sort descending" style="text-decoration: none"></a>
                            @endif
                        </th>
                        <th class="text-nowrap">
                            total pp
                            @if ($sort == "score")
                                <a href="{{ url('/ranking/author/score') }}" class="fas fa-sort-down text-body" alt="sort descending" style="text-decoration: none"></a>
                            @else
                                <a href="{{ url('/ranking/author/score') }}" class="fas fa-sort-down text-muted" alt="sort descending" style="text-decoration: none"></a>
                            @endif
                        </th>
                        <th class="text-nowrap">
                            avg pp
                            @if ($sort == "score_avg")
                                <a href="{{ url('/ranking/author/score_avg') }}" class="fas fa-sort-down text-body" alt="sort descending" style="text-decoration: none"></a>
                            @else
                                <a href="{{ url('/ranking/author/score_avg') }}" class="fas fa-sort-down text-muted" alt="sort descending" style="text-decoration: none"></a>
                            @endif
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($posts as $post)
                        <tr>
                            <td>{{ ++$rank }}</td>
                            <td><a href="{{ url('/author/'.$post->author) }}" class="text-body" style="text-decoration: none">{{ $post->author }}</a></td>
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
