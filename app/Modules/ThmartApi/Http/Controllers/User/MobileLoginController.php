<?php
namespace App\Modules\ThmartApi\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\UserInfo;
use App\Modules\ThmartApi\Models\MobileCode;
use App\Modules\ThmartApi\Models\Address;
use Illuminate\Http\Request;

class MobileLoginController extends Controller
{ 
    public function __construct(){}
    
    public function index(Request $request)
    {
        $validator = $this->validateParam($request);
        if ($validator->fails()) returnJson(0, $validator->getMessageBag());
        $param = $request->input();
        if (!$res = UserInfo::where('mobile', $param['mobile'])->get()->toArray()) {
            returnJson(109, '手机号未被注册');
        }
        //如果微信绑定过手机号但手机号未注册
        /*if (!$res['0']['password']) returnJson(109, '手机号未被注册');*/
        if (!isset($param['code']) && !isset($param['password'])) returnJson(108, '密码或验证码错误');
        if (isset($param['code'])) {
            $this->codeLogin($res['0']['id'], $param['mobile'], $param['code'], $res['0']['nickname'], $res['0']['headimg_url']);
        } elseif (isset($param['password'])) {
            if ($res['0']['password'] != md5Password($param['password'], $res['0']['salt'])) returnJson(110, '密码错误');
            $this->updateLogin($res['0']['id']);
            $token = createToken($res['0']['id']);
            $address = Address::select('fullName', 'phone', 'email', 'regionDetail', 'province')
                ->where(['isDefault'=>1, 'userId'=>$res['0']['id']])
                ->get()
                ->toArray();
            returnJson(1, 'success', ['token'=>$token, 'nickname'=>$res['0']['nickname'], 'headimgurl'=>adminDomain().$res['0']['headimg_url'], 'id'=>$res['0']['id'], 'mobile'=>$address['0']['phone'], 'email'=>$address['0']['email'], 'address_en' => $address['0']['regionDetail'], 'address_cn' => $address['0']['province'], 'pic'=>adminDomain().$res['0']['headimg_url'], 'data'=>false, 'fullname'=>$address['0']['fullName']]);
        }
    }

    public function validateParam(Request $request)
    {
        $validator = \Validator::make($request->input(), [
            'mobile'   => 'required|regex:/^1[34578][0-9]{9}$/',
        ], [
            'required' => ':attribute 为必填项',
            'regex'    => ':attribute 必须为手机号格式',
        ], [
            'mobile' => '手机号',
        ]);
        return $validator;
    }

    //验证码登录验证
    private function codeLogin($id, $mobile, $code, $nickname, $headimgurl)
    {
        $mobileCode = new MobileCode;
        $res = $mobileCode->getOne($mobile);
        if ($res['code'] != $code) returnJson(106, '验证码错误');
        $this->updateLogin($id);
        $token = createToken($id);
        returnJson(1, 'success', ['token'=>$token, 'nickname'=>$nickname, 'headimgurl'=>$headimgurl]);
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
}

