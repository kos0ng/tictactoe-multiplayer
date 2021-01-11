@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    You won {{ $user->won_games }} games
                </div>

            </div>

            <div class="text-center">
                <!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
  Start new game
</button>
            </div>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Select Opponent</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table">
          <thead>
            <td>Name</td>
            <td>Action</td>
          </thead>
          <tbody>
            @foreach($player as $row)
            <tr>
              <td>{{$row->name}}</td>
              <td>
                <a class="btn btn-success" id="play{{$row->id}}" href="/game/{{$row->id}}">Play</a>
                <a class="btn btn-primary hide invite{{$row->id}}" href="/game/{{$row->id}}">Join</a>
                <a class="btn btn-danger hide invite{{$row->id}}" href="/decline/{{$row->id}}">Decline</a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
            <hr>
        </div>
    </div>
</div>
@endsection
