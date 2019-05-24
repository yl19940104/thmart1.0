<?php
namespace App\Modules\ThmartApi\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Orders;
use App\Modules\ThmartApi\Models\OrdersSku;
use App\Modules\ThmartApi\Models\OrdersCoupon;
use App\Modules\ThmartApi\Models\CouponUser;
use App\Modules\ThmartApi\Models\Address;
use App\Modules\ThmartApi\Models\Cart;
use App\Modules\ThmartApi\Models\Coupon;
use App\Modules\ThmartApi\Models\Sku;
use App\Modules\ThmartApi\Models\OrdersSpell;
use App\Modules\ThmartApi\Models\ItemSalePrice;
use Illuminate\Http\Request;

class PlaceOrderController extends Controller
{ 

	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) {
			return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
		}
		$param = $request->input();
		//如果是拼单流程，如果是自己发起的拼单，则无法拼单
        /*if (isset($param['spellId'])) {
            (new ordersSpell)->checkSpell($this->userId, $param['spellId']);
        }*/
		//如果传了优惠券Id，判断用户是否领过此优惠券以及该优惠券是否已被使用,
		if ($param['couponId']) {
			$couponData = (new CouponUser)->getOne($this->userId, $param['couponId']);
			if (!$couponData) returnJson(0, '优惠券不存在');
			if ($couponData['0']['isUsed'] == 1) returnJson(0, '该优惠券已被使用');
		}
		$res = (new Orders)->getInfo($this->userId, $param);
		if (isset($res['message'])) returnJson(0, $res['message']);
		if (!$address = (new Address)->getDetail($param['addressId'])) returnJson(0, '地址id错误');
		$data = [
			'orderNumber'   =>  makeOrder(),
			'phone'         =>  $address['phone'],
			'fullName'      =>  $address['fullName'],
			'feeTotal'  	=>  $res['feeTotal'],
			'priceTotal'  	=>  $res['total'],
			'couponTotal'  	=>  $res['couponReduce'],
			'email'     	=>  $address['email'],
			'userId'    	=>  $address['userId'],
			'province'  	=>  $address['province'],
			'city'      	=>  $address['city'],
			'regionDetail'  =>  $address['regionDetail'],
			'orderTime'     =>  time(),
			'code'          =>  mt_rand(100000, 999999),
		];
		if (isset($param['buyerRemark'])) {
			$data['buyerRemark'] = $param['buyerRemark'];
		} else {
			$data['buyerRemark'] = 0;
		}
		$order = (new Orders)->saveOne($data);
		foreach ($res['skuReducePriceArray'] as $v) {
			$save = [
				'orderNumber'  =>  $data['orderNumber'],
				'orderId'      =>  $order['id'],
				'title'    	   =>  $v['title'],
				'goodsId'      =>  $v['goodsId'],
				'skuPrice'     =>  $v['skuPrice']*100,
				'skuId'    	   =>  $v['skuId'],
				'skuPropName'  =>  $v['skuPropName'],
				'price'        =>  $v['afterReduce']*100,
				'costPrice'    =>  $v['costPrice']*100,
				'discountFee'  =>  $v['reduce']*100,
				'number'       =>  $v['number'],
				'type'         =>  $v['type'],
				'brandId'      =>  $v['brandId'],
				'brandName'    =>  $v['brandName'],
				'pic'          =>  $v['pic'],
			];
			if ($v['point']) {
				$save['costPrice'] = floor($save['price'] * (1 - $v['point'] * 0.0001));
			}
			/*returnJson(1, $save);*/
			isset($v['couponReduce']) ? $save['couponFee'] = $v['couponReduce']*100 : $save['couponFee'] = 0;
			/*returnJson(1, (new OrdersSku)->saveOne($save));*/
			(new OrdersSku)->saveOne($save);
			$skuInfo = (new Sku)->getOne($v['skuId']);
			//下单锁定库存
			(new Sku)->reduceStock(['id'=>$v['skuId'], 'stock'=>$skuInfo['stock']-$v['number']]);
            //如果是拼单请求，则记录拼单
            if (isset($param['isSpell']) && $param['isSpell']) {
                $addData = [
                    'orderNumber' => $save['orderNumber'],
                    'itemId'      => $save['goodsId'],
                    'userId'      => $this->userId,
                ];
                $saleData = ItemSalePrice::select('amount')->where(['skuId'=>$v['skuId'], ['startTime', '<=', time()], ['endTime', '>=', time()], 'type'=>3])->get();
                $addData['amount'] = $saleData['0']->amount;
                if (isset($param['spellId']) && $param['spellId']) {
                    $spellData = (new OrdersSpell())->where(['id'=>$param['spellId']])->first()->toArray();
                    //判断传过来的spellId是否是发起的拼单id
                    if ($spellData['pid'] == 0) {
                        $addData['pid'] = $param['spellId'];
                    } else {
                        $spell = (new OrdersSpell())->where(['id'=>$spellData['pid']])->first()->toArray();
                        $addData['pid'] = $spell['id'];
                    }
                }
                (new OrdersSpell())->create($addData);
                //订单状态从0改成5，即拼单未支付状态
                Orders::where(['orderNumber'=>$data['orderNumber']])->update(['status'=>5]);
            }
			//invite活动更新日志表
			if ($v['goodsId'] == '1162') {
				$res = DB::table('invite_log')->where(['userid'=>$this->userId, 'status'=>0])->orderBy('id', 'desc')->get();
				if (isset($res) && $res) {
					DB::table('invite_log')->where(['id'=>$res['0']->id])->update(['orderid'=>$data['orderNumber']]);
				}
			}
		}
		(new Cart)->deleteSelect($this->userId);
		
		//如果用户使用了优惠券,记录订单优惠券信息
		if ($param['couponId']) {
			$fee = (new Coupon)->getOne($param['couponId']);
			$data = [
				'couponId'     => $param['couponId'],
				'orderNumber'  => $data['orderNumber'],
				'fee'          => $fee['0']['reduce'],
				'couponUserId' => $couponData['0']['id'],
			];
			(new OrdersCoupon)->addOne($data);
		}
		returnJson(1, 'success', ['orderNumber'=>$order['orderNumber']]);
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'addressId' => 'required|integer',
		    'couponId'  => 'required',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
		], [
            'addressId' => '地址编号',
            'couponId'  => '优惠券编号',
		]);
		return $validator;
	}
}

  