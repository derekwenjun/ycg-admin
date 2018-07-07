@extends('layouts.app')

@section('content')
<div class="container">

    <ol class="breadcrumb">
      <li><a href="/">首页</a></li>
      <li><a href="{{ url('/batch') }}">全部批次</a></li>
      <li class="active">批次详情 - 批次号：{{ $batch->id }}</li>
    </ol>
    
    <!-- 显示提示信息，如有 -->
    @if (session('status'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {{ session('status') }}
        </div>
    @endif
    
    <!-- 显示错误信息，如有 -->
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-3">
        		
        		@if ($batch->status_id == 0)
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-title fa-search" aria-hidden="true"></i>扫描运单号添加包裹进入批次</div>                
                <div class="panel-body">
                    <form class="form-inline" action="{{ route('batch.add', ['id'=>$batch->id]) }}" method="POST">
                    {{ csrf_field() }}
                      <div class="form-group">
                        <label for="no" class="sr-only">no</label>
                        <input type="text" class="form-control" id="no" name="no" placeholder="No.">
                      </div>
                      &nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-primary">添加包裹</button>
                    </form>
                </div>
            </div>
            @endif
            
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-title fa-cog" aria-hidden="true"></i>可用操作 | 目前批次状态 - <strong>{{ $batch->status->name }}</strong></div>
                <div class="panel-body">
                @if ($batch->status_id == 0)
                    <button data-toggle="modal" data-target="#to1model" class="btn btn-primary"><i class="fa fa-title fa-truck" aria-hidden="true"></i>批次从集运仓出库</button>
                @elseif ($batch->status_id == 1)
                    <button data-toggle="modal" data-target="#to5model" class="btn btn-primary"><i class="fa fa-title fa-plane" aria-hidden="true"></i>批次航班起飞</button>
                @elseif ($batch->status_id == 5)
                    <button data-toggle="modal" data-target="#to10model" class="btn btn-primary">批次航班到达</button>
                @elseif ($batch->status_id == 10)
                    <button data-toggle="modal" data-target="#to15model" class="btn btn-primary"><i class="fa fa-title fa-caret-square-o-up" aria-hidden="true"></i>批次从机场提货</button>
                @elseif ($batch->status_id == 15)
                    <button data-toggle="modal" data-target="#to20model" class="btn btn-primary"><i class="fa fa-title fa-calendar-check-o" aria-hidden="true"></i>批次清关中</button>
                @elseif ($batch->status_id == 20)
                    <button data-toggle="modal" data-target="#to25model" class="btn btn-primary"><i class="fa fa-title fa-opencart" aria-hidden="true"></i>批次转交落地配</button>
                @else
                		<p>批次由当地物流公司配送中...</p>
                @endif
                </div>
            </div>
            
        </div>
        <div class="col-md-5">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-title fa-info-circle" aria-hidden="true"></i>批次信息</div>
                <div class="panel-body">
        			<table class="table table-hover table-striped">
        				<tbody>
                        <tr>
                            <td>批次号:</td><td>#{{ $batch->id }}</td>
                        </tr>
                        <tr>
                            <td>备注:</td><td>{{ $batch->remark }}</td>
                        </tr>
                        <tr>
                            <td>创建时间:</td><td>{{ $batch->created_at }}</td>
                        </tr>
                        <tr>
                            <td>当前状态:</td><td>{{ $batch->status->name }}</td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-title fa-random" aria-hidden="true"></i>批次追踪</div>
                <div class="panel-body">
				<table class="table table-striped">
                    <thead><tr>
                        <th>时间</th>
                        <th>追踪信息</th>
                    </tr></thead>
                    <tbody>
                    @foreach ($batch->trackings as $tracking)
                        <tr>
                            <td>{{ $tracking->created_at }}</td>
                            <td>{{ $tracking->description }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
              </div>
            </div>
            
        </div>
    </div>
    
    <div class="panel panel-default">
        <div class="panel-heading"><i class="fa fa-title fa-cubes" aria-hidden="true"></i>包裹列表 - 共 <strong>{{ $batch->orders_count }}</strong> 个包裹</div>
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
                <td>操作</td>
            </tr>
            </thead>
            <tbody>
            @foreach($batch->orders as $order)
                <tr class="{{ $order->status_id == 0 ? 'warning' : ( $order->status_id >= 2 ? 'success' : '' ) }}">
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
                    <td>
                        <a target="_blank" href="{{ route('order.show', ['id' => $order->id]) }}" class="btn-sm btn-primary">详情</a>
                        @if ($batch->status_id == 0)
                        <a class="btn-sm btn-danger" data-toggle="modal" data-target=".deleteConfirm" onclick="confirmDelete('{{Route('batch.remove', ['id'=>$order->id])}}')">从批次移除</a>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        </div>
    </div>
    
    <!-- 出库用 Modal -->
    <div class="modal fade" id="to1model" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <form action="{{ Route('batch.to1', ['id' => $batch->id]) }}" method="POST">
                    {!! csrf_field() !!}
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">确认批次出库</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            该批次下所有订单都将添加<strong>出库</strong>相关的物流追踪信息
                        </div>
                        <p></p>
                        <div class="form-group">
                            <label for="tip">追踪信息提示</label>
                            <input type="text" class="form-control" id="tip" name="tip" placeholder="">
                            <span id="helpBlock" class="help-block">例如：下一站 杭州市 杭州萧山国际机场</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-primary">确认出库</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- 起飞用 Modal -->
    <div class="modal fade" id="to5model" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <form action="{{ Route('batch.to5', ['id' => $batch->id]) }}" method="POST">
                    {!! csrf_field() !!}
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">确认批次航班起飞</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            该批次下所有订单都将添加<strong>航班起飞</strong>相关的物流追踪信息
                        </div>
                        <p></p>
                        <div class="form-group">
                            <label for="tip">航班号</label>
                            <input type="text" class="form-control" id="tip" name="tip" placeholder="">
                            <span id="helpBlock" class="help-block">例如：JD362</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-primary">确认航班起飞</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- 到达用 Modal -->
    <div class="modal fade" id="to10model" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <form action="{{ Route('batch.to10', ['id' => $batch->id]) }}" method="POST">
                    {!! csrf_field() !!}
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">确认批次航班到达</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            该批次下所有订单都将添加<strong>航班到达</strong>相关的物流追踪信息
                        </div>
                        <p></p>
                        <div class="form-group">
                            <label for="tip">到达城市</label>
                            <input type="text" class="form-control" id="city" name="city" placeholder="">
                            <span id="helpBlock" class="help-block">例如：杭州市</span>
                        </div>
                        <div class="form-group">
                            <label for="tip">到达机场</label>
                            <input type="text" class="form-control" id="airport" name="airport" placeholder="">
                            <span id="helpBlock" class="help-block">例如：萧山国际机场</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-primary">确认航班到达</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- 提货用 Modal -->
    <div class="modal fade" id="to15model" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <form action="{{ Route('batch.to15', ['id' => $batch->id]) }}" method="POST">
                    {!! csrf_field() !!}
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">确认批次已从机场提货</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            该批次下所有订单都将添加<strong>从机场提货</strong>相关的物流追踪信息
                        </div>
                        <p></p>
                        <div class="form-group">
                            <label for="tip">所在城市</label>
                            <input type="text" class="form-control" id="city" name="city" placeholder="">
                            <span id="helpBlock" class="help-block">例如：杭州市</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-primary">确认已从机场提货</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- 清关用 Modal -->
    <div class="modal fade" id="to20model" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <form action="{{ Route('batch.to20', ['id' => $batch->id]) }}" method="POST">
                    {!! csrf_field() !!}
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">确认批次正在清关</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            该批次下所有订单都将添加<strong>正在清关</strong>相关的物流追踪信息
                        </div>
                        <p></p>
                        <div class="form-group">
                            <label for="city">所在城市</label>
                            <input type="text" class="form-control" id="city" name="city" placeholder="">
                            <span id="helpBlock" class="help-block">例如：杭州市</span>
                        </div>
                        <div class="form-group">
                            <label for="port">口岸名</label>
                            <input type="text" class="form-control" id="port" name="port" placeholder="">
                            <span id="helpBlock" class="help-block">例如：萧山机场口岸</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-primary">确认正在清关</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- 转落地配用 Modal -->
    <div class="modal fade" id="to25model" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <form action="{{ Route('batch.to25', ['id' => $batch->id]) }}" method="POST">
                    {!! csrf_field() !!}
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">确认批次已转交落地配</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            该批次下所有订单都将添加<strong>已转交落地配</strong>相关的物流追踪信息
                        </div>
                        <p></p>
                        <div class="form-group">
                            <label for="city">所在城市</label>
                            <input type="text" class="form-control" id="city" name="city" placeholder="">
                            <span id="helpBlock" class="help-block">例如：杭州市</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-primary">确认已转交落地配关</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- xxx 删除包裹用MODAL xxx -->
    <div class="modal fade deleteConfirm" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form method="POST">
                    {!! csrf_field() !!}
                    <input name="bid" type="hidden" value="{{ $batch->id }}">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">操作确认</h4>
                    </div>
                    <div class="modal-body">
                        <p>确认从批次中删除该包裹？</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-primary">删除</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function confirmDelete(action) {
            $('.deleteConfirm form').attr('action', action);
        }
    </script>
    
</div>
@endsection
