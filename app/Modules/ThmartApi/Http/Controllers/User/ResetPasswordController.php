<?php
namespace App\Modules\ThmartApi\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\UserInfo;
use Illuminate\Http\Request;

/*
 *  重置密码
 */
class ResetPasswordController extends Controller
{ 
    public function __construct(){} 

    public function index(Request $request)
    {
        $validator = $this->validateParam($request);
        if ($validator->fails()) returnJson(0, $validator->getMessageBag());
        $param = $request->input();
        if ($param['password'] != $param['rePassword']) returnJson(107, '两次密码不一致');
        $sign = substr($param['id'], 0, 32);
        $token = substr($param['id'], 32);
        if (md5(md5($token)) != $sign) returnJson(101, 'token签名错误');
        $id = substr(base64_decode($token), 10);
        if (!$res = UserInfo::select('id', 'salt')->where('id', $id)->get()->toArray()) returnJson(103, '用户不存在');
        $data = [
            'id' => $id,
            'password' => md5Password($param['password'], $res['0']['salt']),
        ];
        UserInfo::where('id', $id)->update($data);
        returnJson(1, 'success', ['token' => createToken($id)]);
    }

    public function validateParam(Request $request)
    {
        $validator = \Validator::make($request->input(), [
            'id'         => 'required',
            'password'   => 'required',
            'rePassword' => 'required',
        ], [
            'required' => ':attribute 为必填项',
        ], [
            'id'         => 'id参数',
            'password'   => '密码',
            'rePassword' => '重复密码',
        ]);
        return $validator;
    }
}

