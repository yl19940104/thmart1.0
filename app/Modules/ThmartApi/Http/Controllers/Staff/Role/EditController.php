<?php
namespace App\Modules\ThmartApi\Http\Controllers\Staff\Role;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\StaffRole;
use App\Modules\ThmartApi\Models\StaffRoleAuth;
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
            if ((new StaffRole)->getOneByRoleName($param['roleName'])) returnJson(0, '角色名已存在');
        	$res = (new StaffRole)->saveOne(['roleName'=>$param['roleName']]);
            (new StaffRoleAuth)->deleteList($res['id']);
            foreach ($param['authNameIdArray'] as $v) {
                (new StaffRoleAuth)->saveOne(['role_id'=>$res['id'], 'auth_id'=>$v]);
            }
        } else {
            (new StaffRole)->updateOne(['id'=>$param['id'], 'roleName'=>$param['roleName']]);
            (new StaffRoleAuth)->deleteList($param['id']);
            foreach ($param['authNameIdArray'] as $v) {
                (new StaffRoleAuth)->saveOne(['role_id'=>$param['id'], 'auth_id'=>$v]);
            }
        }
        returnJson(1, '操作成功');
    }

    public function validateParam(Request $request)
    {
        $validator = \Validator::make($request->input(), [
            'roleName' =>  'required',
            /*'authNameIdArray' =>  'required',*/
        ], [
            'required' => ':attribute 为必填项',
        ], [
            'roleName' => '角色名',
            'authNameIdArray' => '权限数组',
        ]);
        return $validator;
    }
}