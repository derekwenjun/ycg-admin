@extends('layouts.app')

@section('content')
<div class="container">

  <ol class="breadcrumb">
    <li><a href="/">Home</a></li>
    <li class="active">发货做单 - Payment</li>
  </ol>

  <div class="row" style="margin-bottom:30px;">
      <div class="col-md-8 col-md-offset-2">
          <h4 class="text-center">
          <span class="text-center text-success">
            <span class="label label-success step-label-finished"><i class="fa fa-check" aria-hidden="true"></i></span>
            &nbsp;&nbsp;&nbsp;Parcel Type
          </span>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-arrow-right text-success" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <span class="text-center text-success"><span class="label label-success step-label-finished">
            <i class="fa fa-check" aria-hidden="true"></i></span>&nbsp;&nbsp;&nbsp;&nbsp;Waybill Info
          </span>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-arrow-right text-success" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <span class="text-center text-success"><span class="label label-success step-label-finished">
            <i class="fa fa-check" aria-hidden="true"></i></span>&nbsp;&nbsp;&nbsp;&nbsp;Products Info
          </span>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-arrow-right text-success" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <span class="text-center text-success"><span class="label label-success">4</span>&nbsp;&nbsp;&nbsp;Payment</span>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-arrow-right text-muted" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <span class="text-center text-muted"><span class="label label-default">5</span>&nbsp;&nbsp;&nbsp;Done</span>
          </h4>
      </div>
  </div>

  <form action="{{ url('order/store_payment') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="oid" value="{{ $order->id }}">
    <div class="row">
        <div class="col-md-7 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-title fa-address-book" aria-hidden="true"></i><strong>Waybill</strong></div>
                <div class="panel-body">
                  <div class="row">
                    <div class="col-md-6">
                      <table class="table table-striped">
                        <thead><tr><th>Shipper</th> <th></th> </tr> </thead>
                        <tbody>
                          <tr><td>Name</td> <td>{{ $order->name }}</td> </tr>
                          <tr><td>Mobile</td><td>{{ $order->mobile }}</td></tr>
                          <tr><td>Country</td><td>{{ $order->country }}</td></tr>
                          <tr><td>City</td><td>{{ $order->city }}</td></tr>
                          <tr><td>Address</td><td>{{ $order->address }}</td></tr>
                          <tr><td>ZIP</td><td>{{ $order->zip }}</td></tr>
                          <tr><td>Tel</td><td>{{ $order->tel }}</td></tr>
                          <tr><td>Mailbox</td><td>{{ $order->mailbox }}</td></tr>
                        </tbody>
                      </table>
                    </div>
                    <div class="col-md-6">
                      <table class="table table-striped">
                        <thead><tr><th>Receiver</th> <th></th> </tr> </thead>
                        <tbody>
                          <tr><td>Name</td> <td>{{ $order->shipping_name }}</td> </tr>
                          <tr><td>Mobile</td><td>{{ $order->shipping_mobile }}</td></tr>
                          <tr><td>State</td><td>{{ $order->shipping_state }}</td></tr>
                          <tr><td>City</td><td>{{ $order->shipping_city }}</td></tr>
                          <tr><td>District</td><td>{{ $order->shipping_district }}</td></tr>
                          <tr><td>Address</td><td>{{ $order->shipping_address }}</td></tr>
                          <tr><td>ZIP</td><td>{{ $order->shipping_zip }}</td></tr>
                          <tr><td>Tel</td><td>{{ $order->shipping_tel ? $order->shipping_tel : '-' }}</td></tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-title fa-eur" aria-hidden="true"></i></i><strong>Payment</strong></div>
                <div class="panel-body">
                  <div class="alert alert-success" role="alert" style="line-height:24px;">
                        Parcel Created Successfully! <br/>
                        Parcel Type &nbsp;&nbsp;<i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;&nbsp; {{ $order->type == 0 ? 'BC Parcel' : 'Personal Mail' }}<br/>
                        Shippment No. &nbsp;&nbsp;<i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;&nbsp; {{ $order->no }}<br/>
                  </div>
                  <table class="table table-striped">
                    <thead><tr><th>Price</th> <th></th> </tr> </thead>
                    <tbody>
                      <tr><td>Sub-Total :</td><td class="text-right">€ 42.5</td></tr>
                      <tr><td>Tax :</td><td class="text-right">€ 11.9</td></tr>
                      <tr><td>Discount :</td><td class="text-right">€ 0.0</td></tr>
                      <tr><td><strong>Total :</strong></td><td class="text-right"><strong>€ 54.4</strong></td></tr>
                    </tbody>
                  </table>
                  <button type="submit" class="btn btn-success">
                    &nbsp;Pay Now&nbsp;&nbsp;<i class="fa fa-arrow-circle-right" aria-hidden="true"></i>&nbsp;
                  </button>
                  <button type="submit" class="btn btn-primary">
                    &nbsp;All Parcels
                  </button>
              </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-7 col-md-offset-1">
          <div class="panel panel-default">
            <div class="panel-heading"><i class="fa fa-title fa-address-book-o" aria-hidden="true"></i></i><strong>Products Info</strong></div>
            <div class="panel-body">
              <table class="table table-striped">
                <thead><tr>
                  <th>Category</th>
                  <th>Name</th>
                  <th>Model</th>
                  <th>Number</th>
                  <th>Declared Value</th>
                </tr></thead>
                <tbody>
                  @foreach ($ops as $op)
                  <tr>
                    <td>{{ $op->category->name }}</td>
                    <td>{{ $op->name }}</td>
                    <td>{{ $op->model }}</td>
                    <td>{{ $op->number }}</td>
                    <td>{{ $op->price }}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
    </div>

  </form>
</div>
@endsection
