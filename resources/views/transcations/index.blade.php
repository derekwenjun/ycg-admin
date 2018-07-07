@extends('layouts.app')

@section('content')
<div class="container">

    <ol class="breadcrumb">
      <li><a href="/">首页</a></li>
      <li class="active">财务管理 Finance</li>
    </ol>

    <form action="{{Route('transcation.index')}}" method="GET">
        <table class="table table-hover table-striped">
            <thead>
            <tr>
                <td>#</td>
                <td>订单类型</td>
                <td>客户号</td>
                <td>客户姓名</td>
                <td>网点名称</td>
                <td>运单号(如有)</td>
                <td>总金额</td>
                <td>创建时间</td>
                <td>操作</td>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td><input type="text" class="form-control input-sm" name="type" value="{{ old('type') }}"/></td>
                    <td><input type="text" class="form-control input-sm" name="client_id" value="{{ old('client_id') }}"/></td>
                    <td><input type="text" class="form-control input-sm" disabled /></td>
                    <td><input type="text" class="form-control input-sm" name="user" value="{{ old('user') }}"/></td>
                    <td><input type="text" class="form-control input-sm" name="no" value="{{ old('no') }}"/></td>
                    <td><input type="number" class="form-control input-sm" name="weight" value="{{ old('amount') }}"/></td>
                    <td><!-- 创建时间 --></td>
                    <td><button type="submit" class="btn btn-primary btn-sm">筛选</button></td>
                </tr>
            @foreach ($transcations as $transcation)
                <tr class="{{ $transcation->type == '用户充值' ? 'success' : '' }}">
                    <td>{{ $transcation->id }}</td>
                    <td>{{ $transcation->type }}</td>
                    <td>{{ $transcation->client_id }}</td>
                    <td>{{ $transcation->client->name }}</td>
                    <td>{{ $transcation->user->name }}</td>
                    <td>{{ $transcation->order_no }}</td>
                    <td><i class="fa fa-title fa-eur" aria-hidden="true"></i>{{ $transcation->amount }}</td>
                    <td>{{ $transcation->created_at }}</td>
                    <td>
                        <a href="#">订单详情</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </form>
    
    {!! $transcations->render() !!}

</div>
@endsection
