@extends('layouts.app')

@section('content')
<div class="container">

    <ol class="breadcrumb">
      <li><a href="/">首页</a></li>
    </ol>
    

    <div class="row">
        <div class="col-md-5 col-md-offset-2">
            <div class="jumbotron">
                <h3>Welcome! {{ Auth::user()->name }}</h3>
                <p>YCG 物流系统管理后台！</p>
                <p>
                		<a class="btn btn-success" href="{{ url('/order') }}" role="button">所有包裹</a>
                    <a class="btn btn-primary" href="{{ url('/client') }}" role="button">所有客户</a>
                </p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-title fa-eur" aria-hidden="true"></i>Financial</div>

                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h2 class="text-center"><i class="fa fa-eur" aria-hidden="true"></i>&nbsp;324.5</h2>
                            <p class="text-center text-muted">Account Balance</p>
                        </div>
                        <div class="col-md-6">
                            <h2>
                            <button type="button" class="btn btn-primary">Recharge</button>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-title fa-cog" aria-hidden="true"></i>包裹信息</div>

                <div class="panel-body">
                    You are logged in!
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
