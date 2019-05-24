<?php
namespace App\Modules\ThmartApi\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Staff;
use App\Modules\ThmartApi\Models\StaffInfoRole;
use App\Modules\ThmartApi\Models\StaffSupplier;
use Illuminate\Http\Request;

class ListController extends Controller
{ 
    public function __construct(){}
    
    public function index(Request $request)
    {
        $validator = $this->validateParam($request);
        if ($validator->fails()) returnJson(0, $validator->getMessageBag());
        $param = $request->input();
    	$res = (new Staff)->getList($param['pageSize']);
    	foreach ($res['data'] as &$v) {
    		$v['role'] = (new StaffInfoRole)->getList($v['id']);
            $v['supplierList'] = (new StaffSupplier)->staffSupplierNameList($v['id']);
    	}
    	unset($v);
        returnJson(1, 'success', $res);
    }

    public function validateParam(Request $request)
    {
        $validator = \Validator::make($request->input(), [
            'pageSize' => 'required|integer',
        ], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 为必整数',
        ], [
            'pageSize' => '每页显示数据量',
        ]);
        return $validator;
    }
}