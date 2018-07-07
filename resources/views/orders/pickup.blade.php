@extends('layouts.app')

@section('content')
<div class="container">

    <ol class="breadcrumb">
      <li><a href="/">首页</a></li>
      <li class="active">包裹入库</li>
    </ol>

    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-title fa-search" aria-hidden="true"></i>输入运单号查询</div>                

                <div class="panel-body">
                    <form class="form-inline" action="{{ url('/order/pickup') }}" method="POST">
                    {{ csrf_field() }}
                      <div class="form-group">
                        <label for="no" class="sr-only">no</label>
                        <input type="text" class="form-control" id="no" name="no" placeholder="No.">
                      </div>
                      &nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-primary">入库包裹</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@if (isset($order))

    @if (session('status'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {{ session('status') }}
        </div>
    @endif
    
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {{ session('error') }}
        </div>
    @endif

    <div class="panel panel-default">
        <div class="panel-heading"><i class="fa fa-title fa-info-circle" aria-hidden="true"></i>包裹信息 - {{ $order->no }}</div>
        <div class="panel-body">
            <table class="table table-hover table-striped">
                <thead>
                <tr>
                    <td>#</td>
                    <td>No.运单号</td>
                    <td>客户名称</td>
                    <td>包裹类型</td>
                    <td>包裹来源</td>
                    <td>重量(KG)</td>
                    <td>运费</td>
                    <td>发件人</td>
                    <td>收件人</td>
                    <td>发件时间</td>
                    <td>支付时间</td>
                    <td>包裹状态</td>
                </tr>
                </thead>
                <tbody>
                    <tr class="success">
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->no }}</td>
                        <td>{{ $order->client->name}}</td>
                        <td>{{ $order->type->name }}</td>
                        <td>{{ $order->source }}</td>
                        <td>{{ $order->weight }}</td>
                        <td>{{ $order->price }}</td>
                        <td>{{ $order->name }}</td>
                        <td>{{ $order->shipping_name }}</td>
                        <td>{{ $order->created_at }}</td>
                        <td>{{ $order->paid_at }}</td>
                        <td>{{ $order->status->name }}</td>
                    </tr>
                </tbody>
            </table>                
        </div>
    </div>

@endif


</div>
@endsection
