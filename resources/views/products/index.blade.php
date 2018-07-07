@extends('layouts.app')

@section('content')

<div class="modal fade" tabindex="-1" role="dialog" id="createModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">创建新商品</h4>
      </div>

      <form id="productForm" action="{{ Route('product.store') }}" method="POST">
      
      {{ csrf_field() }}

      <div class="modal-body">
        
        <div class="row">
          <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="name"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;商品名</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="商品名" required>
              </div>
          </div>
          <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="category"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;分类</label>
                <select class="form-control" id="category_id" name="category_id" required>
                  @foreach ($categories as $category)
                  <option value="{{ $category->id }}">{{ $category->name }}</option>
                  @endforeach
                </select>
              </div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="upc"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;型号</label>
				<input type="text" class="form-control" id="model" name="model" placeholder="型号" required>
              </div>
          </div>
          <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="upc">条形码</label>
                <input type="text" class="form-control" id="upc" name="upc" placeholder="条形码">
              </div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="weight">重量</label>
                <div class="input-group">
					<input type="number" step="0.01" class="form-control" id="weight" name="weight">
					<span class="input-group-addon">KG</span>
				</div>
              </div>
          </div>
          <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="price">申报价</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price">
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
          $("#addressForm").validate();
      });
      </script>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="container">

    <ol class="breadcrumb">
      <li><a href="/">首页</a></li>
      <li class="active">全部商品</li>
      <!-- Button trigger modal -->
        <span class="pull-right">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#createModal">
                <i class="fa fa-plus fa-title" aria-hidden="true"></i>添加新商品
            </button>
        </span>
    </ol>

    <form action="{{Route('order.index')}}" method="GET">
        <table class="table table-hover table-striped">
            <thead>
            <tr>
                <td>#</td>
                <td>商品名</td>
                <td>分类</td>
                <td>型号</td>
                <td>条形码</td>
                <td>重量</td>
                <td>申报价</td>
                <td>操作</td>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td><input type="text" class="form-control input-sm" name="name" value="{{ old('name') }}"/></td>
                    <td></td>
                    <td><input type="text" class="form-control input-sm" name="model" value="{{ old('model') }}"/></td>
                    <td><input type="text" class="form-control input-sm" name="upc" value="{{ old('upc') }}"/></td>
                    <td><input type="number" class="form-control input-sm" name="weight" value="{{ old('weight') }}"/></td>
                    <td><input type="number" class="form-control input-sm" name="price" value="{{ old('price') }}"/></td>
                    <td><button type="submit" class="btn btn-primary btn-sm">筛选</button></td>
                </tr>
            @foreach ($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category->name }}</td>
                    <td>{{ $product->model }}</td>
                    <td>{{ $product->upc }}</td>
                    <td>{{ $product->weight }} KG</td>
                    <td>{{ $product->price }}</td>
                    <td>
                        <a href="#" data-toggle="modal" data-target="#modelAddress{{ $product->id }}">编辑</a>
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
