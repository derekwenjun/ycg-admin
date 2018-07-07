<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\Selfie;
use Dingo\Api\Routing\Helpers;

use App\Jobs\SendReplyMessage;
use App\Jobs\SendAudioMessage;

use GuzzleHttp\Client;
use Carbon\Carbon;

use App\Events\UserInited;

use JWTAuth;
use JWTFactory;

use DB;
use Log;
use Event;
use Redis;

define("REDIS_KEY_LOCATION_MALE", "loc:male");
define("REDIS_KEY_LOCATION_FEMALE", "loc:female");
define("REDIS_KEY_BRANCH_PREFIX", "bch:");
//define("REDIS_KEY_LOCATION_PREFIX", "loc:");

define("PAGE_SIZE", 10);

class UserController extends Controller
{

    use Helpers;

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
     * 请求验证码
     * @return \Illuminate\Http\Response
     */
    public function pin(Request $request)
    {
        $mobile = $request->json('mobile');
        $app = $request->json('app');
        Log::info('========>> 用户请求PIN - ' . $mobile . ' - ' . $app);

        // 检查是否注册新的机器人
        $robot = $this->isRobot($mobile);

        // 检查手机号是否有误
        if (!preg_match('/^1[0-9]{10}$/', $mobile) && !$robot) {
            Log::error('#### 收到错误的手机号');
            $this->response->errorBadRequest('输入的手机号有误！');
        }

        // 检查用户是否已经存在
        if (User::where('mobile', $mobile)->where('app', $app)->count() == 0) {
            $user = new User();
            $user->mobile = $mobile;
            $user->app    = $app;
            $user->save();
            
            Log::info('---->> 创建新用户 Id - ' . $user->id . ' App - ' . $app);

            // TODO: 向 NIM服务器注册用户
            $client = new Client(['base_uri' => config('nim.nim_url'), 'headers' => $this->getHeader()]);
            $response = $client->request('POST', 'user/create.action', [
                'form_params' => [
                    'accid' => $user->id,
                    'token' => $user->id
                ]
            ]);
            $obj = json_decode($response->getBody());
            $code = $obj -> code;
            if($code != '200') {
                Log::error('#### 向NIM服务器用户创建失败');
                return $this->response->errorBadRequest('向聊天服务器申请账号失败!');
            }
        } else {
            $user = User::where('mobile', $mobile)->where('app', $app)->first();
            ///// HACK 如果是机器人，则再向服务器注册一次
            if($robot) {
                Log::info('---->> 临时措施，每次都向NIM服务器重新注册接待员');
                $client = new Client(['base_uri' => config('nim.nim_url'), 'headers' => $this->getHeader()]);
                $client->request('POST', 'user/create.action', [
                    'form_params' => [
                        'accid' => $user->id,
                        'token' => $user->id
                    ]
                ]);
            }
        }

        // 机器人登录无需发送pin码，默认使用8888，直接返回
        if($robot) {
            Log::info('---->> 接待员无须发送真实PIN');
            return $this->response->array(['uid'=>$user->id]);
        }

        // 一分钟内多次发送验证码，直接返回错误
        $lastPin = Carbon::parse($user->last_pin);
        if(!is_null($user->last_pin) && !$lastPin->addSeconds(58)->isPast()) {
            $this->response->errorBadRequest('一分钟内不能多次请求验证码!');
        }
        $user->last_pin = Carbon::now();
        $user->save();


        //////////////////////////////////////////////////////////////////////
        // 向短信服务器请求发送pin码短信

        $templateid = '3054928';
        if($app == 'tcya') $templateid = '3060962';
        else if($app == 'ygbh') $templateid = '3124081';
        else if($app == 'fjjy') $templateid = '3054928';
        else if($app == 'qcyy') $templateid = '3131294';

        $client = new Client(['base_uri' => config('nim.sms_url'), 'headers' => $this->getHeader()]);
        $response = $client->request('POST', 'sendcode.action', [
            'form_params' => [
                'mobile' => $mobile,
                'templateid' => $templateid,
                'codeLen'  => 4
            ]
        ]);

        $obj = json_decode($response->getBody());
        $code = $obj -> code;
        Log::info('===>> Send SMS code to ' . $mobile . ' with response code: ' . $code);
        
        return $this->response->array(['uid'=>$user->id]);
    }

    /**
     * 验证pin码
     * @return \Illuminate\Http\Response
     */
    public function verifyPin(Request $request)
    {
        $mobile = $request->json('mobile');
        $app = $request->json('app');
        $pin = $request->json('pin');

        // 查找mobile对应的user
        $user = User::where('mobile', $mobile)->where('app', $app)->first();
        
        // 检查是机器人登录
        $robot = $this->isRobot($mobile);

        // 检查验证码
        $code = '';
        if($robot) {
            // 机器人直接匹配'8888'
            if($pin == '8888') $code = '200';
        } else {
            // 向短信服务器请求发送pin码短信
            $client = new Client(['base_uri' => config('nim.sms_url'), 'headers' => $this->getHeader()]);
            $response = $client->request('POST', 'verifycode.action', [
                'form_params' => [
                    'mobile' => $mobile,
                    'code' => $pin
                ]
            ]);

            $obj = json_decode($response->getBody());
            $code = $obj -> code;
            Log::info('====>> Verify SMS Code with response code: ' . $code);
        }

        //
        if($code == '200') {
            // 生成JWT Token
            $customClaims = ['sub' => $user->id, 'pin' => $pin];
            $payload = JWTFactory::make($customClaims);
            $token = JWTAuth::encode($payload);
            
            Log::info('====>>Generate Token: ' . $token->get());

            // PIN码验证通过，此时NIM用户一定已经存在，使用Token的前128位更新NIM服务器token
            $client = new Client(['base_uri' => config('nim.nim_url'), 'headers' => $this->getHeader()]);
            $response = $client->request('POST', 'user/update.action', [
                'form_params' => [
                    'accid' => $user->id,
                    'token' => substr($token->get(), 0, 128)
                ]
            ]);
            // 返回完整的token给客户端
            return $this->response->array(["token" => $token -> get()]);
        } else {
            $this->response->errorBadRequest('错误的验证码!');
            return;
        }
    }
    
    /**
     * 获取自己个人信息
     * @return \Illuminate\Http\Response
     */
    public function profile(Request $request)
    {
        // parse user from token
        $user = $this->getAuthUser();
        $selfies = Selfie::where('user_id', $user->id)->get();
        return $this->response->array(['user' => $user->toArray(), 'selfies' => $selfies]);
    }

    /**
     * 获取其他用户信息
     * @return \Illuminate\Http\Response
     */
    public function info(Request $request)
    {
        // parse user from token
        $uId = $request->json('uid');
        $user = User::find($uId);
        $from_user = $this->getAuthUser();

        // TODO: 加入是否喜欢的信息
        $user->like = DB::table('likes')
                        ->where('like_id', $from_user->id)
                        ->where('belike_id', $user->id)
                        ->count();
        
        $selfies = Selfie::where('user_id', $uId)->get();
        return $this->response->array(['user' => $user->toArray(), 'selfies' => $selfies]);
    }

    /**
     * 喜欢某人
     * @return \Illuminate\Http\Response
     */
    public function like(Request $request)
    {
        // parse user from token
        $uId = $request->json('uid');
        $liker = $this->getAuthUser();

        // 先增加喜欢数字
        $belike = User::find($uId);
        $belike->likes ++;
        $belike->save();

        // 插入喜欢关系表
        DB::table('likes')->insert(
            ['like_id' => $liker->id, 'belike_id' => $belike->id]
        );

        return $this->noContent();
    }

    /**
     * 初始化用户信息
     * @return \Illuminate\Http\Response
     */
    public function init(Request $request)
    {
        $nickname = $request->json('nickname');
        $gender = $request->json('gender');
        if(is_null($nickname) || $nickname == '') $nickname = '约爱新人';

        $icon = 'avatar/default_male.png';
        if($gender == 1) $icon = 'avatar/default_female.png';

        // 更新用户数据
        $user = $this->getAuthUser();
        $user->nickname = $nickname;
        $user->avatar = $icon;
        $user->gender = $gender;
        $user->save();

        $client = new Client(['base_uri' => config('nim.nim_url'), 'headers' => $this->getHeader()]);
        $response = $client->request('POST', 'user/updateUinfo.action', [
            'form_params' => [
                'accid' => $user->id,
                'name' => $nickname,
                'icon' => $icon
            ]
        ]);

        // 非接待员
        if(!$this->isRobot($user->mobile)) {
            // 发起事件
            Event::fire(new UserInited($user->id));

            // 4分钟后发送语音消息
            /*
            if($gender == 0) {
                $obj = new SendAudioMessage();
                $obj -> from_user = '11';
                $obj -> to_user = $user->id;
                $this->dispatch($obj -> delay(180));
                
                $job = new SendReplyMessage();
                $job -> from_user = '11';
                $job -> to_user = $user->id;
                $job -> content = '你在干嘛呢？';
                $this->dispatch($job -> delay(184));
            }
            */

        }
        return $this->noContent();
    }

    public function isRobot($mobile)
    {
        if(strpos($mobile, '888') === 0 
                                || $mobile == '18668110679' 
                                || $mobile == '13905800135' 
                                || $mobile == '13905802626') 
        {
            return true;
        }
        return false;
    }

    public function updateAttribute(Request $request)
    {
        $key = $request->json('key');
        $value = $request->json('value');

        $user = $this->getAuthUser();

        switch($key) {
            case 'nickname':
                $user->nickname = $value;
                $client = new Client(['base_uri' => config('nim.nim_url'), 'headers' => $this->getHeader()]);
                $response = $client->request('POST', 'user/updateUinfo.action', [
                    'form_params' => [
                        'accid' => $user->id,
                        'name' => $value
                    ]
                ]);
                break;
            case 'avatar':
                $user->avatar = $value;
                $client = new Client(['base_uri' => config('nim.nim_url'), 'headers' => $this->getHeader()]);
                $response = $client->request('POST', 'user/updateUinfo.action', [
                    'form_params' => [
                        'accid' => $user->id,
                        'icon' => $value
                    ]
                ]);
                break;
            case 'sign':
                $user->sign = $value;
                break;
            case 'birthday':
                $user->birthday = $value;
                break;
            case 'city':
                $user->city = $value;
                break;

            ////////////////////////////////
            case 'relationship':
                $user->relationship = $value;
                break;
            case 'sociality':
                $user->sociality = $value;
                break;
            
            ////////////////////////////////
            case 'height':
                $user->height = $value;
                break;
            case 'job':
                $user->job = $value;
                break;
            case 'salary':
                $user->salary = $value;
                break;
        }

        $user->save();
        return $this->response->noContent();
    }

    /**
     * 添加一张个人照片
     */
    public function addSelfie(Request $request)
    {
        $url = $request->json('url');
        $user = $this->getAuthUser();

        $selfie = new Selfie;
        $selfie->url = $url;
        $selfie->user_id = $user->id;
        $selfie->save();

        $selfies = Selfie::where('user_id', $user->id)->get();
        return $this->response->array(['selfies' => $selfies]);
    }

    /**
     * 删除一张个人照片
     */
    public function deleteSelfie(Request $request)
    {
        $user = $this->getAuthUser();
        $pid = $request->json('pid');
        
        $selfie = Selfie::find($pid);
        if($selfie->user_id == $user->id) {
            $selfie->delete();
        }

        $selfies = Selfie::where('user_id', $user->id)->get();
        return $this->response->array(['selfies' => $selfies]);
    }

    /**
     * 更新一张个人照片
     */
    public function updateSelfie(Request $request)
    {
        $user = $this->getAuthUser();
        $pid = $request->json('pid');
        $url = $request->json('url');

        $selfie = Selfie::find($pid);
        if($selfie->user_id == $user->id) {
            $selfie->url = $url;
            $selfie->save();
        }

        $selfies = Selfie::where('user_id', $user->id)->get();
        return $this->response->array(['selfies' => $selfies]);
    }

    /**
     * 获取附近的用户
     */
    public function hot(Request $request) 
    {
        // 经纬度
        Log::info('========>> 用户查找热门的异性');

        // 翻页参数
        $page = intval($request->json('page'));
        $user = $this->getAuthUser();

        if($user->gender == 0) {
            // Get hot users
            $result = User::whereBetween('id', [11, 6073])
                            ->whereNotNull('nickname')
                            ->where('nickname', '<>', '')
                            ->orderBy('likes', 'desc')
                            ->skip($page * PAGE_SIZE)->take(PAGE_SIZE)
                            ->get();
        } else {
            $result = User::whereBetween('id', '>', 6073)
                            ->whereNotNull('nickname')
                            ->where('nickname', '<>', '')
                            ->orderBy('likes', 'desc')
                            ->skip($page * PAGE_SIZE)->take(PAGE_SIZE)
                            ->get();
        }

        return $this->response()->array(['users' => $result]);
    }

    /**
     * 获取附近的用户
     */
    public function near(Request $request) 
    {
        // 经纬度
        $lon  = $request->json('lon');
        $lat  = $request->json('lat');
        Log::info('========>> 用户查找附近的异性，自身坐标 - ' . $lon . ' - ' . $lat);

        // 翻页参数
        $page = intval($request->json('page'));

        $user = $this->getAuthUser();
        // 将自身地理位置信息存储在male list中
        if($user->gender == 0) {
            Redis::geoadd(REDIS_KEY_LOCATION_MALE, $lon, $lat, $user->id);

            // 为这位男士创造一个美好的世界
            $exists = Redis::exists(REDIS_KEY_BRANCH_PREFIX . $user->id);
            if($exists == 0) {
                $this->createBranch($user->id, $lon, $lat);
            }

            // find near users
            $near_users = Redis::georadius(REDIS_KEY_BRANCH_PREFIX . $user->id, $lon, $lat, 200, 'km', 'ASC', 'WITHDIST');
            Log::info('---->> 男性用户，共找到 ' . sizeof($near_users) . ' 名接待员');
        } else {
            $near_users = Redis::georadius(REDIS_KEY_LOCATION_MALE, $lon, $lat, 100, 'km', 'ASC', 'WITHDIST');
            Log::info('---->> 女性用户，共找到 ' . sizeof($near_users) . ' 名附近异性');
        }

        // 提取ids，准备数据库查询
        $ids = array();
        for($i = 0; $i < count($near_users); $i++) {
            array_push($ids, $near_users[$i][0]);
        }

        // populate users
        $ids = array_slice($ids, $page * PAGE_SIZE, PAGE_SIZE);
        $users = User::whereIn('id', $ids)->get();

        $result = array();
        for($i = 0; $i < count($near_users); $i++) {
            foreach($users as $user) {
                if($near_users[$i][0] == $user->id) {
                    $user->distance = $near_users[$i][1];
                    array_push($result, $user);
                    break;
                }
            }
        }

        return $this->response()->array(['users' => $result]);
    }

    public function newUsers(Request $request) 
    {
        // 翻页参数
        $page = intval($request->json('page'));
        $user = $this->getAuthUser();

        // populate users
        //$ids = array_slice($ids, $page * PAGE_SIZE, PAGE_SIZE);
        $users = User::/*where('role', 2)->*/orderBy('id', 'desc')
               ->skip($page * PAGE_SIZE)->take(PAGE_SIZE)
               ->get();

        return $this->response()->array(['users' => $users]);
    }

    public function createBranch($userID, $lon, $lat)
    {
        Log::info('========>> 场景过期，为 ' . $userID . '重新创造场景');
        $hosts = Redis::georadius(REDIS_KEY_LOCATION_FEMALE, $lon, $lat, 2000, 'km', 'ASC');
        $count = sizeof($hosts);
        Log::info('---->> 2000km内共找到 ' . $count . ' 位接待员');

        $middleIdx = intval($count / 2.1);
        for ($i = 1; $i <= $count; $i ++) {
            if($i <= $middleIdx) {
                $lonAdd = bcdiv(0.06 * $i, $middleIdx, 6) + 0.0035;
                $latAdd = bcdiv(0.06 * $i, $middleIdx, 6) + 0.0035;
            } else {
                $lonAdd = 0.06 + bcdiv(0.47 * ($i - $middleIdx), $count, 6);
                $latAdd = 0.06 + bcdiv(0.47 * ($i - $middleIdx), $count, 6);
            }
            
            // 进行随机颤动
            $lonAdd *= rand(8, 11) / 10.0;
            $latAdd *= rand(8, 11) / 10.0;

            Redis::geoadd(REDIS_KEY_BRANCH_PREFIX . $userID, $lon + $lonAdd, $lat + $latAdd, $hosts[$i - 1]);
            //Log::info('Insert user with : ' . ($lon + $lonAdd) . ' - ' . ($lat + $latAdd));
        }
        // 设置过期时间
        Redis::expire(REDIS_KEY_BRANCH_PREFIX . $userID, 600);
    }

    public function report(Request $request)
    {
        return $this->response()->noContent();
    }

}
