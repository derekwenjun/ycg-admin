@extends('layouts.app')

@section('content')
<div class="container">

  <ol class="breadcrumb">
    <li><a href="/">首页</a></li>
    <li class="active">发货做单 - 物流方式</li>
  </ol>

  <div class="row" style="margin-bottom:40px;">
      <div class="col-md-8 col-md-offset-2">
          <h4 class="text-center">
          <span class="text-center text-success">
            <span class="label label-success step-label-finished"><i class="fa fa-check" aria-hidden="true"></i></span>
            &nbsp;&nbsp;&nbsp;Parcel Type
          </span>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-arrow-right text-success" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <span class="text-center text-success">
            <span class="label label-success">2</span>&nbsp;&nbsp;&nbsp;&nbsp;Waybill Info
          </span>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-arrow-right text-muted" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <span class="text-center text-muted"><span class="label label-default step-label-todo">3</span>&nbsp;&nbsp;&nbsp;&nbsp;Products Info</span>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-arrow-right text-muted" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <span class="text-center text-muted"><span class="label label-default step-label-todo">4</span>&nbsp;&nbsp;&nbsp;Payment</span>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-arrow-right text-muted" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <span class="text-center text-muted"><span class="label label-default step-label-todo">5</span>&nbsp;&nbsp;&nbsp;Done</span>
          </h4>
      </div>
  </div>

  <div class="row">
    <div class="col-md-10 col-md-offset-1">
      <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            Parcel Type &nbsp;&nbsp;<i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;&nbsp; {{ $order->type == 0 ? 'BC Parcel' : 'Personal Mail' }} 
            &nbsp;&nbsp;|&nbsp;&nbsp; 
            Shippment No. &nbsp;&nbsp;<i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;&nbsp; {{ $order->no }}
      </div>
    </div>
  </div>


  <form action="{{ url('order/store_waybill') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="oid" value="{{ $order->id }}">
    <div class="row">
        <div class="col-md-5 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-title fa-address-book" aria-hidden="true"></i><strong>Shipper</strong></div>
                <div class="panel-body">

                <div class="input-group" style="margin-bottom:10px;">
                  <span class="input-group-addon"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></span>
                  <input type="text" class="form-control" placeholder="搜索已有发货人" aria-label="...">
                  <div class="input-group-btn">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Select Shipper <span class="caret"></span>
                    </button>
                    <ul id="address_list" class="dropdown-menu dropdown-menu-right">
                        <li><a href="#">[ Default Shipper ]</a></li>
                        <li role="separator" class="divider"></li>
                        @foreach ($addresses as $address)
                            <li><a class="text-info" href="#" data-aid="{{ $address->id }}">
                                {{ $address->name }}&nbsp;&nbsp;-&nbsp;&nbsp;{{ $address->mobile }}&nbsp;&nbsp;-&nbsp;&nbsp;{{ $address->address }}</a>
                            </li>
                        @endforeach
                    </ul>
                  </div><!-- /btn-group -->
                </div><!-- /input-group -->
                
                <hr/>
                <div class="row">
                  <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label" for="name"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;Company or Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $order->name }}" required>
                      </div>
                  </div>
                  <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label" for="mobile"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;Mobile</label>
                        <input type="text" class="form-control" id="mobile" name="mobile" value="{{ $order->mobile }}" required>
                      </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label" for="country"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;Country</label>
                        <input type="text" class="form-control" id="country" value="España" disabled>
                        <input type="hidden" name="country" value="España">
                      </div>
                  </div>
                  <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label" for="city"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;City</label>
                        <select class="form-control" id="city" name="city" value="{{ $order->city }}">
                          @foreach ($cities as $city)
                          <option value="{{ $city->name }}">{{ $city->name }}</option>
                          @endforeach
                        </select>
                      </div>
                  </div>
                </div>
                <div class="form-group">
                    <label class="control-label" for="address"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;Address</label>
                    <input type="text" class="form-control" id="address" name="address" value="{{ $order->address }}" required>
                </div>
                <div class="row">
                  <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label" for="zip"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;ZIP Code</label>
                        <input type="text" class="form-control" id="zip" name="zip" value="{{ $order->zip }}" required>
                      </div>
                  </div>
                  <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label" for="tel">Telephone</label>
                        <input type="text" class="form-control" id="tel" name="tel" value="{{ $order->tel }}">
                      </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label" for="mailbox">Mailbox</label>
                        <input type="text" class="form-control" id="mailbox" name="mailbox" value="{{ $order->mailbox }}">
                      </div>
                  </div>
                </div>
                </div>
            </div>
        </div>

        <script>
        $(function() {
            
            var address_arr = [
                @foreach ($addresses as $address)
                {id: '{{ $address->id }}', 
                name: '{{ $address->name }}', 
                mobile: '{{ $address->mobile }}',
                city: '{{ $address->city }}',
                address: '{{ $address->address }}',
                zip: '{{ $address->zip }}',
                tel: '{{ $address->tel }}',
                mailbox: '{{ $address->mailbox }}'},
                @endforeach
            ];

            $( "#address_list li a" ).each( function( index, element ){
                $(this).on( "click", function() {
                    // 防止页面滚动
                    event.preventDefault();
                    // 获取发货地址id
                    $aid = $(this).data('aid');
                    $.each( address_arr, function( index, value ){
                        if( $aid == value.id) {
                            $('#name').val( value.name );
                            $('#mobile').val( value.mobile );
                            $('#city').val( value.city );
                            $('#address').val( value.address );
                            $('#zip').val( value.zip );
                            $('#tel').val( value.tel );
                            $('#mailbox').val( value.mailbox );
                        }
                    });
                });
            });

            function getStr(i){
                alert("自定义了函数getStr" + i);
            }

            console.log( "ready!" );
        });
        </script>


        <div class="col-md-5">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-title fa-address-book-o" aria-hidden="true"></i></i><strong>收货人地址</strong></div>
                <div class="panel-body">
                <div class="input-group" style="margin-bottom:10px;">
                  <span class="input-group-addon"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></span>
                  <input type="text" class="form-control" placeholder="搜索已有发货人" aria-label="...">
                </div><!-- /input-group -->
                
                <hr/>
                <div class="row">
                  <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label" for="shipping_name"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;收货人</label>
                        <input type="text" class="form-control" id="shipping_name" name="shipping_name" value="{{ $order->shipping_name }}" required>
                      </div>
                  </div>
                  <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label" for="shipping_mobile"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;手机</label>
                        <input type="text" class="form-control" id="shipping_mobile" name="shipping_mobile" value="{{ $order->shipping_mobile }}" placeholder="" required>
                      </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12">
                      <div class="form-group">
                        <label class="control-label"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;地区</label>
                        <div data-toggle="distpicker">
                          <div class="row">
                            <div class="col-md-4">
                              <select class="form-control" id="shipping_state" name="shipping_state" value="{{$order->shipping_state}}"></select>
                            </div>
                            <div class="col-md-4">
                              <select class="form-control" id="shipping_city" name="shipping_city" value="{{$order->shipping_city}}"></select>
                            </div>
                            <div class="col-md-4">
                              <select class="form-control" id="shipping_district" name="shipping_district" value="{{$order->shipping_district}}"></select>
                            </div>
                          </div>
                        </div>
                      </div>
                  </div>
                </div>
                <div class="form-group">
                    <label class="control-label" for="shipping_address"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;详细地址</label>
                    <input type="text" class="form-control" id="shipping_address" name="shipping_address" value="{{ $order->shipping_address }}" placeholder="" required>
                </div>
                <div class="row">
                  <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label" for="shipping_zip"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;邮编</label>
                        <input type="text" class="form-control" id="shipping_zip" name="shipping_zip" value="{{ $order->shipping_zip }}" placeholder="" required>
                      </div>
                  </div>
                  <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label" for="shipping_tel">固定电话</label>
                        <input type="text" class="form-control" id="shipping_tel" name="shipping_tel" value="{{ $order->shipping_tel }}" placeholder="">
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
        </div>
    </div>

  </form>

    <script src="{{ asset('js/distpicker.min.js') }}"></script>
    <script>
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
    </script>
</div>
@endsection
