@extends('layouts.app')

@section('content')
<div class="container">

    <ol class="breadcrumb">
      <li><a href="/">Home</a></li>
      <li class="active">Order Tracking</li>
    </ol>

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

    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-title fa-random" aria-hidden="true"></i><strong>Tracking Events</strong></div>
                <div class="panel-body">
                <div class="row">
                <div class="col-md-10">
                <table class="table table-striped">
                    <thead><tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Location</th>
                        <th>Status</th>
                    </tr></thead>
                    <tbody>
                        <tr>
                            <th scope="row">6</th>
                            <td>Wed Jun 15</td>
                            <td>21:47</td>
                            <td>CN</td>
                            <td>Shipment delivered</td>
                        </tr>    
                        <tr>
                            <th scope="row">5</th>
                            <td>Sun Jun 14</td>
                            <td>17:00</td>
                            <td>CN</td>
                            <td>Received in country of destination</td>
                        </tr>                           
                        <tr>
                            <th scope="row">4</th>
                            <td>Sun Jun 12</td>
                            <td>17:00</td>
                            <td>CN</td>
                            <td>Your shipment has arrived in the country of destination, awaiting customs clearance</td>
                        </tr>                        
                        <tr>
                            <th scope="row">3</th>
                            <td>Fri Jun 10</td>
                            <td>17:00</td>
                            <td>NL</td>
                            <td>Sent to country of destination</td>
                        </tr>
                        <tr>
                            <th scope="row">2</th>
                            <td>Fri Jun 10</td>
                            <td>05:12</td>
                            <td>NL</td>
                            <td>Parcel received and in sorting process</td>
                        </tr>
                        <tr>
                            <th scope="row">1</th>
                            <td>Wed Jun 8</td>
                            <td>23:17</td>
                            <td>NL</td>
                            <td>Shipment is expected, but not yet in sorting process</td>
                        </tr>
                    </tbody>
                </table>
                </div></div>
                </div>
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
