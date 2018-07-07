<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Client, App\City;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::orderBy('id', 'desc');
		// if ($request->has('no')) $orders = $orders->where('no', 'like', '%' . $request->no . '%');
        // if ($request->has('no')) $query = $query->where('no', $request->no);
		// if ($request->has('nickname')) $query = $query->where('users.nickname', $request->input('name'));
        // if ($request->has('app')) $query = $query->where('orders.app', $request->input('app'));
		// if ($request->has('price')) $query = $query->where('orders.price', $request->input('price'));
		// if ($request->has('status')) $query = $query->where('orders.status', $request->input('status'));

		// $request->flash();
		// $orders = $query->where('orders.price', '<>', '0.0') -> orderBy('orders.id', 'desc') -> paginate(20);
		// $orders->appends($request->all());

        $clients = $clients->get();

        // request flash to access the old value
        $request->flash();

        return view('clients.index', ['nav' => 'client', 'clients' => $clients ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function show(Request $request, $id)
    {
        $client = Client::find($id);
        $addresses = $client->addresses;
        $shippingAddresses = $client->shippingAddresses;
        $cities = City::get();
        return view('clients.show', ['nav' => 'client', 
                                        'client' => $client, 
                                        'addresses' => $addresses, 
                                        'shippingAddresses' => $shippingAddresses, 
                                        'cities' => $cities]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function charge(Request $request, $id)
    {
        $client = User::find($id);
        return view('clients.charge', ['nav' => 'client', 'client' => $client]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(Request $request, $id)
    {
        $client = User::find($id);
        return view('clients.edit', ['nav' => 'client', 'client' => $client]);
    }
}
