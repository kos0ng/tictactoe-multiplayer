@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
      <div class="col-md-2"></div>
      <div class="col-md-8 text-center">
        
      </div>
      <div class="col-md-2"></div>
    </div>
    <div class="row" style="margin-top: 25%">
        <div class="col-md-8 col-md-offset-2">
          <h1 style="color: white" class="text-center">Welcome {{$user->name}}!</h1>
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    <h4>You won {{ $user->won_games }} games!</h4>
                </div>

            </div>

            <div class="text-center">
                
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
  Start new game
</button>
            </div>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="margin-top: 8%">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="exampleModalLabel">Select Opponent</h3>
      </div>
      <div class="modal-body">
        <table class="table">
          <thead>
            <th>Name</th>
            <th>Action</th>
          </thead>
          <tbody>
            @foreach($player as $row)
            <tr>
              <td>{{$row->name}}</td>
            {{--   @php
              $last_login=strtotime($row->last_login_at);
              if(time()-$last_login>env('SESSION_LIFETIME')*60 || $row->logged_in==0){
                echo '<td id="offline'.$row->id.'" class="hide">Player is offline</td>';
              }
            else{
            @endphp --}}
              <td id="offline{{$row->id}}" class="hide">
                <span style="color:red">Player is offline</span>
              </td>
              <td id="online{{$row->id}}">
                <a class="btn btn-success" id="play{{$row->id}}" href="/game/{{$row->id}}">Play</a>
                <a class="btn btn-primary hide invite{{$row->id}}" href="/game/{{$row->id}}">Join</a>
                <button class="btn btn-danger hide invite{{$row->id}}" onclick="decline({{$row->id}})">Decline</button>
              </td>
            </tr>
{{--             @php
            }
            @endphp --}}
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
