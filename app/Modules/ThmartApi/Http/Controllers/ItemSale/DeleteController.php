<?php
namespace App\Modules\ThmartApi\Http\Controllers\ItemSale;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\ItemSalePrice;
use Illuminate\Http\Request;

class DeleteController extends Controller
{ 

    public function __construct(){}
    
	public function index(Request $request)
	{
		/*$validator = $this->validateParam($request);
		if ($validator->fails()) {
			return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
		}*/
		$param = $request->input();
		foreach ($param['array'] as $k => $v) {
			if (!isset($v)) unset($param['array'][$k]);
		}
		if ((new ItemSalePrice)->deleteList($param['array'])) returnJson(1, 'success');
	}
	/*public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'shopId'             => 'required|integer',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
            'min'      => ':attribute 最小为0',
            'max'      => ':attribute 最大为1',
            'string'   => ':attribute 必须为字符串',
		], [
            'shopId' => '商家编号',
		]);
		return $validator;
	}*/
}

