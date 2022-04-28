@extends('layouts.main')

@section('content')

    <div class="uk-text-center">
        <h1 class="uk-heading-medium">Player Score Ranking</h1>
        <p>Top Players</p>
    </div>
    <br><br>
    <div class="uk-overflow-auto">
        <table class="uk-table uk-table-small uk-table-divider uk-table-justify">
            <thead>
                <tr>
                    @if($sort === 'score')
                        <th></th>
                    @endif
                    <th>#</th>
                    <th>name</th>
                    <th class="uk-text-nowrap">
                        posts
                        @if ($sort === 'posts')
                            <a href="{{ url('/ranking/player/posts') }}" class="fas fa-sort-down reddit" alt="sort descending" style="text-decoration: none"></a>
                        @else
                            <a href="{{ url('/ranking/player/posts') }}" class="fas fa-sort-down uk-text-muted" alt="sort descending" style="text-decoration: none"></a>
                        @endif
                    </th>
                    <th class="uk-text-nowrap">
                        spicy
                        @if ($sort === 'controversy')
                            <a href="{{ url('/ranking/player/controversy') }}" class="fas fa-sort-down reddit" alt="sort descending" style="text-decoration: none"></a>
                        @else
                            <a href="{{ url('/ranking/player/controversy') }}" class="fas fa-sort-down uk-text-muted" alt="sort descending" style="text-decoration: none"></a>
                        @endif
                    </th>
                    <th class="uk-text-nowrap">
                        score
                        @if ($sort === 'score')
                            <a href="{{ url('/ranking/player/score') }}" class="fas fa-sort-down reddit" alt="sort descending" style="text-decoration: none"></a>
                        @else
                            <a href="{{ url('/ranking/player/score') }}" class="fas fa-sort-down uk-text-muted" alt="sort descending" style="text-decoration: none"></a>
                        @endif
                    </th>
                    <th class="uk-text-nowrap">
                        avg
                        @if ($sort === 'score_avg')
                            <a href="{{ url('/ranking/player/score_avg') }}" class="fas fa-sort-down reddit" alt="sort descending" style="text-decoration: none"></a>
                        @else
                            <a href="{{ url('/ranking/player/score_avg') }}" class="fas fa-sort-down uk-text-muted" alt="sort descending" style="text-decoration: none"></a>
                        @endif
                    </th>
                </tr>
            </thead>

            <tbody>
                @foreach($posts as $post)
                    <?php ++$rank ?>
                    <tr>
                        @if($sort === 'score')
                            <td>
                                @if ($post->lastRank > ($rank + 1)) {{-- more than 1 rank gained --}}
                                    <i class="fas fa-angle-double-up rank-up" uk-tooltip="{{ $post->lastRank - $rank }} ranks gained over the past day"></i>
                                @elseif ($post->lastRank === ($rank + 1)) {{-- 1 rank gained --}}
                                    <i class="fas fa-angle-up rank-up" uk-tooltip="1 rank gained over the past day"></i>
                                @elseif ($post->lastRank < ($rank - 1)) {{-- more than 1 rank lost --}}
                                    <i class="fas fa-angle-double-down rank-down" uk-tooltip="{{ $rank - $post->lastRank }} ranks lost over the past day"></i>
                                @elseif ($post->lastRank === ($rank - 1)) {{-- 1 rank lost --}}
                                    <i class="fas fa-angle-down rank-down" uk-tooltip="1 rank lost over the past day"></i>
                                @endif
                            </td>
                        @endif
                        <td>{{ $rank }}</td>
                        <td><a href="{{ url('/player/'.$post->player_id) }}" style="text-decoration: none">{{ $post->name }}</a></td>
                        <td>{{ $post->posts }}</td>
                        <td>{{ number_format($post->controversy, 2) }}%</td>
                        <td>{{ round($post->score) }}</td>
                        <td>{{ round($post->score_avg) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <ul class="uk-pagination uk-flex-center">
        <li><a href="{{ $posts->previousPageUrl() }}" ><span uk-pagination-previous></span></a></li>
        @if (($posts->total() > 7) && ($posts->currentPage() > 5))
            <li><a href="{{ $posts->url(1) }}">1</a></li>
            <li class="uk-disabled"><span>...</span></li>
        @endif

        @foreach($pageUrls as $pageNo => $pageUrl)
            @if ($posts->currentPage() === $pageNo)
                <li class="uk-active">
            @else
                <li>
            @endif
                <a href="{{ $pageUrl }}">{{ $pageNo }}</a>
            </li>
        @endforeach
        <li><a href="{{ $posts->nextPageUrl() }}"><span uk-pagination-next></span></a></li>
    </ul>
@endsection
