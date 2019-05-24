<?php
namespace App\Modules\ThmartApi\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\MobileCode;
use App\Modules\ThmartApi\Models\UserInfo;
use Illuminate\Http\Request;

/*
 *  注册新手机的时候验证手机号和验证码
 */
class MobileRegisterByPcController extends Controller
{ 
    public function __construct(){}
    
    public function index(Request $request)
    {
        $validator = $this->validateParam($request);
        if ($validator->fails()) returnJson(0, $validator->getMessageBag());
        $param = $request->input();
        $mobileCode = new MobileCode();
        $res = UserInfo::select('id', 'password')->where('mobile', $param['mobile'])->get()->toArray();
        if ($res && $res['0']['password']) returnJson(104, '账号已存在');
        $get = $mobileCode->getOne($param['mobile']);
        if ($get['code'] != $param['code']) returnJson(106, '验证码错误');
        returnJson(1, 'success');
    }

    public function validateParam(Request $request)
    {
        $validator = \Validator::make($request->input(), [
            'mobile'     => 'required|regex:/^1[34578][0-9]{9}$/',
            'code'       => 'required|integer',
        ], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
            'regex'    => ':attribute 必须为手机号格式',
        ], [
            'mobile'     => '用户名',
            'code'       => '验证码',
        ]);
        return $validator;
    }
}

