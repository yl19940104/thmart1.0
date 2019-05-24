<?php
namespace App\Modules\ThmartApi\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
/*use App\Modules\ThmartApi\Models\UserLogin;*/
use App\Modules\ThmartApi\Models\MobileCode;
use App\Modules\ThmartApi\Models\UserInfo;
use Illuminate\Http\Request;

class MobileRegisterController extends Controller
{ 
    public function __construct(){}
    
	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) returnJson(0, $validator->getMessageBag());
		$param = $request->input();
        /*$userInfo = new UserInfo;*/
        $mobileCode = new MobileCode;
        $res = UserInfo::select('id', 'password', 'nickname', 'headimg_url')->where(['mobile'=>$param['mobile']])->get()->toArray();
        if ($res && $res['0']['password']) returnJson(104, '账号已存在');
        $get = $mobileCode->getOne($param['mobile']);
        if ($get['code'] != $param['code']) returnJson(106, '验证码错误');
        if ($param['password'] != $param['rePassword']) returnJson(107, '两次密码不同');
        $salt = mt_rand(100000, 999999);
        //如果该手机号既未注册过，又没有绑定过微信
        if (!$res) {
            $data = [
                'salt'          =>   $salt,
                'password'      =>   md5Password($param['password'], $salt),
                'mobile'        =>   $param['mobile'],
                'nickname'      =>   $param['mobile'],
                'headimg_url'   =>   config('config.headimg'),
            ];
            $message = UserInfo::create($data);
            $token = createToken($message['id']);
            $headimg_url = config('config.headimg');
            $nickname = $param['mobile'];
        }
        //如果该手机号绑定过微信，但是手机号没有注册过
        if ($res && !$res['0']['password']) {
            $data = [
                'salt'       =>   $salt,
                'password'   =>   md5Password($param['password'], $salt),
            ];
            UserInfo::where('id', $res['0']['id'])->update($data);
            $token = createToken($res['0']['id']);
            $headimg_url = $res['0']['headimg_url']; 
            $nickname = $res['0']['nickname'];
        }
        returnJson(1, 'success', ['token'=>$token, 'nickname'=>$nickname, 'headimgurl'=>$headimg_url]);
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'mobile'     => 'required|regex:/^1[34578][0-9]{9}$/',
            'code'       => 'required|integer',
            'password'   => 'required',
            'rePassword' => 'required',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
            'regex'    => ':attribute 必须为手机号格式',
		], [
            'mobile'     => '用户名',
            'code'       => '验证码',
            'password'   => '密码',
            'rePassword' => 'repeat密码'
		]);
		return $validator;
	}
}

