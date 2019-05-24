<?php
namespace App\Modules\ThmartApi\Http\Controllers\Coupon;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Coupon;
use App\Modules\ThmartApi\Models\CouponSku;
use App\Modules\ThmartApi\Models\ItemSalePrice;
use App\Modules\ThmartApi\Models\Sku;
use Illuminate\Http\Request;

class ItemListController extends Controller
{ 

	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) {
			return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
		}
		$param = $request->input();
		if (!(new Coupon)->getOne($param['couponId'])) returnJson(0, 'couponId不存在');
		$res = (new CouponSku)->getItemIdList($param['couponId']);
		$res = convertUrl($res);
        foreach ($res as &$v) {
        	$v['price'] = DB::table('sku')->where(['itemId'=>$v['id'], 'isDelete'=>0])->min('price');
  			$v['price'] *= 0.01;
			$salePrice = (new Sku)->getMinPrice($v['id']); 
			if (isset($salePrice) && $salePrice) {
				$v['originalPrice'] = $v['price'];
				$v['price'] = $salePrice; 
			}
			$data = (new ItemSalePrice)->hasSalePrice($v['id'], 2);
			$data2 = (new ItemSalePrice)->hasSalePrice($v['id'], 1);
			if (isset($data)) {
				$v['saleType']['type'] = 'group';
			} elseif (isset($data2)) {
				$v['saleType']['type'] = 'sale';
			} else {
				$v['saleType']['type'] = 'none';
			}
	    }
	    unset($v);
	    /*$res = (new ItemSalePrice)->addArrayMinSalePrice($res);*/
	    returnJson(1, 'success', pageData($res, $param['page'], $param['pageSize']));
		/*returnJson(1, 'success', ['data'=>$res['data'], 'totalPage'=>$res['last_page']]);*/
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'page'     => 'required|integer',
		    'pageSize' => 'required|integer',
		    'couponId' => 'required|integer',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
		], [
            'page'     => '当前页',
		    'pageSize' => '每页显示数据量',
		    'couponId' => '优惠券id',
		]);
		return $validator;
	}
}

