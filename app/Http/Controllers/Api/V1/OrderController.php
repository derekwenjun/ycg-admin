<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\Order;
use Dingo\Api\Routing\Helpers;

use GuzzleHttp\Client;

use JWTAuth;
use JWTFactory;

use DB;
use Log;

class OrderController extends Controller
{
    use Helpers;

    /**
     * 获取验证用户
     */
    public function getAuthUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        }
        return $user;
    }

    /**
     * 获取随机四个打招呼信息
     * @return \Illuminate\Http\Response
     */
    public function types(Request $request)
    {
        $app = $request->json('app');

        $types = DB::table('order_types')
                        ->where('app', $app)
                        ->orderBy('id', 'asc')
                        ->get();
        // 获取支付设置
        $pay = DB::table('settings')->where('app', $app)->value('pay');
        // 
        return $this->response->array(['pay' => $pay, 'types' => $types]);
    }

    /**
     * 发送消息，并且记数
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // parse user from token
        $user = $this->getAuthUser();
        $app = $request->json('app');
        $tid = $request->json('tid');

        $type = DB::table('order_types')
                        ->where('id', $tid)
                        ->first();
        
        $order = new Order();
        $order->user_id = $user->id;
        $order->app = $app;
        $order->price = $type->price;
        $order->period = $type->period;
        $order->title = $type->title;
        $order->sub_title = $type->sub_title;

        // 生成随机订单号
        $orderNO = $app . date("Ymd") . rand(1000000, 9999999);
        $order->no = $orderNO;

        $order->save();

        return $this->response->array(['no' => $orderNO]);
    }
}


