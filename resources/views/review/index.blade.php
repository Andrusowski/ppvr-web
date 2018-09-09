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
                            <td>{{ $tmppost->title }}</td>
                            <td>
                                <a href="{{ url('review/add/'.$tmppost->id) }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-pencil-alt"></i></a>
                                <a href="{{ url('review/delete/'.$tmppost->id) }}" class="btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></a>
                            </td>
                        </tr>

                        <div class="modal fade" id="{{ '#editTmppostModal'.$tmppost->id }}" tabindex="-1" role="dialog" aria-labelledby="{{ '#editTmppostModal'.$tmppost->id }}" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="{{ '#editTmppostModal'.$tmppost->id }}">Edit Post</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <div class="modal-body">
                                  {{ $tmppost->title }}
                                  <br>
                                  <form method="post" action="{{url('review/edit/'.$tmppost->id)}}">
                                    <div class="form-group">
                                      <label for="InputPlayer">Player</label>
                                      <input type="text" class="form-control" id="InputPlayer">
                                    </div>
                                    <div class="form-group">
                                        <label for="InputArtist">Artist</label>
                                        <input type="text" class="form-control" id="InputArtist">
                                    </div>
                                    <div class="form-group">
                                        <label for="InputArtist">Artist</label>
                                        <input type="text" class="form-control" id="InputArtist">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                  </form>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary">Save changes</button>
                              </div>
                            </div>
                          </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
