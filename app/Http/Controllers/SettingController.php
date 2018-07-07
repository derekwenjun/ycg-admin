<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

use App\City;

use DB;

class SettingController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $cities = City::get();
        $client_rate = DB::table('rates')->where('name', 'client_rate')->value('value');
        $user_rate = DB::table('rates')->where('name', 'user_rate')->value('value');
        
        return view('settings.index', ['nav' => 'setting', 
            'cities' => $cities, 
            'client_rate' => $client_rate,
            'user_rate' => $user_rate
        ]);
    }
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }
}
