<?php
namespace App\Modules\ThmartApi\Http\Controllers\Coupon;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Coupon;
use Illuminate\Http\Request;

class DeleteCouponController extends Controller
{ 

	public function __construct(){}

	public function index(Request $request)
	{
		$param = $request->input();
		if (!isset($param['idArray'])) returnJson(0, 'idArray missing');
		foreach ($param['idArray'] as $v) {
			$res = (new Coupon)->getOne($v);
			if (!isset($res) || !$res) returnJson(0, 'wrong couponId');	
		}
		foreach ($param['idArray'] as $v) {
			(new Coupon)->saveOne(['id'=>$v, 'endTime'=>time()]);
		}
		returnJson(1, 'success');
	}
}

