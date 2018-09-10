@extends('layouts.main')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="card">
            <div class="card-header">
                <div class="dropdown text-right">
                    <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ Auth::user()->name }} <span class="caret"></span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('logout') }}"
                           onclick="event.preventDefault();
                                         document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                <a href="{{ 'https://www.reddit.com/r/osugame/comments/'.$tmppost->id}}" class="fas fa-external-link-alt" style="text-decoration: none"></a>
                <a target="_blank" rel="noopener noreferrer">{{ $tmppost->title }}</a>
                <br><br>

                <form method="post" action="{{url('review/add/'.$tmppost->id)}}">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="player">Player</label>
                            <input type="text" class="form-control" name="player" value="{{old('player')?old('player'):$tmppostParsed["player"]}}">
                            @if ($user == null)
                                <small class="form-text text-danger">Player does not exist</small>
                            @else
                                <small class="form-text text-success">Player exists</small>
                            @endif
                            @if ($errors->has('player'))
                                {{ $errors->first('player') }}
                            @endif
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="artist">Artist</label>
                            <input type="text" class="form-control" name="artist" value="{{old('artist')?old('artist'):$tmppostParsed["artist"]}}">
                            @if ($errors->has('artist'))
                                {{ $errors->first('artist') }}
                            @endif
                        </div>
                        <div class="form-group col-md-6">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" name="title" value="{{old('title')?old('title'):$tmppostParsed["title"]}}">
                            @if ($errors->has('title'))
                                {{ $errors->first('title') }}
                            @endif
                        </div>
                        <div class="form-group col-md-2">
                            <label for="diff">Diff</label>
                            <input type="text" class="form-control" name="diff" value="{{old('diff')?old('diff'):$tmppostParsed["diff"]}}">
                            @if ($errors->has('diff'))
                                {{ $errors->first('diff') }}
                            @endif
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit</button><a href="{{ url('/review') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
