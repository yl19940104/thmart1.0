<?php
namespace App\Modules\ThmartApi\Http\Controllers\User\Wx;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\UserInfo;
use Illuminate\Http\Request;

class MiniProgramController extends Controller
{
    public function __construct(){}

    public function index(Request $request)
    {
        $get = $request->input();
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=wxd6f3b503f5b3b0c2&secret=f73478711455df7245d8623da032cb19&js_code={$get['code']}&grant_type=authorization_code";
        $res = https_request($url,true);
        if (!($res = json_decode($res,true))) returnJson(111, 'third_callback_data_error');
        if ($res['unionid']) {
            /*$userLogin = new UserLogin;
            $res = $userLogin->isLogin(config('config.login_type.miniProgram'), $res['unionid']);
            returnJson(1, 'success', $res);*/
            $userInfo = new UserInfo;
            $data = $userInfo::select('id')->where('wx_unionid', $res['unionid'])->get()->toArray();
            if ($data) {
                $token = createToken($data['0']['id']);
                returnJson(1, 'success', ['token'=>$token, 'isRegister'=>1, 'unionid'=>$res['unionid'], 'data'=>$res]);
            } else {
                returnJson(1, 'success', ['unionid'=>$res['unionid'], 'isRegister'=>0, 'token'=>'', 'session_key'=>$res['session_key']]);
            }
        }
    }
}

