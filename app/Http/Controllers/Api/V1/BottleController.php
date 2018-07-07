<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\Selfie;
use Dingo\Api\Routing\Helpers;

use GuzzleHttp\Client;

use App\Jobs\SendReplyMessage;

use JWTAuth;
use JWTFactory;

use DB;
use Log;
use Redis;

class BottleController extends Controller
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
     * 尝试丢一个瓶子
     */
    public function tryone(Request $request)
    {
        // parse user from token
        $user = $this->getAuthUser();

        $count = DB::table('bottle_throws')
                    ->whereRaw('user_id = ' . $user->id . ' AND DATEDIFF(CURRENT_DATE, updated_at) = 0')
                    ->value('count');
        $count = intval($count);

        if($count == 0) {
            return $this->response->array(['message' => 'OK']);
        } else {
            return $this->response->errorBadRequest('每日只能扔一个漂流瓶，捡一个吧！');
        }
    }

    
    /**
     * 丢一个瓶子
     */
    public function throwone(Request $request)
    {
        // parse user from token
        $user = $this->getAuthUser();
        $content = $request->json('content');

        // 记录用户的丢瓶子内容
        DB::table('bottle_log')->insert(['user_id' => $user->id, 'content' => $content]);
        // 记录当日丢瓶子数字
        DB::table('bottle_throws')->insert(['user_id' => $user->id, 'count' => 1]);

        return $this->response->array(['message' => 'OK']);
    }

    /**
     * 捡一个瓶子
     */
    public function pickone(Request $request)
    {
        $user = $this->getAuthUser();


        // 0. 查看今天已经捞起来的瓶子个数，超过1个则返回VIP提示
        $count = DB::table('bottle_picks')
                    ->whereRaw('user_id = ' . $user->id . ' AND DATEDIFF(CURRENT_DATE, updated_at) = 0')
                    ->value('count');
        $count = intval($count);

        if($count > 0 && $user->role != 2) {
            return $this->response->errorBadRequest('VIP');
        }

        // 1. 如果该人获取的瓶子已经超过系统瓶子能力，返回空
        if($user->bottle_index >= DB::table("bottles")->count()) {
            return $this->response->array(['message' => '什么都没捡到']);
        }

        // 2. 计算概率，33%的概率能捞起瓶子
        $seed = rand(0, 9);
        if($seed <= 3) {
            
            // 3. 概率通过，随机选择漂流瓶发出人，返回漂流瓶信息

            DB::table('bottle_picks')->insert(['user_id' => $user->id, 'count' => 1]);

            $senders = User::where([['id', '>=', '11'], ['id', '<=', '6000'],])
                            ->whereNotNull('nickname')
                            ->where('nickname', '<>', '')
                            ->get();
            $count = sizeof($senders);
            $idx = rand(0, $count - 1);
            $sender = $senders[$idx];

            // 计算使用哪条漂流瓶语句
            $bottle_index = $user->bottle_index;
            $content = DB::table('bottles')->where('id', $bottle_index + 1)->value('content');
            $user->bottle_index ++;
            $user->save();

            // 发送漂流瓶消息
            $job = new SendReplyMessage();
            $job -> from_user = $user->id;
            $job -> to_user = $sender->id;
            $job -> content = '【漂流瓶消息】我捡到了你的漂流瓶 : ' . $content;
            $this->dispatch($job);

            return $this->response->array(['content' => $content, 'sender' => $sender]);
        } else {
            return $this->response->array(['content' => '']);
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


