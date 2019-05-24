<?php
namespace App\Modules\ThmartApi\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Staff;
use App\Modules\ThmartApi\Models\StaffInfoRole;
use App\Modules\ThmartApi\Models\StaffSupplier;
use Illuminate\Http\Request;

class EditController extends Controller
{ 
    public function __construct(){}
    
    public function index(Request $request)
    {
        $validator = $this->validateParam($request);
        if ($validator->fails()) returnFalse($validator->getMessageBag());
        $param = $request->input();
        //修改
        if (isset($param['id'])) {
            (new Staff)->saveOne(['id'=>$param['id'], 'username'=>$param['username']]);
            (new StaffInfoRole)->deleteList($param['id']);
            foreach ($param['roleArray'] as $v) {
                (new StaffInfoRole)->saveOne(['staff_id'=>$param['id'], 'role_id'=>$v]);
            }
            (new StaffSupplier)->deleteStaffList($param['id']);
            if (isset($param['supplierList']) && $param['supplierList']) {
                $this->saveSupplierList($param['id'], $param['supplierList']);
            }
        //添加
        } else {
            if (!$param['password']) returnJson(0, '密码必填');
            if ((new Staff)->getOneByUsername($param['username'])) returnJson(0, '用户名已存在');
            $salt = mt_rand(100000, 999999);
            $password = md5Password($param['password'], $salt);
            $res = (new Staff)->addOne(['username'=>$param['username'], 'password'=>$password, 'salt'=>$salt]);   
            foreach ($param['roleArray'] as $v) {
                (new StaffInfoRole)->saveOne(['staff_id'=>$res['id'], 'role_id'=>$v]);
            }
            (new StaffSupplier)->deleteStaffList($res['id']);
            if (isset($param['supplierList']) && $param['supplierList']) {
                $this->saveSupplierList($res['id'], $param['supplierList']);
            }
        }
        returnJson(1, '操作成功');
    }

    public function validateParam(Request $request)
    {
        $validator = \Validator::make($request->input(), [
            'username'  => 'required',
            'roleArray' => 'required',
        ], [
            'required' => ':attribute 为必填项',
        ], [
            'username'  => '用戶名',
            'roleArray' => '角色数组',
        ]);
        return $validator;
    }

    public function saveSupplierList($staff_id, $supplierList) {
        foreach ($supplierList as $v) {
            (new StaffSupplier)->addOne(['staff_id'=>$staff_id, 'supplier_id'=>$v['id']]);
        }
    }
}