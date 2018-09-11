@extends('layouts.main')

@section('content')

<div class="text-center welcometext">
  <h1 class="display-3">PPV<span style="color: #ff4500;">R</span></h1>
  <p class="lead">
    The only pp system where peppy is in the top 50
  </p>
</div>
<br><br>

<h5 class="text-center">Select Ranking</h5>

<div class="row">

  <div class="col-md-3">
  </div>

  <div class="col-md-3 pt-2 pb-2">
      <a class="btn btn-primary btn-lg btn-block" href="{{ url('/ranking/player/') }}">Player</a>
  </div>

  <div class="col-md-3 pt-2 pb-2">
      <a class="btn btn-primary btn-lg btn-block" href="{{ url('/ranking/author/') }}">Author</a>
  </div>

  <div class="col-md-3">
  </div>

</div>

@endsection
