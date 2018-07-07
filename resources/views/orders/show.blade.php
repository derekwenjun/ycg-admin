@extends('layouts.app')

@section('content')
<div class="container">

    <ol class="breadcrumb">
        <li><a href="/">首页</a></li>
        <li class="active">包裹详情</li>
    </ol>

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
                    <td>来源网点</td>
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
                        <td>{{ $order->user->name }}</td>
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


    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-title fa-address-book" aria-hidden="true"></i><strong>面单信息</strong></div>
                <div class="panel-body">
                  <div class="row">
                    <div class="col-md-6">
                      <table class="table table-striped">
                        <thead><tr><th>发货人</th> <th></th> </tr> </thead>
                        <tbody>
                          <tr><td>姓名</td> <td>{{ $order->name }}</td> </tr>
                          <tr><td>手机号</td><td>{{ $order->mobile }}</td></tr>
                          <tr><td>国家</td><td>{{ $order->country }}</td></tr>
                          <tr><td>城市</td><td>{{ $order->city }}</td></tr>
                          <tr><td>详细地址</td><td>{{ $order->address }}</td></tr>
                          <tr><td>邮政编码</td><td>{{ $order->zip }}</td></tr>
                          <tr><td>固定电话</td><td>{{ $order->tel }}</td></tr>
                          <tr><td>邮箱</td><td>{{ $order->mailbox }}</td></tr>
                        </tbody>
                      </table>
                    </div>
                    <div class="col-md-6">
                      <table class="table table-striped">
                        <thead><tr><th>收货人</th> <th></th> </tr> </thead>
                        <tbody>
                          <tr><td>姓名</td> <td>{{ $order->shipping_name }}</td> </tr>
                          <tr><td>手机号</td><td>{{ $order->shipping_mobile }}</td></tr>
                          <tr><td>省</td><td>{{ $order->shipping_state }}</td></tr>
                          <tr><td>市</td><td>{{ $order->shipping_city }}</td></tr>
                          <tr><td>区</td><td>{{ $order->shipping_district }}</td></tr>
                          <tr><td>详细地址</td><td>{{ $order->shipping_address }}</td></tr>
                          <tr><td>邮政编码</td><td>{{ $order->shipping_zip }}</td></tr>
                          <tr><td>固定电话</td><td>{{ $order->shipping_tel ? $order->shipping_tel : '-' }}</td></tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
            </div>
            
          <div class="panel panel-default">
            <div class="panel-heading"><i class="fa fa-title fa-archive" aria-hidden="true"></i></i><strong>商品信息</strong></div>
            <div class="panel-body">
              <table class="table table-striped">
                <thead><tr>
                  <th>商品分类</th>
                  <th>商品名</th>
                  <th>型号</th>
                  <th>数量</th>
                  <th>申报价格</th>
                </tr></thead>
                <tbody>
                  @foreach ($order->orderProducts as $op)
                  <tr>
                    <td>{{ $op->category->name }}</td>
                    <td>{{ $op->name }}</td>
                    <td>{{ $op->model }}</td>
                    <td>{{ $op->number }}</td>
                    <td>{{ $op->price }}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
            
        </div>
    
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-title fa-random" aria-hidden="true"></i>订单追踪</div>
                <div class="panel-body">
				<table class="table table-striped">
                    <thead><tr>
                        <th>时间</th>
                        <th>地点</th>
                        <th>追踪进度</th>
                    </tr></thead>
                    <tbody>
                    @foreach ($order->trackings as $tracking)
                        <tr>
                            <td>{{ $tracking->created_at }}</td>
                            <td>{{ $tracking->location }}</td>
                            <td>{{ $tracking->description }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
              </div>
            </div>
        </div>
        
    </div>

</div>
@endsection
