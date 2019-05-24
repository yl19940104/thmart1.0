<?php
namespace App\Modules\ThmartApi\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\UserInfo;
use Illuminate\Http\Request;

class ChangePasswordController extends Controller
{ 
    
    public function index(Request $request)
    {
        $validator = $this->validateParam($request);
        if ($validator->fails()) returnJson(0, $validator->getMessageBag());
        $param = $request->input();
        if ($this->userData['password'] != md5Password($param['oldPassword'], $this->userData['salt'])) returnJson(117, 'wrong oldPassword');
        UserInfo::where('id', $this->userId)->update(['password'=>md5Password($param['password'], $this->userData['salt'])]);
        returnJson(1, 'success');
    }

    public function validateParam(Request $request)
    {
        $validator = \Validator::make($request->input(), [
            'password' => 'required|regex:/^[0-9A-Za-z]{6,16}$/',
        ], [
            'required' => ':attribute 为必填项',
            'regex'    => ':attribute 密码长度必须在6-16位之间',
        ], [
            'password' => '密码',
            'oldPassword' => '原始密码',
        ]);
        return $validator;
    }
}

