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

                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Actions</th>
                    	</tr>
                	</thead>

                	<tbody>
                        @foreach($tmpposts as $tmppost)
                        <tr>
                            <td @if((time() - strtotime($tmppost->created_at)) < 48*60*60) class="text-muted" @endif>{{ $tmppost->title }}</td>
                            <td>
                                <a href="{{ url('review/add/'.$tmppost->id) }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-pencil-alt"></i></a>
                                <a href="{{ url('review/delete/'.$tmppost->id) }}" class="btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
