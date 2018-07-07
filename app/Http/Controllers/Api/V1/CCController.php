<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use Dingo\Api\Routing\Helpers;

use App\Jobs\SendReplyMessage;
use App\Jobs\SendAudioMessage;

use JWTAuth;
use JWTFactory;


use DB;
use Log;
use Redis;

use Carbon\Carbon;


define("TEAM_LOCATION_REDIS_KEY", "loct");
define("NEAR_TEAM_LIST_REDIS_KEY", "tlst:");

class CCController extends Controller
{

    use Helpers;

    /**
     * 请求验证码
     * @return \Illuminate\Http\Response
     */
    public function cc(Request $request)
    {
        Log::info("");
        
        $ContentType = $request->header('Content-Type');
        $AppKey = $request->header('AppKey');
        $CurTime = $request->header('CurTime');
        $MD5 = $request->header('MD5');
        $CheckSum = $request->header('CheckSum');

        $requestBody = $request->getContent();
        $verifyMD5 = md5($requestBody);
        $verifyCheckSum = strtolower(sha1(config('nim.nim_app_secret') . $verifyMD5 . $CurTime));

        /*
        Log::info("ContentType : " . $ContentType);
        Log::info("AppKey : " . $AppKey);
        Log::info("CurTime : " . $CurTime);
        Log::info("MD5 FROM : " . $MD5);
        Log::info("MD5 GETD : " . $verifyMD5);
        
        
        Log::info('CheckSum Verification Started : ', [
            'ServerCheckSum' => $CheckSum, 
            'VerifyCheckSum' => $verifyCheckSum, 
        ]);
        */

        // 检查是否真实请求
        if($CheckSum != $verifyCheckSum) {
            Log::info("========>> CheckSum Verification FFFFFFFFFFailed！");
            return;
        }

        $eventType      = $request->json('eventType');
        $convType       = $request->json('convType');
        $attach_json    = $request->json('attach');

        //$attach         = json_decode($attach_json);
        //$attach_id      = intval($attach->id);
        Log::info("========>> 收到网易服务器CC消息, 消息类型(eventType) : " . $eventType);
        //Log::info("attach : " . var_export($attach));
        //Log::info("attach.id : " . $attach_id);

        switch ($eventType) {
            case '1':               // 聊天消息，群消息
                ////// ;
                switch ($convType) {
                    case 'TEAM':
                        switch($msgType) {
                            case 'NOTIFICATION':
                                if($attach_id == 4) {
                                    Log::info('收到群解散消息');
                                    $this->dismissTeam($to);
                                }
                            break;

                        }
                    break;

                    case 'CUSTOM_TEAM':
                    break;

                    case 'PERSON':
                        $toAccount      = $request->json('to');
                        $fromAccount    = $request->json('fromAccount');
                        $msgType        = $request->json('msgType');
                        $msg = $request->json('body');

                        $current = Carbon::now();
                        $today00 = Carbon::today();
                        $today06 = $today00->copy()->addHours(7);
                        $today21 = $today00->copy()->addHours(19);

                        //return;
                        //if( $current->gt($today06) && $current->lt($today21) ) return;     // safe call
                        if( $toAccount == '85' || $toAccount == '34' ) return;

                        // 向非888用户发送信息，直接返回
                        $to_user = User::findOrFail($toAccount);
                        if(substr($to_user->mobile, 0, 3) != '888') return;


                        Log::info("convType : " . $convType);
                        Log::info('MsgFromAccount : ' . $fromAccount);
                        Log::info('MsgToAccount   : ' . $toAccount);
                        Log::info('MsgType        : ' . $msgType);
                        Log::info('MsgContent     : ' . $msg);

                        // 1. 检查是否VIP，如果是，则返回
                        $from_user = User::findOrFail($fromAccount);

                        Log::info('From user : ', [
                            'id' => $from_user->id, 
                            'nickname' => $from_user->nickname,
                            'mobile' => $from_user->mobile
                        ]);

                        if(intval($from_user->role) == 2) {
                            Log::info('---->> Got message from a VIP, return!');
                            return;
                        }

                        if(strpos($msg, '漂流瓶消息】')) {

                            $seed = rand(0, 9);

                            $job = new SendReplyMessage();
                            $job -> from_user = $toAccount;
                            $job -> to_user = $fromAccount;
                            switch ($seed) {
                                case 0:
                                    $job -> content = '这都能捡到。。。缘分';
                                    break;
                                case 1:
                                    $job -> content = '咦，你捡了？聊聊？';
                                    break;
                                default:
                                    $job -> content = '哎呦～～';
                                    break;
                            }
                            $delay = rand(15, 25);
                            $this->dispatch($job -> delay($delay));
                        } else {

                            // 2. 判断招呼语句类别
                            $greetingIdx = $this->parseContent($msg);
                            Log::info('Got Msg index --> ' . $greetingIdx);

                            // 3. 进行人格计算：不理，消极，积极
                            $seed = rand(0, 9);
                            $personality = '';
                            if($seed == 0) {
                                $personality = 'IGNORE';    // 不理型人格，直接返回
                                log::info('---> Got IGNORE personality, return');
                                return;
                            } else if ($seed >= 1 && $seed <= 5) {
                                $personality = 'POS';       // 积极性人格
                            } else {
                                $personality = 'NEG';       // 消极性人格
                            }

                            // 5. 选取对应的 人格|招呼 语句
                            $replayIdx = rand(0, 2);
                            log::info('---> Got \"' . $personality . '\" personality, and replay index: '. $replayIdx);
                            $responseArr = DB::table('responses') 
                                            -> select('content')
                                            -> where('greeting_index', $greetingIdx)
                                            -> where('personality', $personality)
                                            -> where('index', $replayIdx)
                                            -> orderBy('order', 'asc')
                                            -> get();

                            // 6. 进行伪原创处理(TODO)

                            // 7. 每句replay生成一个queue job
                            $totalDelay = 0;
                            foreach ($responseArr as $obj) {
                                // dispatch the job
                                $job = new SendReplyMessage();
                                $job -> from_user = $toAccount;
                                $job -> to_user = $fromAccount;
                                $job -> content = $obj -> content;

                                // 第一次回复较慢
                                if($totalDelay == 0)
                                    $totalDelay += rand(15, 25);
                                else
                                    $totalDelay += rand(4, 10);
                                $this->dispatch($job -> delay($totalDelay));
                            }
                        }

                    break;
                }
            break;

            case "2": case "3":     // 登入、登出消息
                ////// ;
            break;

          default:
            break;
        }

        return $this->response->array(['status' => "200"]);
    }

        /*
    0. 你好，你平时喜欢做些什么事情呢？
    1. 很高兴认识你，可以交换下微信吗？
    2. 你好！很高兴在这里遇到你~
    3. 好呀，认识很难得哦，加个微信吗？
    4. 好！看了你的照片很心动，可以认识吗？
    5. 你好，请问你喜欢什么类型的异性朋友呢？
    6. Hi，好呀，平时都听什么类型的音乐呢？
    7. 你好，很喜欢你的头像，可以加下你的微信吗？
    8. 好！可以认识下吗，我很喜欢你的头像！
    9. Hi，你是哪里人呢？
    10. 很高兴认识你，可以互换下qq吗？
    */
    private function parseContent($content) {
        if(strpos($content, '做些什么事情')) return 0;
        if(strpos($content, '交换下微信吗')) return 1;
        if(strpos($content, '在这里遇到你')) return 2;
        if(strpos($content, '加个微信吗')) return 3;
        if(strpos($content, '照片很心动')) return 4;
        if(strpos($content, '什么类型的异性')) return 5;
        if(strpos($content, '什么类型的音乐')) return 6;
        if(strpos($content, '加下你的微信')) return 7;
        if(strpos($content, '喜欢你的头像')) return 8;
        if(strpos($content, '你是哪里人呢')) return 9;
        if(strpos($content, '互换下qq吗')) return 10;
    }

    public function dismissTeam($tid)
    {
        // 首先查找服务器中是否有这个群，没有则直接异常返回
        $team = Team::where('tid', $tid)->firstOrFail();

        // 1. 更新数据库中群的状态为：1(已解散)
        $team->status = 1;
        $team->save();

        // 2. 从redis中删除该群的坐标位置
        Redis::zrem(TEAM_LOCATION_REDIS_KEY, $team->id);
    }


}
