<?php
namespace App\Modules\ThmartApi\Http\Controllers\Staff\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\StaffAuth;
use Illuminate\Http\Request;

class EditController extends Controller
{ 
    public function __construct(){}
    
    public function index(Request $request)
    {
        $validator = $this->validateParam($request);
        if ($validator->fails()) returnFalse($validator->getMessageBag());
        $param = $request->input();
        if (!isset($param['id'])) {
            if ((new StaffAuth)->getOneByAuth($param['auth'])) returnJson(0, '权限地址已存在');
            $res = (new StaffAuth)->saveOne(['authName'=>$param['authName'], 'auth'=>$param['auth']]);
            returnJson(1, '添加成功');
        } else {
            (new StaffAuth)->updateOne($param);
            returnJson(1, '修改成功');
        }
    }

    public function validateParam(Request $request)
    {
        $validator = \Validator::make($request->input(), [
            'authName' =>  'required',
            'auth'     =>  'required',
        ], [
            'required' => ':attribute 为必填项',
        ], [
            'authName' => '权限名',
            'auth' => '权限',
        ]);
        return $validator;
    }
}