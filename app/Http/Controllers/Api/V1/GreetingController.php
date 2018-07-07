<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\Selfie;
use Dingo\Api\Routing\Helpers;

use GuzzleHttp\Client;

use JWTAuth;
use JWTFactory;

use DB;
use Log;
use Redis;

class GreetingController extends Controller
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
    public function rnd(Request $request)
    {
        // parse user from token
        $user = $this->getAuthUser();

        $count = DB::table('greeting_sents')
        			->whereRaw('id = ' . $user->id . ' AND DATEDIFF(CURRENT_DATE, updated_at) = 0')
        			->value('count');
        $count = intval($count);

        if($count < 3 || $user->id == 84) {
	        $greetings = DB::table('greetings')->select('content')->get();
	        shuffle($greetings);
	        return $this->response->array(array_slice($greetings, 0, 4));
        } else {
        	return $this->response->errorBadRequest('今日打招呼次数已用完！');
        }
    }

    /**
     * 发送消息，并且记数
     * @return \Illuminate\Http\Response
     */
    public function sent(Request $request)
    {
        // parse user from token
        $user = $this->getAuthUser();
        $to = $request->json('to');
        
        // 检查今天是否已经给此人打过招呼了
        if (DB::table('greeting_log')
            ->where('from', $user->id)
            ->where('to', $to)
            ->whereRaw('DATEDIFF(CURRENT_DATE, `created_at`) = 0')
            ->count() > 0) {

            // 返回错误
            return $this->response->errorBadRequest('您今日已经向TA打过招呼了!');
        } else {
            DB::table('greeting_log')->insert(
                ['from' => $user->id, 'to' => $to]
            );
        }

        if (DB::table('greeting_sents')->where('id', $user->id)->count() == 0) {
			DB::table('greeting_sents')->insert(['id' => $user->id, 'count' => 1]);
        } else {
        	DB::update(DB::raw('UPDATE `greeting_sents` SET `count` = CASE ' .
        		'WHEN DATEDIFF(CURRENT_DATE , `updated_at`) = 0 THEN (`count` + 1) ELSE 1 END, updated_at = CURRENT_TIMESTAMP WHERE `id` = ' . $user->id));
        }
        
        return $this->response->array(['message' => 'OK']);
    }
}


