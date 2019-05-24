<?php
namespace App\Modules\ThmartApi\Http\Controllers\Coupon;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Coupon;
use App\Modules\ThmartApi\Models\CouponUser;
use Illuminate\Http\Request;

class GetController extends Controller
{ 

	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) {
			return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
		}
		$param = $request->input();
		$res = (new Coupon)->getOneCoupon($param['id']);
		if (!isset($res) || !$res) returnJson(0, '无此优惠券');
		$res = (new CouponUser)->getOne($this->userId, $param['id']);
		if (isset($res) && $res) returnJson(0, '用户已领过此优惠券');
		$data = [
			'couponId' => $param['id'],
			'userId'   => $this->userId,
			'getTime'  => time(),
		];
		(new CouponUser)->saveRecord($data);
		returnJson(1, 'success');
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

