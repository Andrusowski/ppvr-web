@extends('layouts.main')

@section('content')
    <h1 class="uk-heading-small uk-text-center">Statistics</h1>

    <div class="uk-child-width-expand@s uk-text-center" uk-grid>
        <div>
            <div class="uk-card uk-card-default uk-card-body uk-card-hover">
                <h2 class="uk-card-title">Scoreposts Total</h2>
                <p>{{ $postsTotal }}</p>
            </div>
        </div>
        <div>
            <div class="uk-card uk-card-default uk-card-body uk-card-hover">
                <h2 class="uk-card-title">Invalid Scoreposts Total</h2>
                <p>{{ $tmpPostsTotal }}</p>
            </div>
        </div>
    </div>

    <div class="uk-card uk-card-default uk-card-body uk-card-hover uk-margin">
        <h2 class="uk-card-title">Posts created per Day</h2>
        <bar-chart
            posts="{{ json_encode($postsHistory) }}"
            name="postsHistory"
            value-index="postsDaily"
            v-bind:y-axes-display="true">
        </bar-chart>
    </div>

    <div class="uk-card uk-card-default uk-card-body uk-card-hover uk-margin">
        <h2 class="uk-card-title">Upvotes per Day</h2>
        <bar-chart
            posts="{{ json_encode($upvotesHistory) }}"
            name="postsHistory"
            value-index="postsDaily"
            color="rgb(255, 127, 17)"
            v-bind:y-axes-display="true">
        </bar-chart>
    </div>
@endsection
