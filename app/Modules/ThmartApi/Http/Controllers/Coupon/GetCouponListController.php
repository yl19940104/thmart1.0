<?php
namespace App\Modules\ThmartApi\Http\Controllers\Coupon;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Coupon;
use App\Modules\ThmartApi\Models\CouponUser;
use Illuminate\Http\Request;

class GetCouponListController extends Controller
{ 

	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) {
			return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
		}
		$param = $request->input();
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'id' => 'required|integer',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
		], [
            'id'  => '优惠券id',
		]);
		return $validator;
	}
}

