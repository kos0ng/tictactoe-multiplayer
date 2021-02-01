@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6 ">
           <div class="panel panel-default">
  <div class="panel-heading">Player Profile</div>
  <div class="panel-body">
    <div class="row">
        <div class="col-md-6">
            <img src="{{url('/images/profile/'.$user->photo)}}" class="img-thumbnail">
        </div>
        <div class="col-md-6">
            <table class="table-striped table">
                <tbody>
                    <tr>
                        @php
                        $total=$user->won_games+$user->draw_games+$user->lose_games;
                        @endphp
                        <th>Name</th>
                        <td>: {{$user->name}}</td>
                    </tr>
                    <tr>
                        <th>Total Playing</th>
                        <td>: {{$total}}</td>
                    </tr>
                    <tr>
                        <th>Win</th>
                        <td>: {{$user->won_games}}</td>
                    </tr>
                    <tr>
                        <th>Draw</th>
                        <td>: {{$user->draw_games}}</td>
                    </tr>
                    <tr>
                        <th>Lose</th>
                        <td>: {{$user->lose_games}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-12">
            <table class="table-striped table">
                <tbody>
                    <tr class="success">
                        <th>Win Ratio</th>
                        <td>
                            @php
                                if($total==0){
                                    echo '0';
                                }
                                else{
                                    echo round(($user->won_games/$total)*100);
                                }
                            @endphp
                        %</td>
                    </tr>
                    <tr class="info">
                        <th>Draw Ratio</th>
                        <td>
                            @php
                                if($total==0){
                                    echo '0';
                                }
                                else{
                                    echo round(($user->draw_games/$total)*100);
                                }
                            @endphp
                        %</td>
                    </tr>
                    <tr class="warning"> 
                        <th>Lose Ratio</th>
                        <td>
                             @php
                                if($total==0){
                                    echo '0';
                                }
                                else{
                                    echo round(($user->lose_games/$total)*100);
                                }
                            @endphp
                        %</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
  </div>
</div>

        </div>
        <div class="col-md-3"></div>
    </div>
</div>
@endsection
