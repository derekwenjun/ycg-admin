@extends('layouts.app')

@section('content')
<div class="container">

    <ol class="breadcrumb">
      <li><a href="/">首页</a></li>
      <li class="active">所有网点</li>
    </ol>
    
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        管理系统暂不提供网点管理功能，如需更新网点信息请联系 <strong>wenjun.guo@outlook.com</strong>
    </div>

    <form action="{{Route('user.index')}}" method="GET">
        <table class="table table-hover table-striped">
            <thead>
            <tr>
                <td>#</td>
                <td>网点名称</td>
                <td>网点邮箱</td>
                <td>账户余额</td>
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
                    <td><input type="text" class="form-control input-sm" value="" disabled /></td>
                    <td><!-- 创建时间 --></td>
                    <td><button type="submit" class="btn btn-primary btn-sm">筛选</button></td>
                </tr>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td><i class="fa fa-title fa-eur" aria-hidden="true"></i>{{ $user->receivable }}</td>
                    <td>普通网点</td>
                    <td>{{ $user->created_at }}</td>
                    <td>
                        <a href="{{ route('user.show', ['id' => $user->id]) }}">详情</a>&nbsp;|&nbsp;
                        <a href="#">编辑</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </form>
</div>
@endsection
