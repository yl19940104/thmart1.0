<?php
namespace App\Modules\ThmartApi\Http\Controllers\User\Wx;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\UserInfo;
use Illuminate\Http\Request;

class LoginController extends Controller
{ 
    public function __construct(){}
    
    public function index(Request $request)
    {
        $get = $request->input();
        //$urlArray中的第一个元素是微信已绑定手机号情况下的跳转地址，第二个元素是未绑定手机号情况下的跳转地址,第三个元素表示是移动端微信登录还是PC端微信登录,mobile代表移动端，pc代表PC端
        $urlArray = explode('|', $get['state']);
        $source = isset($urlArray['2']) ? 'unionid' : 'openid';
        $appid = config('config.appid.'.$source);
        $secret = config('config.secret.'.$source);
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$get['code']}&grant_type=authorization_code";
        $res = https_request($url,true);
        if (!($res = json_decode($res,true))) returnJson(111, 'third_callback_data_error');
        /*if ($res['errcode']) returnJson(112, $res['errmsg']);*/
        if ($res[$source]) {
            //如果此微信id已经登陆过则跳转至原来访问的地址
            $data = UserInfo::select('id', 'headimg_url', 'nickname')->where('wx_'.$source, $res[$source])->get()->toArray();
            if ($data) {
                $token = createToken($data['0']['id']);
                $login_time = date('Y-m-d H:i:s', time());
                UserInfo::where('wx_'.$source, $res[$source])->update(['login_time'=>$login_time]);
                if (strpos($urlArray['0'], '?')) {
                    return redirect($urlArray['0'].'&token='.$token.'&headimgurl='.$data['0']['headimg_url'].'&nickname='.$data['0']['nickname']);
                } else {
                    return redirect($urlArray['0'].'?token='.$token.'&headimgurl='.$data['0']['headimg_url'].'&nickname='.$data['0']['nickname']);
                }
            }
        }
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$res['access_token']}&openid={$res['openid']}";
        $res = https_request($url,true);
        if (!($res = json_decode($res,true))) returnJson(-101, 'third_get_user_error');
        $res = http_build_query($res);
        //跳转至微信绑定手机号页面
        return redirect($urlArray['1'].'?'.$res);
    }     
}

