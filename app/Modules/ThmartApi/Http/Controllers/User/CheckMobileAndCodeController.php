<?php
namespace App\Modules\ThmartApi\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
/*use App\Modules\ThmartApi\Models\UserLogin;
use App\Modules\ThmartApi\Models\MobileCode;*/
use App\Modules\ThmartApi\Models\UserInfo;
use App\Modules\ThmartApi\Models\MobileCode;
use Illuminate\Http\Request;

/*
 *  重置密码时验证手机号和验证码
 */
class CheckMobileAndCodeController extends Controller
{ 
    public function __construct(){}
    
    public function index(Request $request)
    {
        $validator = $this->validateParam($request);
        if ($validator->fails()) returnJson(0, $validator->getMessageBag());
        $param = $request->input();
        $mobileCode = new MobileCode;
        if (!$res = UserInfo::select('id')->where('mobile', $param['mobile'])->get()->toArray()) returnJson(109, '手机号未注册');
        $data = $mobileCode->getOne($param['mobile']);
        if ($data['code'] != $param['code']) returnJson(106, '验证码错误');
        $string = base64_encode((time() + config('config.tokenTTL')).$res['0']['id']);
        returnJson(1, 'success', ['id'=>md5(md5($string)).$string]);
    }

    public function validateParam(Request $request)
    {
        $validator = \Validator::make($request->input(), [
            'mobile'   => 'required|regex:/^1[34578][0-9]{9}$/',
            'code'     => 'required|integer'
        ], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 数字',
            'regex'    => ':attribute 必须为手机号格式',
        ], [
            'mobile' => '手机号',
        ]);
        return $validator;
    }
}

