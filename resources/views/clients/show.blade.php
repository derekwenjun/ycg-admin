@extends('layouts.app')

@section('content')

<div class="container">

    <ol class="breadcrumb">
      <li><a href="/">首页</a></li>
      <li><a href="{{ url('/client') }}">客户列表</a></li>
      <li class="active">客户详情 - {{ $client->name }}</li>
    </ol>

    <div class="row">
    <div class="col-md-9">
      <div class="panel panel-default">
        <div class="panel-heading">
          <i class="fa fa-id-card fa-title" aria-hidden="true"></i>基本信息
<!--           
          <span class="pull-right">
            <a class="btn btn-primary btn-xs" href="{{ route('client.edit', ['id' => $client->id]) }}">
              <i class="fa fa-pencil-square-o fa-title" aria-hidden="true"></i>编辑基本信息
            </a>
          </span>
-->
        </div>
        <div class="panel-body">
          <table class="table table-hover table-striped">
              <thead>
              <tr>
                  <td>#</td>
                  <td>姓名</td>
                  <td>手机号</td>
                  <td>注册网点</td>
                  <td>用户等级</td>
                  <td>创建时间</td>
              </tr>
              </thead>
              <tbody>
                  <tr>
                      <td>{{ $client->id }}</td>
                      <td>{{ $client->name }}</td>
                      <td>{{ $client->mobile }}</td>
                      <td>{{ $client->user->name }}</td>
                      <td>Normal</td>
                      <td>{{ $client->created_at }}</td>
                  </tr>
              </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="panel panel-default">
        <div class="panel-heading">
          <i class="fa fa-eur fa-title" aria-hidden="true"></i>账户余额
<!--           <span class="pull-right">
              <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal">
                  <i class="fa fa-money fa-title" aria-hidden="true"></i>充值
              </button>
          </span> -->
        </div>
        <div class="panel-body">
          <h2 class="text-center"><i class="fa fa-eur fa-title" aria-hidden="true"></i>{{ $client->balance }}</h2>
          <p class="text-center text-muted">账户余额</p>
        </div>
      </div>
    </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading">
        <i class="fa fa-building fa-title" aria-hidden="true"></i>发货地址
        <!-- Button trigger modal -->
<!--         <span class="pull-right">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal">
                <i class="fa fa-plus fa-title" aria-hidden="true"></i>添加发货地址
            </button>
        </span> -->
      </div>
      <div class="panel-body">
        <table class="table table-hover table-striped">
            <thead>
            <tr>
                <td>#</td>
                <td>发件人</td>
                <td>手机号</td>
                <td>国家</td>
                <td>城市</td>
                <td>详细地址</td>
                <td>邮政编码</td>
                <td>固定电话</td>
                <td>操作</td>
            </tr>
            </thead>
            <tbody>
            @foreach ($addresses as $address)
                <tr class="{{ $address->is_default == 1 ? 'success' : '' }}">
                    <td>{{ $address->id }}</td>
                    <td>{{ $address->name }}</td>
                    <td>{{ $address->mobile }}</td>
                    <td>{{ $address->country }}</td>
                    <td>{{ $address->city }}</td>
                    <td>{{ $address->address }}</td>
                    <td>{{ $address->zip }}</td>
                    <td>{{ $address->tel }}</td>
                    <td>
                        @if ($address->is_default == 1)
                            <span class="text-success">Default</span> | 
                        @else
                            <a href="#" data-toggle="modal" data-target="#">设为默认地址</a> | 
                        @endif
                        <a href="#" data-toggle="modal" data-target="#modelAddress{{ $address->id }}">编辑</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading">
        <i class="fa fa-address-book fa-title" aria-hidden="true"></i>收货地址
<!--         <span class="pull-right">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#shippingModal">
                <i class="fa fa-plus fa-title" aria-hidden="true"></i>添加收货地址
            </button>
        </span> -->
      </div>
      <div class="panel-body">
        <table class="table table-hover table-striped">
            <thead>
            <tr>
                <td>#</td>
                <td>发件人</td>
                <td>手机号</td>
                <td>省</td>
                <td>市</td>
                <td>区</td>
                <td>详细地址</td>
                <td>邮政编码</td>
                <td>操作</td>
            </tr>
            </thead>
            <tbody>
            @foreach ($shippingAddresses as $shippingAddress)
                <tr>
                    <td>{{ $shippingAddress->id }}</td>
                    <td>{{ $shippingAddress->name }}</td>
                    <td>{{ $shippingAddress->mobile }}</td>
                    <td>{{ $shippingAddress->state }}</td>
                    <td>{{ $shippingAddress->city }}</td>
                    <td>{{ $shippingAddress->district }}</td>
                    <td>{{ $shippingAddress->address }}</td>
                    <td>{{ $shippingAddress->zip }}</td>
                    <td>
                        <a href="#" data-toggle="modal" data-target="#modelAddress{{ $address->id }}">编辑</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
      </div>
    </div>

</div>
@endsection
