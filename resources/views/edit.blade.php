@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6 ">
           <div class="panel panel-default">
  <div class="panel-heading">Edit Profile</div>
  <div class="panel-body">
    <form action="{{url('/updateProfile')}}" method="POST"  enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="row">
        @if(Session::has('success'))
            <div class="col-md-2"></div><div class="col-md-10"  style="color: green">{{ Session::get('success') }}</div>
        @endif
    </div>
    <div class="row">
        <div class="col-md-2" style="padding-top:1%">Name</div>
        <div class="col-md-10">
            <input type="text" name="name" class="form-control" value="{{$user->name}}" required>
        </div>
    </div>
    <div class="row" style="margin-top: 2%">
        <div class="col-md-2" style="padding-top:1%">Email</div>
        <div class="col-md-10">
            <input type="email" name="email" class="form-control" value="{{$user->email}}" required>
        </div>
        @if($errors->has('email'))
            <div class="col-md-2"></div><div class="col-md-10" style="color: red">{{ $errors->first('email') }}</div>
        @endif
    </div>
    <div class="row" style="margin-top: 2%">
        <div class="col-md-2" style="padding-top:1%">Old Password</div>
        <div class="col-md-10">
            <input type="password" name="oldpass" class="form-control">
        </div>
    </div>
    <div class="row" style="margin-top: 2%">
        <div class="col-md-2" style="padding-top:1%">New Password</div>
        <div class="col-md-10">
            <input type="password" name="newpass" class="form-control">
        </div>
        @if($errors->has('newpass'))
            <div class="col-md-2"></div><div class="col-md-10" style="color: red">{{ $errors->first('newpass') }}</div>
        @endif
        @if(Session::has('message'))
            <div class="col-md-2"></div><div class="col-md-10"  style="color: red">{{ Session::get('message') }}</div>
        @endif
    </div>
    <div class="row" style="margin-top: 1%">
                <div class="col-md-2"></div>
        <div class="col-md-10">
            <img src="{{url('/images/profile/'.$user->photo)}}" class="img-thumbnail" style="width: 40%">
        </div>
    </div>
    <div class="row" style="margin-top: 2%">
        <div class="col-md-2" style="padding-top:1%">Photo</div>
        <div class="col-md-10">
            <input type="file" name="photo" class="form-control">
        </div>
        @if($errors->has('photo'))
            <div class="col-md-2"></div><div class="col-md-10" style="color: red">{{ $errors->first('photo') }}</div>
        @endif
    </div>
    <div class="row" style="margin-top: 4%">
        <div class="col-md-4"></div>
        <div class="col-md-2" >
            <input type="submit" name="submit" value="Update" class="btn btn-primary">
        </div>
        <div class="col-md-2">
            <input type="reset" name="reset" value="Reset" class="btn btn-danger">
        </div>
        <div class="col-md-4"></div>
    </div>
    </form>
  </div>
</div>

        </div>
        <div class="col-md-3"></div>
    </div>
</div>
@endsection
