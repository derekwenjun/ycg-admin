@extends('layouts.app')

@section('content')
<div class="container">

    <ol class="breadcrumb">
      <li><a href="/">首页</a></li>
      <li class="active">我的订单</li>
    </ol>

    <form action="{{Route('order.index')}}" method="GET">
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
                <td>包裹状态</td>
                <td>操作</td>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td class="{{ old('no') ? 'has-success' : '' }}">
                        <input class="form-control input-sm" type="text" name="no" value="{{ old('no') }}"/>
                    </td>
                    <td></td>
                    <td>
                        <select class="form-control input-sm" name="type">
                            <option value="" @if (old('type_id')=="") selected @endif></option>
                            <option value="1" @if (old('type_id')=="1") selected @endif>BC</option>
                            <option value="2" @if (old('type_id')=="2") selected @endif>个人物品</option>
                        </select>
                    </td>
                    <td class="{{ old('user_id') ? 'has-success' : '' }}"></td>
                    <td><!-- 总重量 --></td>
                    <td><!-- 运费 --></td>
                    <td><input class="form-control input-sm" type="text" name="name" value="{{ old('name') }}"/></td>
                    <td><input class="form-control input-sm" type="text" name="shipping_name" value="{{ old('shipping_name') }}"/></td>
                    <td><!-- 创建时间 --></td>
                    <td class="{{ old('status') ? 'has-success' : '' }}">
                        <select class="form-control input-sm" name="status_id">
                            <option value="" @if (old('status_id')=="") selected @endif></option>
                            <option value="0" @if (old('status_id')=="0") selected @endif>未完成</option>
                            <option value="1" @if (old('status_id')=="1") selected @endif>待付款</option>
                        </select>
                    </td>
                    <td><button type="submit" class="btn btn-primary btn-sm">筛选</button></td>
                </tr>
            @foreach ($orders as $order)
                <tr class="{{ $order->status_id == 0 ? 'warning' : ( $order->status_id >= 2 ? 'success' : '' ) }}">
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
                    <td>{{ $order->status->name }}</td>
                    <td>
                        <a href="{{ route('order.show', ['id' => $order->id]) }}" class="btn-sm btn-primary">详情 | 追踪</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </form>

    <script>
        function confirmDelete(action) {
            $('.deleteConfirm form').attr('action', action);
        }
    </script>
</div>
@endsection
