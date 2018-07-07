<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Address;
use App\City;

use Auth;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $addresses = Address::where('uid', $user->id) -> get();
        $cities = City::get();
        return view('addresses.index', ['nav' => 'address', 'addresses' => $addresses, 'cities' => $cities ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'name' => 'required',
            'mobile' => 'required',
        ]);

        $user = Auth::user();

        $address = new Address;
        $address->uid = $user->id;
        $address->name = $request->name;
        $address->mobile = $request->mobile;
        $address->country = $request->country;
        $address->city = $request->city;
        $address->address = $request->address;
        $address->zip = $request->zip;
        $address->mailbox = $request->mailbox;
        $address->tel = $request->tel;
        $address->save();

        return redirect()->route('address.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $address = Address::find($id);
        $address->name = $request->name;
        $address->mobile = $request->mobile;
        $address->country = $request->country;
        $address->city = $request->city;
        $address->address = $request->address;
        $address->zip = $request->zip;
        $address->mailbox = $request->mailbox;
        $address->tel = $request->tel;
        $address->save();

        return redirect()->route('address.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
