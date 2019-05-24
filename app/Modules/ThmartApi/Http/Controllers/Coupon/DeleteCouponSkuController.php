<?php
namespace App\Modules\ThmartApi\Http\Controllers\Coupon;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\CouponSku;
use Illuminate\Http\Request;

class DeleteCouponSkuController extends Controller
{ 

	public function __construct(){}

	public function index(Request $request)
	{
		$param = $request->input();
		if (!isset($param['idArray'])) returnJson(0, 'idArray missing');
		/*foreach ($param['idArray'] as $v) {
			(new Coupon)->saveOne(['id'=>$v, 'endTime'=>time()]);
		}*/
		(new CouponSku)->deleteSku($param['idArray']);
		returnJson(1, 'success');
	}
}

