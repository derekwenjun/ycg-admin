<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::orderBy('id', 'desc');
		// if ($request->has('no')) $orders = $orders->where('no', 'like', '%' . $request->no . '%');
        // if ($request->has('no')) $query = $query->where('no', $request->no);
		// if ($request->has('nickname')) $query = $query->where('users.nickname', $request->input('name'));
        // if ($request->has('app')) $query = $query->where('orders.app', $request->input('app'));
		// if ($request->has('price')) $query = $query->where('orders.price', $request->input('price'));
		// if ($request->has('status')) $query = $query->where('orders.status', $request->input('status'));

		// $request->flash();
		// $orders = $query->where('orders.price', '<>', '0.0') -> orderBy('orders.id', 'desc') -> paginate(20);
		// $orders->appends($request->all());

        $users = $users->get();

        // request flash to access the old value
        $request->flash();

        return view('users.index', ['nav' => 'user', 'users' => $users ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function show(Request $request, $id)
    {
        $user = User::find($id);
        return view('users.show', ['nav' => 'user', 'user' => $user]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(Request $request, $id)
    {
        $user = User::find($id);
        return view('users.edit', ['nav' => 'user', 'user' => $user]);
    }
}
