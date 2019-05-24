<?php
namespace App\Modules\ThmartApi\Http\Controllers\ItemSale;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\ItemSalePrice;
use Illuminate\Http\Request;

class EditController extends Controller
{ 
    
    public function __construct(){}

	public function index(Request $request)
	{
		$param = $request->input();
		if (!is_array($param['array'])) $param['array'] = json_decode($param['array'], true);
		if (!isset($param['array']) || !is_array($param['array'])) returnJson(0, '参数格式错误');
		foreach ($param['array'] as $k => &$v) {
			$v['startTime'] = strtotime($v['startTime']);
			$v['endTime'] = strtotime($v['endTime']);
			$v['salePrice'] *= 100;
			if (!$v['id']) unset($param['array'][$k]['id']);
		}
		unset($v);
		$res = (new ItemSalePrice)->addSalePriceList($param['array'], $param['type']);
        if (!isset($res['message'])) {
        	returnJson(1, 'success');
        } else {
            returnJson(0, $res['message']);
        };
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'type'     => 'required|integer|min:1|max:2',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
            'min'      => ':attribute 最小为1',
            'max'      => ':attribute 最大为2',
		], [
            'type'     => '促销类型',
		]);
		return $validator;
	}
}

