@extends('layouts.app')

@section('content')
<div class="container">

<div class="modal fade" tabindex="-1" role="dialog" id="selectModal" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">从商品库中选择</h4>
      </div>

      <div class="modal-body">
        <div class="row">
          <div class="col-md-12 form-horizontal">
              <div class="form-group">
                <label class="control-label col-sm-2" for="search_input">Search Product:&nbsp;</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="search_input" placeholder="输入商品名称搜索">
                </div>
              </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <table id="result_table" class="table table-hover table-striped">
            <thead>
            <tr>
                <td>#</td>
                <td>Product Name</td>
                <td>Category</td>
                <td>Model</td>
                <td>Declared Value</td>
                <td>Operation</td>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
        <button type="submit" class="btn btn-primary">保存 Save</button>
      </div>
      <script>
      $(function() {

          function search() {
              var queryStr = $("#search_input").val();
              console.log(queryStr);

              $.ajax({
                  // The URL for the request
                  url: "{{ url('/product/search') }}",
                  // The data to send (will be converted to a query string)
                  data: {
                      query : queryStr,
                      _token : "{{ csrf_token() }}"
                  },
                  type: "POST",
                  // The type of data we expect back
                  dataType : "json",
              })
              // Code to run if the request succeeds (is done);
              // The response is passed to the function
              .done(function( json ) {
                  $("#result_table > tbody").empty();
                  $.each(json.products, function( key, value ) {
                      var elem = $("<tr><td>1</td>" + 
                        "<td>" + value.name + "</td>" + 
                        "<td>" + value.category.name + "</td>" +
                        "<td>" + value.model + "</td>" +
                        "<td>" + value.price + "</td>" + 
                        "<td><a id=\"select_btn\" href=\"#\">Select</a></td>" + 
                        "</tr>");
                      elem.find("#select_btn").data("pid", value.id);
                      elem.appendTo($("#result_table > tbody"));
                  });
                  // 为每一个选择按钮添加事件
                  $("#result_table #select_btn").each(function( idx, elem ) {
                      var element = $( elem );
                      element.click(function(){
                          event.preventDefault();

                          // 填写数据
                          var pid = element.data("pid");
                          var obj = undefined;
                          $.each(json.products, function( key, value ) {
                              if(value.id == pid) {
                                obj = value;
                              }
                          });
                          console.log(obj);

                          var target = $("#product_list").find("#product_" + $("#selectModal").data("idx"));
                          target.find("select#category").val(obj.category_id);
                          target.find("input#name").val(obj.name);
                          target.find("input#model").val(obj.model);
                          target.find("input#number").val("1");
                          target.find("input#price").val(obj.price);

                          // 隐藏选择modal
                          $("#selectModal").modal('hide');
                      });
                  });
              })
              // Code to run if the request fails; the raw request and
              // status codes are passed to the function
              .fail(function( xhr, status, errorThrown ) {
                  alert( "Sorry, there was a problem!" );
                  console.log( "Error: " + errorThrown );
                  console.log( "Status: " + status );
                  console.dir( xhr );
              })
              
              // Code to run regardless of success or failure;
              .always(function( xhr, status ) {
                  console.log("The request is complete!");
              });
          }

          $("#search_input").on('keyup', function (event) {
              // If enter key is pressed check if the user wants to select hovered row
              var keycode = event.keyCode || event.which;
              if (keycode === 13) {

              } else {
                  // If something other than enter is pressed start search immediately
                  search();
              }
          });
      });
      </script>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


    <ol class="breadcrumb">
      <li><a href="/">首页</a></li>
      <li class="active">发货做单 - 物流方式</li>
    </ol>

    <div class="row" style="margin-bottom:30px;">
        <div class="col-md-8 col-md-offset-2">
            <h4 class="text-center">
            <span class="text-center text-success">
              <span class="label label-success step-label-finished"><i class="fa fa-check" aria-hidden="true"></i></span>
              &nbsp;&nbsp;&nbsp;Parcel Type
            </span>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-arrow-right text-success" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span class="text-center text-success">
                <span class="label label-success step-label-finished"><i class="fa fa-check" aria-hidden="true"></i></span>
                &nbsp;&nbsp;&nbsp;&nbsp;Waybill Info
            </span>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-arrow-right text-success" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span class="text-center text-success">
              <span class="label label-success">3</span>&nbsp;&nbsp;&nbsp;&nbsp;<strong>Products Info</strong>
            </span>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-arrow-right text-muted" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span class="text-center text-muted">
              <span class="label label-default step-label-todo">4</span>&nbsp;&nbsp;&nbsp;Payment
            </span>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-arrow-right text-muted" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span class="text-center text-muted">
              <span class="label label-default step-label-todo">5</span>&nbsp;&nbsp;&nbsp;Done
            </span>
            </h4>
        </div>
    </div>

  <div class="row">
    <div class="col-md-10 col-md-offset-1">
      <div class="alert alert-success alert-dismissible" role="alert" style="line-height:24px;">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            Parcel Type &nbsp;&nbsp;<i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;&nbsp; {{ $order->type == 0 ? 'BC Parcel' : 'Personal Mail' }}&nbsp;&nbsp;|&nbsp;&nbsp; 
            Shippment No. &nbsp;&nbsp;<i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;&nbsp; {{ $order->no }}<br/>
            Shipper<span class="h-spacing-8">&nbsp;</span><i class="fa fa-arrow-right" aria-hidden="true"></i>
            <span class="h-spacing-8">{{ $order->name }}<span class="h-spacing-8">&nbsp;</span>|<span class="h-spacing-8">&nbsp;</span>{{ $order->address }}
            <br/>
            Receiver<span class="h-spacing-8"></span><i class="fa fa-arrow-right" aria-hidden="true"></i>
            <span class="h-spacing-8">{{ $order->shipping_name }}<span class="h-spacing-8">|<span class="h-spacing-8">{{ $order->shipping_address }}
      </div>
    </div>
  </div>

    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <!-- order products form -->
            <form action="{{ url('order/store_products') }}" method="POST">
            {{ csrf_field() }}
            <input type="hidden" name="oid" value="{{ $order->id }}">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-title fa-archive" aria-hidden="true"></i></i><strong>Products</strong></div>
                <div class="panel-body">
                    <!-- 产品列表 begin -->
                    <div id="product_list">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                              <div class="col-md-3">
                                  <div class="form-group">
                                    <label class="control-label" for="name"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;Product Category</label>
                                    <select class="form-control" id="category" name="products[0][category]" required>
                                      <option value=""></option>
                                      <option value="1">彩妆护理</option>
                                      <option value="2">车载/旅游/户外</option>
                                      <option value="3">服饰家纺</option>
                                    </select>
                                  </div>
                              </div>
                              <div class="col-md-3">
                                  <div class="form-group">
                                    <label class="control-label" for="mobile"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;Name</label>
                                    <input type="text" class="form-control" id="name" name="products[0][name]" value="" required>
                                  </div>
                              </div>
                              <div class="col-md-2">
                                  <div class="form-group">
                                    <label class="control-label" for="mobile"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;Model</label>
                                    <input type="text" class="form-control" id="model" name="products[0][model]" value="" required>
                                  </div>
                              </div>
                              <div class="col-md-2">
                                  <div class="form-group">
                                    <label class="control-label" for="mobile"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;Number</label>
                                    <input type="number" class="form-control" id="number" name="products[0][number]" value="" required>
                                  </div>
                              </div>
                              <div class="col-md-2">
                                  <div class="form-group">
                                    <label class="control-label" for="mobile"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;Declared Value</label>
                                    <input type="text" class="form-control" id="price" name="products[0][price]" value="" required>
                                  </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-12">
                                  <strong id="prod_idx">#1</strong>
                                  <span class="h-spacing-8"></span>
                                  <a id="prod_select_btn" href="#">从商品库选择</a>
                                  <span class="h-spacing-8"></span>
                                  <a id="prod_delete_btn" href="#"><i class="fa fa-times fa-btn" aria-hidden="true"></i>删除</a>
                              </div>
                            </div>
                        </div>
                    </div>
                    </div>
                    <!-- 产品列表 end -->

                    <p><button id="one_more_btn" type="button" class="btn btn-default btn-lg btn-block dashed-border">
                      <i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;Add One More Product
                    </button></p>

                    <hr/>

                    <div class="row form-inline">
                        <div class="col-md-12">
                            <div class="form-group pull-right">
                                <label class="control-label" for="mobile">Total Value : 600.00 &nbsp;&nbsp;|&nbsp;&nbsp; Parcel Weight :&nbsp;</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="mobile" name="mobile" value="" required>
                                    <div class="input-group-addon">KG</div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                  <button type="submit" class="btn btn-primary pull-right">
                    &nbsp;Next&nbsp;&nbsp;<i class="fa fa-arrow-circle-right" aria-hidden="true"></i>&nbsp;
                  </button>
                </div>
            </div>
            </form>
        </div>

        <script>
        $(function() {

            function addOneMoreProduct() {
                // Clone the first node and append it to the list
                var elem = $( "#product_list div:first" ).clone();

                elem.find("#category").val("");
                elem.find("#name").val("");
                elem.find("#model").val("");
                elem.find("#number").val("");
                elem.find("#price").val("");

                elem.appendTo( "#product_list" );
                elem.hide().fadeIn(500);
                sortProductList();
            }

            function sortProductList() {
                // Add event listener for every delete button
                $( "#product_list > div" ).each(function( index ) {
                    
                    $(this).data("idx", index);
                    $(this).attr("id", "product_" + index);

                    $(this).find("#prod_idx").text('#' + (index + 1));

                    // 重置所有输入项name
                    $(this).find("#category").attr("name", "products[" + index + "][category]");
                    $(this).find("#name").attr("name", "products[" + index + "][name]");
                    $(this).find("#model").attr("name", "products[" + index + "][model]");
                    $(this).find("#number").attr("name", "products[" + index + "][number]");
                    $(this).find("#price").attr("name", "products[" + index + "][price]");

                    $(this).find("#prod_delete_btn").data("idx", index);
                    $(this).find("#prod_delete_btn").click(deleteClick);

                    $(this).find("#prod_select_btn").data("idx", index);
                    $(this).find("#prod_select_btn").click(selectClick);
                });
            }
            // 初始化商品列表
            sortProductList();

            // 给增加按钮添加监听
            $( "#one_more_btn" ).click(addOneMoreProduct);

            // 打印订单对应的商品数组
            var ops = [];
            @foreach ($ops as $op)
            ops.push({name : "{{ $op -> name }}", 
              category_id : "{{ $op -> category_id }}", 
              model : "{{ $op -> model }}",
              number : "{{ $op -> number }}", 
              price : "{{ $op -> price }}"});
            @endforeach

            // 打印已有的商品数组
            for(var i = 0; i < ops.length; i++) {
                var op = ops[i];
                if(i > 0) addOneMoreProduct();
                var target = $("#product_list").find("#product_" + i);
                target.find("select#category").val(op.category_id);
                target.find("input#name").val(op.name);
                target.find("input#model").val(op.model);
                target.find("input#number").val(op.number);
                target.find("input#price").val(op.price);
            }

            function deleteClick() {
                event.preventDefault();
                // 第一个节点不删除
                var index = $(this).data("idx");
                if(index == 0) return;
                $("#product_" + index).remove();

                sortProductList();
            }

            function selectClick() {
                event.preventDefault();
                var index = $(this).data("idx");
                $("#selectModal").data("idx", index);
                $("#selectModal").modal();
            }
        });
        </script>

    </div>

    <script>
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
    </script>
</div>
@endsection
