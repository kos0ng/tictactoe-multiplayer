@extends('layouts.app')

@section('content')

    <style>
        div.grid {
            width : 100%;
        }

        div.grid .row {
            text-align      : center;
            display         : flex;
            justify-content : center;
        }

        div.cell {
            width       : 120px;
            height      : 120px;
            border      : 1px solid #333;
            display     : inline-block;
            font-size   : 80px;
            font-weight : bolder;
            cursor      : pointer;
            transition  : 0.4s all;
        }

        div.cell:hover {
            background-color : #333;
        }
    </style>

    <div class="container">
        <div class="row" style="margin-top: 5%">
            <h1 class="turn1 hide text-center" style="color: red">{{$first }} turn</h1>
            <h1 class="turn2 hide text-center" style="color: red">{{$second}} turn</h1>
            <h1 class="win hide text-center" style="color: green">You WIN!</h1>
            <h1 class="lose hide text-center" style="color: red">You Lose!</h1>
            <h1 class="draw hide text-center" style="color: white">Draw!</h1>
            <div class="col-md-8 col-md-offset-2" style="margin-top: 5%">
                <div class="panel panel-default">

                    <hr>
                    <div class="text-center loading">
                        <h2>Waiting for player</h2>
                    </div>
                    <input type="hidden" id="id" value="{{$room_id}}">
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                    <div class="win lose draw text-center hide" style="margin-bottom: 5%">
                        <button class="btn btn-success" onclick="window.location.reload();">Play Again?</button>
                        <a href="/home"><button class="btn btn-danger" style="margin-left: 3%">Back to home</button></a>
                    </div>
                    <div class="grid hide"></div>

                    <hr>

                </div>
                <h2 style="text-align: center;color: white" id="x">You are <b>{{$type}}<b></h2>
            </div>
        </div>
    </div>
@endsection