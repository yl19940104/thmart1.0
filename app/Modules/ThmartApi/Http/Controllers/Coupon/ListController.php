<?php
namespace App\Modules\ThmartApi\Http\Controllers\Coupon;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\CouponUser;
use Illuminate\Http\Request;

class ListController extends Controller
{ 

	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) {
			return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
		}
		$param = $request->input();
		$res = (new CouponUser)->getListPage($this->userId, $param['isUsed'], $param['pageSize']);
		$res['data'] = convertUrl($res['data']);
		returnJson(1, 'success', ['data'=>$res['data'], 'totalPage'=>$res['last_page']]);
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'page'     => 'required|integer',
		    'pageSize' => 'required|integer',
		    'isUsed'   => 'required|integer',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
		], [
            'page'     => '当前页',
		    'pageSize' => '每页显示数据量',
		    'isUsed'   => '是否使用',
		]);
		return $validator;
	}
}

