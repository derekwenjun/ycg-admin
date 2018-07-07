@extends('layouts.app')

@section('content')
<div class="container">

    <ol class="breadcrumb">
      <li><a href="/">首页</a></li>
      <li class="active">发货做单 - 物流方式</li>
    </ol>

    <div class="row" style="margin-bottom:40px;">
        <div class="col-md-8 col-md-offset-2">
            <h4 class="text-center"><span class="label label-primary">1</span>&nbsp;&nbsp;&nbsp;&nbsp;
            <span class="text-primary">选择物流方式</span>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-arrow-right text-muted" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span class="text-center text-muted"><span class="label label-default">2</span>&nbsp;&nbsp;&nbsp;&nbsp;填写面单信息</span>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-arrow-right text-muted" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span class="text-center text-muted"><span class="label label-default">3</span>&nbsp;&nbsp;&nbsp;&nbsp;填写商品信息</span>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-arrow-right text-muted" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span class="text-center text-muted"><span class="label label-default">4</span>&nbsp;&nbsp;&nbsp;确认付款</span>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-arrow-right text-muted" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span class="text-center text-muted"><span class="label label-default">5</span>&nbsp;&nbsp;&nbsp;完成</span>
            </h4>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-title fa-random" aria-hidden="true"></i>分拨中心</div>
                <div class="panel-body">
                  <form class="form-horizontal">
                  <div class="form-group">
                    <label class="control-label col-sm-4" for="city"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;选择分拨中心:</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="city" name="city" disabled>
                          <option>MADRID - España</option>
                        </select>
                    </div>
                    <span id="helpBlock" class="help-block col-sm-8 col-sm-offset-4">分拨中心目前无法修改</span>
                  </div>
                  </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-title fa-truck" aria-hidden="true"></i></i>发货类型</div>

                <div class="panel-body">
                    <div class="alert alert-warning alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        选择类型后，订单会被即刻创建，未完成的订单可以在订单列表中编辑或删除
                    </div>
                <!--
                    <div class="row">
                        <div class="col-sm-4">
                            <button type="button" class="btn btn-primary btn-lg">
                                BC包裹发货&nbsp;<i class="fa fa-arrow-circle-right" aria-hidden="true"></i>
                            </button>
                        </div>
                        <div class="col-sm-4">
                            <button type="button" class="btn btn-success btn-lg">
                                个人行邮发货&nbsp;<i class="fa fa-arrow-circle-right" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                -->
                <table class="table table-striped">
                    <thead><tr>
                        <th>#</th>
                        <th>Tipo</th>
                        <th>Port Description</th>
                        <th>Operación</th>
                    </tr></thead>
                    <tbody>
                        <tr>
                            <form method="POST" action="{{ url('/order/store') }}">
                            {{ csrf_field() }}
                            <input type="hidden" name="type" value="0">
                            <input type="hidden" name="source" value="在线录入">
                            <th scope="row">1</th> <td>BC包裹</td>
                            <td>固定税率11.9%，需身份信息，时效3-5天</td>
                            <td>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    BC包裹发货&nbsp;&nbsp;&nbsp;<i class="fa fa-arrow-circle-right" aria-hidden="true"></i>
                                </button>
                            </td>
                            </form>
                        </tr>
                        <tr>
                            <th scope="row">2</th><td>个人行邮</td>
                            <td>Thornton</td> <td>@fat</td>
                        </tr>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
