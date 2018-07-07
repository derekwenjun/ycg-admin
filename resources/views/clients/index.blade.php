@extends('layouts.app')

@section('content')
<div class="container">

    <ol class="breadcrumb">
      <li><a href="/">Home</a></li>
      <li class="active">All Clients</li>
    </ol>

    <form action="{{Route('client.index')}}" method="GET">
        <table class="table table-hover table-striped">
            <thead>
            <tr>
                <td>#</td>
                <td>姓名</td>
                <td>电子邮件</td>
                <td>账户余额</td>
                <td>注册网点</td>
                <td>用户等级</td>
                <td>创建时间</td>
                <td>操作</td>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td><input type="text" class="form-control input-sm" name="name" value="{{ old('name') }}"/></td>
                    <td><input type="text" class="form-control input-sm" name="model" value="{{ old('model') }}"/></td>
                    <td><input type="text" class="form-control input-sm" name="upc" value="{{ old('receivable') }}"/></td>
                    <td><!-- 注册网点 --></td>
                    <td><input type="text" class="form-control input-sm" value="" disabled /></td>
                    <td><!-- 创建时间 --></td>
                    <td><button type="submit" class="btn btn-primary btn-sm">Filter</button></td>
                </tr>
            @foreach ($clients as $client)
                <tr>
                    <td>{{ $client->id }}</td>
                    <td>{{ $client->name }}</td>
                    <td>{{ $client->mobile }}</td>
                    <td><i class="fa fa-title fa-eur" aria-hidden="true"></i>{{ $client->balance }}</td>
                    <td>{{ $client->user->name }}</td>
                    <td>Normal</td>
                    <td>{{ $client->created_at }}</td>
                    <td>
                        <a href="{{ route('client.show', ['id' => $client->id]) }}">详情</a>&nbsp;|&nbsp;
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </form>
</div>
@endsection
