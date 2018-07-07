<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Order;
use App\Http\Requests;

use App\Address, App\City, App\OrderProduct, App\Tracking;

use Auth;
use Log;

use GuzzleHttp\Client;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $orders = Order::orderBy('id', 'desc');
		if ($request->has('no')) $orders = $orders->where('no', 'like', '%' . $request->no . '%');
        if ($request->has('status_id')) $orders = $orders->where('status_id', $request->input('status_id'));

        $orders = $orders->where('status_id', '>=', '2');
        $orders = $orders->orderBy('id', 'desc');

        // request flash to access the old value
        $request->flash();

        $orders = $orders->paginate(20);
        $orders->appends($request->all());

        return view('orders.index', ['nav' => 'order', 'orders' => $orders ]);
    }

    /**
     * 揽收包裹进入总仓
     * 如果订单状态尚未进入总仓，则直接更改，否则不做变动
     * @param  int  $id
     * @return Response
     */
    public function pickup(Request $request)
    {
        if ($request->has('no')) {
            // 根据运单号查询包裹
            $order = Order::where('no', $request->no)->first();
            if(is_null($order)) return view('orders.pickup', ['nav' => 'order']);
            
            // 更新状态为 - 总仓已入库
            if($order->status_id == 2) {
                

                // TODO: 向 NIM服务器注册用户
                $client = new Client(['base_uri' => 'http://api.zhenrh.com/']);

                $jsonStr = json_encode(
                    [
                        'OrderCode' => '2018999999999',
                        'Sender' => 'aaaa',
                        'SendAddress' => 'bbbb',
                        'SendPhone' => '12121212121',
                        'Receiver' => 'cccccc',
                        'ReceiveCardNo' => '330902198407031015',
                        'ReceivePhone' => '121212121',
                        'ReceiveAddress' => 'ddddddddd',
                        'ReceiveProvince' => '浙江省',
                        'ReceiveCity' => '杭州市',
                        'ShopId' => 9997,
                        'TotalPrice' => 0.0,
                        'CountryCode' => 'ES',
                        'ExpressId' => 34,
                        'BagWeight' => 0.0,
                        'BagCount' => 0,
                        'IsTax' => false,
                        'Items' => []
                    ]
                );
                Log::info('Request JSON ===> ' . $jsonStr);
                $stream = \GuzzleHttp\Psr7\stream_for($jsonStr);
                $response = $client->request('POST', 'api/order/PostOrder', [
                    'auth' => ['ozkj001:123456:9a2e05ddd44fbd5bd6d8ddd43f84c50e', ''],
                    'body' => $stream
                ]);

                //$obj = json_decode($response->getBody());
                Log::error($response->getBody());
                $code = $obj -> code;
                if($code != '200') {
                    Log::error('#### 向NIM服务器用户创建失败');
                    return $this->response->errorBadRequest('向聊天服务器申请账号失败!');
                }

                $order->status_id = 3;
                $order->save();

                // 生成第一条追踪信息
                $tracking = new Tracking;
                $tracking->order_id = $order->id;
                $tracking->location = '西班牙';
                $tracking->description = '总仓已入库';
                $tracking->save();
            }
            
            $request->session()->flash('status', '包裹 - ' . $order->no . ' 已在总仓入库！');
            return view('orders.pickup', ['nav' => 'order', 'order' => $order]);
        }
        return view('orders.pickup', ['nav' => 'order']);
    }

    public function getHeader(){
        // 构建checksum
        $curtime = time();
        $nonce = rand();
        $checksum = strtolower(sha1(config('nim.nim_app_secret') . $nonce . $curtime));
        // 构建header
        $header = [
            'AppKey' => config('nim.nim_app_key'),
            'CurTime' => $curtime,
            'CheckSum' => $checksum,
            'Nonce' => $nonce,
            'Content-Type' => 'application/x-www-form-urlencoded;charset=utf-8',
        ];
        return $header;
    }

    /**
     * 显示订单成功页面
     * @param  int  $id
     * @return Response
     */
    public function tracking(Request $request, $id)
    {
        $order = Order::find($id);
        return view('orders.tracking', ['nav' => 'order', 'order' => $order]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
    
    /**
     * 显示订单详情
     * @param  int  $id
     * @return Response
     */
    public function show(Request $request, $id)
    {
        $order = Order::find($id);
        return view('orders.show', ['nav' => 'order', 'order' => $order]);
    }
}
