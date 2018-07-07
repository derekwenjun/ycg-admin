<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use Dingo\Api\Routing\Helpers;

use Log;
use Qiniu\Auth;

class QiniuController extends Controller
{

    use Helpers;

    /**
     * 请求验证码
     * @return \Illuminate\Http\Response
     */
    public function uploadToken(Request $request)
    {
        $eventType      = $request->json('eventType');
        
        $accessKey      = 'mlpcU-aMlOC5QEdGCmyn3zX7bBRi3x7xys2hrYEp';
        $secretKey      = 'pbc-0_3miTEvqQs3pD0mOl6OjZWlmnRjk4-fvfDY';

        // 构建鉴权对象
        $auth = new Auth($accessKey, $secretKey);
        // 要上传的空间
        $bucket = 'meiqiu';
        // 生成上传 Token
        $token = $auth->uploadToken($bucket);

        return $this->response->array(['token' => $token]);
    }

}
