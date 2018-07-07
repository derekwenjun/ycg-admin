@extends('layouts.app')

@section('content')

<div class="modal fade" tabindex="-1" role="dialog" id="createModal">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">创建新的批次</h4>
      </div>

      <form id="batchForm" action="{{ Route('batch.store') }}" method="POST">
      {{ csrf_field() }}

      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="remark"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;备注</label>
                <input type="text" class="form-control" name="remark" placeholder="添加批次备注" required>
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
          $("#batchForm").validate();
      });
      </script>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="container">

    <ol class="breadcrumb">
        <li><a href="/">首页</a></li>
        <li class="active">全部批次</li>
        
        <!-- Button trigger modal -->
        <span class="pull-right">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#createModal">
                <i class="fa fa-plus fa-title" aria-hidden="true"></i>创建新批次
            </button>
        </span>
    </ol>
    
    <div class="alert alert-info" role="alert">
    		批次状态流：已创建 => 总仓已出库 => 航班已起飞 => 已到达国内 => 已从机场提货 => 正在清关中 => 已转交落地配
    </div>

    <form action="{{Route('batch.index')}}" method="GET">
        <table class="table table-hover table-striped">
            <thead>
            <tr>
                <td>#</td>
                <td>状态</td>
                <td>包裹数</td>
                <td>备注</td>
                <td>创建时间</td>
                <td>操作</td>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" class="form-control input-sm" name="id" value="{{ old('id') }}" style="width:100px;"/></td>
                    <td><!-- 批次状态 --></td>
                    <td></td>
                    <td><input type="text" class="form-control input-sm" name="remark" value="{{ old('remark') }}"/></td>
                    <td><!-- 创建时间 --></td>
                    <td><button type="submit" class="btn btn-primary btn-sm">筛选</button></td>
                </tr>
            @foreach ($batches as $batch)
                <tr>
                    <td>{{ $batch->id }}</td>
                    <td>{{ $batch->status->name }}</td>
                    <td>{{ $batch->orders_count }}</td>
                    <td>{{ $batch->remark }}</td>
                    <td>{{ $batch->created_at }}</td>
                    <td>
                        <a href="{{ route('batch.show', ['id' => $batch->id]) }}" class="btn-sm btn-primary">批次详情</a>
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
