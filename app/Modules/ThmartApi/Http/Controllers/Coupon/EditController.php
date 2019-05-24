<?php
namespace App\Modules\ThmartApi\Http\Controllers\Coupon;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Coupon;
use App\Modules\ThmartApi\Models\Sku;
use App\Modules\ThmartApi\Models\CouponSku;
use Illuminate\Http\Request;

class EditController extends Controller
{ 

	public function __construct(){}

	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) {
			/*return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);*/
			returnFalse($validator->getMessageBag());
		}
		$param = $request->input();
		if (!isset($param['amount']) || $param['amount'] < 0) $param['amount'] = 0;
		if ($param['type'] == 1 && !$param['pic']) returnJson(0, '请上传图片');
		$param['startTime'] = strtotime($param['startTime']);
		$param['endTime'] = strtotime($param['endTime']);
		if ($param['startTime'] == 0 || $param['endTime'] == 0) returnJson(0, '时间格式错误');
		//是否传了skuList参数
		if (isset($param['skuList'])) {
			//判断skulist里的sku是否都存在
            foreach ($param['skuList'] as $v) {
            	if (!((new Sku)->getOneBySkuNumber($v))) returnJson(0, 'sku '.$v.' 不存在');
            }
            $couponId = null;
            //判断skuList里面的sku是否已加入不允许叠加的优惠池或满减池
			$res = $this->checkSkuListIsOverlay($param['skuList'], $param['type']);
			if ($res['message']) returnJson(0, 'skuNumber '.$res['message'].' 已经加入其它无法叠加的优惠活动中');
    	}
		if ($param['type'] == 1 && !isset($param['amount'])) returnJson(0, '需要添加优惠券数量');
		//如果不传id，则添加coupon以及skuList
        if (!isset($param['id']) || $param['id']<=0) {
        	//添加
        	$this->addCouponAndSku($param);
        //如果传id，则更新coupon以及skuList
        } else {
        	if (!(new Coupon)->getOne($param['id'])) returnJson(0, '该id不存在');
        	//更新
        	$this->saveCouponAndSku($param);
        }
        returnJson(1, 'success');
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'startTime'  => 'required',
		    'endTime'    => 'required',
		    'over'       => 'required|integer',
		    'reduce'     => 'required|integer',
		    'type'       => 'required|integer|min:1|max:2',
		    'isOverlay'  => 'required|integer|min:0|max:1',
		    'name'       => 'required',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 为整数',
            'min'      => ':attribute 最小为1',
            'max'      => ':attribute 最大为2',
		], [
            'id'       => '商品编号',
		]);
		return $validator;
	}

	public function addCouponAndSku($param)
	{
        $res = (new Coupon)->addOne($param);
    	if (isset($param['skuList'])) {
    		foreach ($param['skuList'] as $v) {
    			$data = (new Sku)->getTitleBySkuNumber($v);
        		(new CouponSku)->addOne(['skuNumber'=>$v, 'couponId'=>$res['id'], 'title'=>$data['0']['title'], 'skuId'=>$data['0']['id']]);
        	}
    	}
	}

	public function saveCouponAndSku($param)
	{
		$data = $param;
    	unset($data['skuList']);
    	(new Coupon)->saveOne($data);
    	if (isset($param['skuList'])) {
	    	(new CouponSku)->deleteList($param['id']);
	    	foreach ($param['skuList'] as $v) {
	    		$res = (new Sku)->getTitleBySkuNumber($v);
	    		(new CouponSku)->addOne(['skuNumber'=>$v, 'couponId'=>$param['id'], 'title'=>$res['0']['title']]);
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

