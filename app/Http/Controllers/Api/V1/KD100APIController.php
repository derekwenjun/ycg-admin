<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Dingo\Api\Routing\Helpers;

use GuzzleHttp\Client;

use DB;
use Event;
use Log;

class KD100APIController extends Controller
{
    use Helpers;

    /**
     * 获取随机四个打招呼信息
     * @return \Illuminate\Http\Response
     */
    public function tracking(Request $request)
    {
        Log::info('====>> 收到快递100订单追踪信息查询请求，订单号 - ' . $request->nu);
        // get tracking info
        $trackings = DB::table('trackings')
            ->join('orders', 'trackings.order_id', '=', 'orders.id')
            ->select(DB::raw('trackings.created_at as time, concat(trackings.location, " ", trackings.description) as context'))
            ->where('orders.no', '=', $request->nu)
            ->get();
        Log::info("====>> 查询成功！");
        return $this->response->array($trackings);
    }
}


