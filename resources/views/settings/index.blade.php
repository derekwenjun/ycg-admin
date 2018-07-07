@extends('layouts.app')

@section('content')

<div class="modal fade" tabindex="-1" role="dialog" id="clientModal">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">修改客户费率</h4>
      </div>

      <form id="clientForm" action="{{ Route('setting.store') }}" method="POST">
      
      {{ csrf_field() }}

      <div class="modal-body">
        <div class="row">
          <div class="col-sm-12">
              <div class="form-group">
                  <div class="input-group">
    					<div class="input-group-addon"><i class="fa fa-eur" aria-hidden="true"></i></div>
                    <input type="number" step="0.01" class="form-control" id="client_rate" name="client_rate" value="{{ $client_rate }}" required>
                  </div>
              </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary">保存</button>
      </div>
      <script>
      $( document ).ready(function() {
          $("#clientForm").validate();
      });
      </script>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="container">

    <ol class="breadcrumb">
      <li><a href="/">首页</a></li>
      <li class="active">系统设置</li>
    </ol>

    <div class="row">
        <div class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-title fa-building" aria-hidden="true"></i>城市列表</div>
                <div class="panel-body">
					<table class="table table-striped">
                        <thead><tr>
                            <th>#</th>
                            <th>城市名称</th>
                        </tr></thead>
                        <tbody>
                        @foreach ($cities as $city)
                            <tr>
                                <th scope="row">{{ $city->id }}</th>
                                <td>{{ $city->name }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-title fa-euro" aria-hidden="true"></i></i>系统费率</div>
                <div class="panel-body">

                    <table class="table table-striped">
                        <thead><tr>
                            <th>#</th>
                            <th>项目</th>
                            <th>费率</th>
                            <th>描述</th>
                            <th>操作</th>
                        </tr></thead>
                        <tbody>
                            <tr>
                                <th scope="row">1</th>
                                <td>客户费率</td>
                                <td>
                                		<input type="text" class="form-control input-sm" value="{{ $client_rate }}" disabled />
                                	</td>
                                <td>网店收取客户的每公斤费率，欧元计算</td>
                                <td>
                                    <button type="submit" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#clientModal">
                                        <i class="fa fa-edit fa-title" aria-hidden="true"></i>修改
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">2</th>
                                <td>网点费率</td>
                                <td><input type="text" class="form-control input-sm" value="{{ $user_rate }}" disabled /></td>
                                <td>公司收取网点的每公斤费率，欧元计算</td>
                                <td>
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fa fa-edit fa-title" aria-hidden="true"></i>修改
                                    </button>
                                </td>
                            </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
