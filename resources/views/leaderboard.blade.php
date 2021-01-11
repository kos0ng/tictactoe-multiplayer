@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 ">
            <table class="table">
              <thead>
                <th>No.</th>
                <th>Name</th>
                <th>Win</th>
                <th>Draw</th>
                <th>Lose</th>
              </thead>
              <tbody>
                @php
                $i=0;
                @endphp
                @foreach($result as $row)
                <tr>
                <td>{{++$i}}</td>
                <td>{{$row->name}}</td>
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
