@extends('layouts.app')

@section('content')
<div class="container">

    <ol class="breadcrumb">
      <li><a href="/">Home</a></li>
      <li><a href="{{ url('/user') }}">All Users</a></li>
      <li class="active">User Detail</li>
    </ol>

    <div class="row">
        <div class="col-md-9">
            <div class="media">
                <div class="media-left">
                    <a href="#">
                        <img class="media-object img-rounded" src="{{ asset('image/avatar.gif') }}" alt="" style="width:200px;height:200px;">
                    </a>
                </div>
                <div class="media-body">
                    <div class="panel panel-default">
                        <div class="panel-heading">User Detail</div>
                        <div class="panel-body">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr><th>#</th><th>Item Name</th><th>Value</th></tr>
                                </thead>
                                <tr><th>1</th><td>ID</td><td>{{ $user->id }}</td></tr>
                                <tr><th>2</th><td>Name</td><td>{{ $user->name }}</td></tr>
                                <tr><th>3</th><td>EMail</td><td>{{ $user->email }}</td></tr>
                                <tr><th>3</th><td>Password</td><td>******</td></tr>
                                <tr><th>4</th><td>Balance</td><td>{{ $user->balance }}</td></tr>
                                <tr><th>5</th><td>Create Time</td><td>{{ $user->created_at }}</td></tr>
                            </table>
                            <a class="btn btn-primary" href="{{Route('user.edit', ['id'=>$user->id])}}">Edit User Info</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
