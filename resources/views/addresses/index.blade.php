@extends('layouts.app')

@section('content')

<div class="modal fade" tabindex="-1" role="dialog" id="myModal" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Create New Address</h4>
      </div>

      <form id="addressForm" action="{{ Route('address.store') }}" method="POST">
      {{ csrf_field() }}

      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="name"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
              </div>
          </div>
          <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="mobile"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;Mobile</label>
                <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Mobile" required>
              </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="country"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;Country</label>
                <input type="text" class="form-control" id="country" value="Netherlands" disabled>
                <input type="hidden" name="country" value="Netherlands">
              </div>
          </div>
          <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="city"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;City</label>
                <select class="form-control" id="city" name="city">
                  @foreach ($cities as $city)
                  <option value="{{ $city->name }}">{{ $city->name }}</option>
                  @endforeach
                </select>
              </div>
          </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="address"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;Address</label>
            <input type="text" class="form-control" id="address" name="address" placeholder="Address" required>
        </div>
        <div class="row">
          <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="zip"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;ZIP Code</label>
                <input type="text" class="form-control" id="zip" name="zip" placeholder="ZIP Code" required>
              </div>
          </div>
          <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="tel">Tel</label>
                <input type="text" class="form-control" id="tel" name="tel" placeholder="Tel">
              </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="mailbox">Mailbox</label>
                <input type="text" class="form-control" id="mailbox" name="mailbox" placeholder="Mailbox">
              </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save</button>
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

@foreach ($addresses as $address)

<div class="modal" tabindex="-1" role="dialog" id="modelAddress{{ $address->id }}" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Edit Shipper Address</h4>
      </div>

      <form id="addressForm" action="{{ route('address.update', ['id'=>$address->id]) }}" method="POST">
      {{ csrf_field() }}
      <input type="hidden" name="_method" value="PUT">

      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="name"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="发货人" value="{{ $address->name }}" required>
              </div>
          </div>
          <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="mobile"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;Mobile</label>
                <input type="text" class="form-control" id="mobile" name="mobile" placeholder="手机" value="{{ $address->mobile }}" required>
              </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="country"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;Country</label>
                <input type="text" class="form-control" id="country" value="Netherlands" disabled>
                <input type="hidden" name="country" value="Netherlands">
              </div>
          </div>
          <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="city"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;City</label>
                <select class="form-control" id="city" name="city" value="{{ $address->city }}">
                  @foreach ($cities as $city)
                  <option value="{{ $city->name }}">{{ $city->name }}</option>
                  @endforeach
                </select>
              </div>
          </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="address"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;Address</label>
            <input type="text" class="form-control" id="address" name="address" placeholder="Address" value="{{ $address->address }}" required>
        </div>
        <div class="row">
          <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="zip"><i class="fa fa-asterisk text-danger" aria-hidden="true"></i>&nbsp;ZIP Code</label>
                <input type="text" class="form-control" id="zip" name="zip" placeholder="ZIP Code" value="{{ $address->zip }}" required>
              </div>
          </div>
          <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="tel">Tel</label>
                <input type="text" class="form-control" id="tel" name="tel" value="{{ $address->tel }}" placeholder="Tel">
              </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="mailbox">Mailbox</label>
                <input type="text" class="form-control" id="mailbox" name="mailbox" value="{{ $address->mailbox }}" placeholder="Mailbox">
              </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button data-toggle="modal" data-target=".deleteConfirm" onclick="confirmDelete('{{Route('address.destroy', ['id'=>$address->id])}}')" type="submit" class="btn btn-danger pull-left">Delete Address</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
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

@endforeach


<div class="container">

@if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    <ol class="breadcrumb">
      <li><a href="/">Home</a></li>
      <li class="active">Addresses</li>
        <!-- Button trigger modal -->
        <span class="pull-right">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal">
                <i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Create New Address
            </button>
        </span>
    </ol>



    <form action="{{Route('order.index')}}" method="GET">
        <table class="table table-hover table-striped">
            <thead>
            <tr>
                <td>#</td>
                <td>Name</td>
                <td>Mobile</td>
                <td>Country</td>
                <td>City</td>
                <td>Address</td>
                <td>ZIP Code</td>
                <td>Tel</td>
                <td>Operation</td>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td><input type="text" class="form-control input-sm" name="name" value="{{ old('name') }}"/></td>
                    <td><input type="text" class="form-control input-sm" name="mobile" value="{{ old('mobile') }}"/></td>
                    <td></td>
                    <td><input type="text" class="form-control input-sm" name="city" value="{{ old('city') }}"/></td>
                    <td><input type="text" class="form-control input-sm" name="address" value="{{ old('address') }}"/></td>
                    <td><input type="text" class="form-control input-sm" name="zip" value="{{ old('zip') }}"/></td>
                    <td><input type="text" class="form-control input-sm" name="tel" value="{{ old('tel') }}"/></td>
                    <td>
                        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    </td>
                </tr>
            @foreach ($addresses as $address)
                <tr class="{{ $address->is_default == 1 ? 'success' : '' }}">
                    <td>{{ $address->id }}</td>
                    <td>{{ $address->name }}</td>
                    <td>{{ $address->mobile }}</td>
                    <td>{{ $address->country }}</td>
                    <td>{{ $address->city }}</td>
                    <td>{{ $address->address }}</td>
                    <td>{{ $address->zip }}</td>
                    <td>{{ $address->tel }}</td>
                    <td>
                        @if ($address->is_default == 1)
                            <span class="text-success">Default</span> | 
                        @else
                            <a href="#" data-toggle="modal" data-target="#">Set Default</a> | 
                        @endif
                        <a href="#" data-toggle="modal" data-target="#modelAddress{{ $address->id }}">Edit</a>
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
