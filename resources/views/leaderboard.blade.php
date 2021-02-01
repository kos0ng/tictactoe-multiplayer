@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 ">
            <table class="table">
              <thead>
                <tr class="active">
                <th>No.</th>
                <th>Name</th>
                <th>Win</th>
                <th>Draw</th>
                <th>Lose</th>
            </tr>
              </thead>
              <tbody>
                @php
                $i=0;
                @endphp
                @foreach($result as $row)
                <tr class="active">
                <td>{{++$i}}</td>
                <td><a href="{{ route('profile',['id'=>$row->id]) }}">{{$row->name}}</a></td>
                <td>{{$row->won_games}}</td>
                <td>{{$row->draw_games}}</td>
                <td>{{$row->lose_games}}</td>
              </tr>
                @endforeach
              </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
