<?php
namespace App\Modules\ThmartApi\Http\Controllers\Coupon;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Coupon;
use App\Modules\ThmartApi\Models\Sku;
use App\Modules\ThmartApi\Models\CouponSku;
use Illuminate\Http\Request;

class EditSkuListController extends Controller
{ 

	public function __construct(){}

	public function index(Request $request)
	{
		$skuList = [];
		$param = $request->input();
		if (!isset($param['skuList'])) returnJson(0, 'skuList missing');
		$param['skuList'] = json_decode($param['skuList'], true);
		foreach ($param['skuList'] as $v) {
			array_push($skuList, $v['sku']);
		}
        foreach ($skuList as $v) {
        	if (!((new Sku)->getOneBySkuNumber($v))) returnJson(0, 'sku '.$v.' 不存在');
        }
        $data = (new Coupon)->getOne($param['id']);
        //判断skuList里面的sku是否已加入不允许叠加的优惠池或满减池
		$res = $this->checkSkuListIsOverlay($skuList, $data['0']['type']);
		if ($res['message']) returnJson(0, 'skuNumber '.$res['message'].' 已经加入其它无法叠加的优惠活动中');
		//如果不传id，则添加coupon以及skuList
    	if (!(new Coupon)->getOne($param['id'])) returnJson(0, '该id不存在');
    	$this->saveCouponAndSku($param);
        returnJson(1, 'success');
	}

	public function saveCouponAndSku($param)
	{
    	if (isset($param['skuList'])) {
	    	(new CouponSku)->deleteList($param['id']);
	    	foreach ($param['skuList'] as $v) {
	    		$res = (new Sku)->getTitleBySkuNumber($v['sku']);
	    		(new CouponSku)->addOne(['skuNumber'=>$v['sku'], 'couponId'=>$param['id'], 'title'=>$res['0']['title'], 'skuId'=>$res['0']['id']]);
	    	}
	    }
	}

    //判断skuList里面的sku是否已加入不允许叠加的优惠池或满减池
	public function checkSkuListIsOverlay($skuList, $type)
	{
		$type = $type == 1 ? 2 : 1;
		$res = (new CouponSku)->getListBySkuNumberAndType($skuList, $type);
		foreach ($res as $v) {
			if ($v['isOverlay'] == 0 && $v['couponId']) {
				return ['message'=>$v['skuNumber']];
			}
		}
		return true;
	}
}

