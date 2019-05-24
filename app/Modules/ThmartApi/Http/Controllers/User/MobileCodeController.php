<?php
namespace App\Modules\ThmartApi\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Modules\ThmartApi\Models\MobileCode;
use Illuminate\ChuanglanSmsHelper\ChuanglanSmsApi;

class MobileCodeController extends Controller
{ 
    public function __construct(){}
    
	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) returnJson(0, $validator->getMessageBag());
		$param = $request->input();
        $mobileCode = new MobileCode; 
        $ChuanglanSmsApi = new ChuanglanSmsApi;
        $res = $mobileCode->getOne($param['mobile']);
        $data['mobile'] = $param['mobile'];
        $data['code'] = $this->createCode();
        $data['time'] = $res ? $res['time'] + 1 : 1;
        $data['createTime'] = time();
        if ($res) {
            if ($data['time'] > 5 && (time() - ($res['createTime'])) < (30 * 60)) {
                returnJson(105, '发送频率过快，稍后再试');
                $data['time'] = 1;
            } 
            $mobileCode->saveOne($data);
        } else {
            $mobileCode->addOne($data);
        }
        if ($ChuanglanSmsApi->sendSMS($data['mobile'], $data['code'])) {
        	returnJson(1, 'success');
        } else {
        	returnJson(0, 'fail');
        };
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'mobile' => 'required|regex:/^1[345789]\d{9}$/',
		], [
            'required' => ':attribute 为必填项',
            'regex'    => ':attribute 必须为手机号格式',
		], [
            'mobile' => '手机号',
		]);
		return $validator;
	}

	/*
     * 生成验证码
     */
	private function createCode()
	{
        return mt_rand(100000, 999999);
	}
}

