<?php
namespace App\Modules\ThmartApi\Http\Controllers\Staff\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\StaffAuth;
use Illuminate\Http\Request;

class ListController extends Controller
{ 
    public function __construct(){}
    
    public function index(Request $request)
    {
    	$validator = $this->validateParam($request);
        if ($validator->fails()) returnJson(0, $validator->getMessageBag());
        $param = $request->input();
    	$res = (new StaffAuth)->getList($param['pageSize']);
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