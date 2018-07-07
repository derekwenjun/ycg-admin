<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Events\VIPPurchased;

use App\User;
use App\Order;
use Dingo\Api\Routing\Helpers;

use GuzzleHttp\Client;

use JWTAuth;
use JWTFactory;

use DB;
use Event;
use Log;
use Exception;

class IAPController extends Controller
{
    use Helpers;

    public function notify(Request $request) 
    {
        $sandboxEndpoint = "https://sandbox.itunes.apple.com/verifyReceipt";
        $endpoint = "https://buy.itunes.apple.com/verifyReceipt";

        // 验证请求。
        Log::info('IAP notify get data verification success.', [
            'user' => $request->input('userId'),
            'receipt' => $request->input('appStoreReceipt')
        ]);

        $userId = $request->input('userId');
        $receiptObject['receipt-data'] = $request->input('appStoreReceipt');

        $decoded = $this->verify($endpoint, $receiptObject);
        if ($decoded === FALSE) $decoded = $this->verify($sandboxEndpoint, $receiptObject);
        if ($decoded === FALSE) { return "failure"; }

        $orderNO = $decoded['receipt']['transaction_id'];
        $sku_id = $decoded['receipt']['product_id'];

        $type = DB::table('order_types')
                        ->where('sku', $sku_id)
                        ->first();

        try {
            $order = new Order();
            $order->user_id = $userId;
            $order->no = $orderNO;
            $order->price = $type->price;
            $order->period = $type->period;
            $order->title = '[In-App]' . $type->title;
            $order->sub_title = $type->sub_title;
            $order->app = $type->app;

            $order->save();
        } catch(Exception $e) {
            Log::debug('IAP notify duplicated transaction id.', [
                'order no' => $orderNO
            ]);
            Log::debug($e);
            return "failure";
        }

        Event::fire(new VIPPurchased($orderNO));
        Log::info('iOS purchased notify success.', [
            'order no' => $orderNO
        ]);

        return "success";
    }

    private function verify($endpoint, $receiptObject) 
    {
        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded",
                'method'  => 'POST',
                'content' => json_encode($receiptObject)
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($endpoint, FALSE, $context);
        Log::info('IAP notify apple server result.', [
            'result' => $result
        ]);

        if ($result === FALSE) {
            return FALSE;
        }
        $decoded = json_decode($result, TRUE);
        if (!isset($decoded['status'])) { return FALSE; }
        else if ($decoded['status'] != 0) { return FALSE; }

        return $decoded;
    }
}


