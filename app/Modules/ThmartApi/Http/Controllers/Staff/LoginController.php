<?php
namespace App\Modules\ThmartApi\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Staff;
use App\Modules\ThmartApi\Models\StaffInfoRole;
use Illuminate\Http\Request;

class LoginController extends Controller
{ 
    public function __construct(){}
    
    public function index(Request $request)
    {
        $array = [];
        $validator = $this->validateParam($request);
        if ($validator->fails()) returnFalse($validator->getMessageBag());
        $param = $request->input();
        $res = (new Staff)->getOne($param['username']);
        if (!$res) returnJson(118, '账号或密码错误');
        if ($res['0']['password'] != md5Password($param['password'], $res['0']['salt'])) returnJson(118, '账号或密码错误');
        $data = (new StaffInfoRole)->getRoleIdList($res['0']['id']);
        foreach ($data as $v) {
            array_push($array, $v['role_id']);
        }
        session()->put('userInfo', ['username' => $res['0']['username'], 'id' => $res['0']['id'], 'roleArray' => $array]);
        session()->save();
        returnJson(1, '欢迎用户', session()->get('username'));
    }

    public function validateParam(Request $request)
    {
        $validator = \Validator::make($request->input(), [
            'username' => 'required',
            'password' => 'required',
        ], [
            'required' => ':attribute 为必填项',
        ], [
            'username' => '用户名',
            'password' => '密码',
        ]);
        return $validator;
    }
}