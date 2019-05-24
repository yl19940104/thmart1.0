<?php
namespace App\Modules\ThmartApi\Http\Controllers\User\Wx;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\MobileCode;
use App\Modules\ThmartApi\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\WxBizDataCrypt\WxBizDataCrypt;
use phpDocumentor\Reflection\Types\Object_;

class WxBindMobileController extends Controller
{ 
    public function __construct(){}
    
    public function index(Request $request)
    {
        $validator = $this->validateParam($request);
        if ($validator->fails()) returnJson(0, $validator->getMessageBag());
        $param = $request->input();
        $source = isset($param['source']) ? 'unionid' : 'openid';
        $mobileCode = new MobileCode;
        $data = UserInfo::select('id')->where('wx_'.$source, $param[$source])->get()->toArray();
        if ($data) return $this->returnJson(113, '微信号已注册');
        //如果没有传mobile参数，说明请求的客户端是微信小程序，那么需要解析出mobile并复制到$param['mobile']
        if (!isset($param['mobile'])) {
            $crypt = new WxBizDataCrypt('wxd6f3b503f5b3b0c2', $param['session_key']);
            $errCode = $crypt->decryptData($param['encryptedData'], $param['iv'], $data);
            $phone = json_decode($data)->phoneNumber;
            if ($errCode != 0) returnJson(0, $errCode);
            $param['mobile'] = $phone;
        } else {
            $res = $mobileCode->getOne($param['mobile']);
            if ($param['code'] != $res['code']) return $this->returnJson(106, '验证码错误');
        }
        $result = UserInfo::select('id', 'wx_'.$source)->where(['mobile'=>$param['mobile']])->get()->toArray();
        //如果手机号未被注册过
        if (!$result) {
            $message = UserInfo::create(['mobile'=>$param['mobile'], 'nickname'=>$param['nickname'], 'headimg_url'=>$param['headimgurl'], 'wx_'.$source=>$param[$source]]);
            $this->updateLogin($message['id']);
            $token = createToken($message['id']);
        }
        if ($result) {
            if ($result['0']['wx_'.$source]) {
                returnJson(121, '该手机号已经绑定过微信号');
            } else {
                UserInfo::where('id', $result['0']['id'])->update(['wx_'.$source=>$param[$source], 'headimg_url'=>$param['headimgurl'], 'nickname'=>$param['nickname']]);
            }
            $token = createToken($result['0']['id']);
        }
        returnJson(1, 'success', ['token'=>$token, 'headimgurl'=>$param['headimgurl'], 'nickname'=>$param['nickname']]);
    }

    //更新登录时间
    private function updateLogin($id)
    {
        $data = [
            'id'         => $id,
            'login_time' => date('Y-m-d H:i:s', time()),
        ];
        UserInfo::where('id', $id)->update($data);
        return true;
    }

    public function validateParam(Request $request)
    {
        $validator = \Validator::make($request->input(), [
            /*'mobile'      => 'required|regex:/^1[34578][0-9]{9}$/',
            'code'        => 'required|integer',*/
            /*'openid'      => 'required',*/
            'nickname'    => 'required',
            'headimgurl'  => 'required',
            /*'unionid'     => 'required',*/
        ], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 数字',
            'regex'    => ':attribute 必须为手机号格式',
        ], [
            /*'mobile'     => '手机号',
            'code'       => '验证码',*/
            /*'openid'     => '验证码',*/
            'nickname'   => '微信昵称',
            'headimgurl' => '头像',
            /*'unionid'    => '微信唯一标识符'*/
        ]);
        return $validator;
    }
}

