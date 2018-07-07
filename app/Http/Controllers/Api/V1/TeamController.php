<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Team;
use Dingo\Api\Routing\Helpers;

use GuzzleHttp\Client;

use JWTAuth;
use JWTFactory;

use DB;
use Log;
use Redis;

define("TEAM_LOCATION_REDIS_KEY", "loct");
define("NEAR_TEAM_LIST_REDIS_KEY", "tlst:");

define("PAGE_SIZE", 20);

class TeamController extends Controller
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
        //Log::info($header);
        return $header;
    }

    /**
     * 创建高级群
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // 解析请求参数
        $name = $request->json('name');
        $avatar = $request->json('avatar');
        $location = $request->json('location');
        $lon = $request->json('lon');
        $lat = $request->json('lat');

        // parse user from token
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        }

        // 向NIM服务器请求创建高级群
        $client = new Client(['base_uri' => config('nim.nim_url'), 'headers' => $this->getHeader()]);
        $response = $client->request('POST', 'team/create.action', [
            'form_params' => [
                'tname' => $name,
                'owner' => $user->mobile,
                'members'  => '["' . $user->mobile . '"]',
                'icon'  => $avatar,
                'joinmode'  => 0,
                'msg'   =>  ($name . '创建'),
            ]
        ]);
    
        // parse response code
        $obj = json_decode($response->getBody());
        $code = $obj -> code;

        if($code == '200') {

            // parse team id
            $tid = $obj->tid;
            Log::info('Create team status code ==> ' . $code . '  tid ==> ' . $tid);

            // 创建新群
            $team = new Team();
            $team->tid = $tid;
            $team->name = $name;
            $team->avatar = $avatar;
            $team->location = $location;
            $team->lon = $lon;
            $team->lat = $lat;
            $team->save();

            // 设置redis geo
            $values = Redis::geoadd(TEAM_LOCATION_REDIS_KEY, $lon, $lat, $team->id);

            return $this->response->array(["tid" => $tid]);
        } else {
            // 向NIM请求创建聊天室失败，解析desc
            $desc = $obj->desc;
            Log::info('Create team failed with status code ==> ' . $code . '  desc ==> ' . $desc);
            return $this->response()->errorInternal('创建聊天室失败 - ' . $desc);
        }
    }

    /**
     * 创建高级群
     * @return \Illuminate\Http\Response
     */
    public function dismiss(Request $request)
    {
        $tid = $request->json('tid');

        // parse user from token
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        }

        // 首先查找服务器中是否有这个群，没有则直接异常返回
        $team = Team::where('tid', $tid)->firstOrFail();

        // 向NIM服务器请求删除高级群
        $client = new Client(['base_uri' => config('nim.nim_url'), 'headers' => $this->getHeader()]);
        $response = $client->request('POST', 'team/remove.action', [
            'form_params' => [
                'tid' => $tid,
                'owner' => $user->mobile,
            ]
        ]);

        // parse response code
        $obj = json_decode($response->getBody());
        $code = $obj -> code;

        // 1. 更新数据库中群的状态为：1(已解散)
        // 2. 从redis中删除该群的坐标位置
        if($code == '200') {
            // 1
            $team->status = 1;
            $team->save();

            Redis::zrem(TEAM_LOCATION_REDIS_KEY, $team->id);
            return $this->response->array(["tid" => $tid]);
        } else {
            $desc = $obj->desc;
            Log::info('Create team failed with status code ==> ' . $code . '  desc ==> ' . $desc);
            return $this->response()->errorInternal('解散聊天室失败 - ' . $desc);
        }

    }

    /**
     * 请求附近的群列表
     * @return \Illuminate\Http\Response
     */
    public function near(Request $request) 
    {
        // 经纬度
        $lon  = $request->json('lon');
        $lat  = $request->json('lat');
        // 翻页参数
        $page = intval($request->json('page'));

        // parse user from token
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        }

        // get team ids
        $near_teams;
        if($page == 0) {
            // find near team list from redis
            $near_teams = Redis::georadius(TEAM_LOCATION_REDIS_KEY, $lon, $lat, 50, 'km', 'ASC');
            Log::info('Find Near Team -' . sizeof($near_teams));
            Redis::set(NEAR_TEAM_LIST_REDIS_KEY . $user->id, json_encode($near_teams));
        } else {
            $near_teams = json_decode(Redis::get(NEAR_TEAM_LIST_REDIS_KEY . $user->id));
        }

        // get sub team ids
        $ids = array_slice($near_teams, $page * PAGE_SIZE, PAGE_SIZE);
        $teams = DB::table('teams')
                    ->whereIn('id', $ids)
                    ->get();


        return $this->response()->array($teams);
    }

    // 查找附近的命令
    //GEORADIUS loct 120.1 30.26 100 km WITHDIST ASC
    
}
