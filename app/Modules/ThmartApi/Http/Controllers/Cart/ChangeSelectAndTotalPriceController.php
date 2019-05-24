<?php
namespace App\Modules\ThmartApi\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Cart;
use App\Modules\ThmartApi\Models\Sku;
use App\Modules\ThmartApi\Models\ItemSalePrice;
use App\Modules\ThmartApi\Models\CouponSku;
use Illuminate\Http\Request;

class ChangeSelectAndTotalPriceController extends Controller
{ 

	public function index(Request $request)
	{
		/*returnJson(1, (new Sku)->getOneUnDelete(170));*/
		$validator = $this->validateParam($request);
		if ($validator->fails()) {
			returnJson(0, $validator->getMessageBag());
		}
		$param = $request->input();
		if (isset($param['allSelect'])) {
			if ($param['allSelect'] == 1) {
				//勾选某一用户所有购物车商品
                (new Cart)->changeSelectAll($this->userId, 1);
			} else {
				//取消勾选某一用户所有购物车商品
                (new Cart)->changeSelectAll($this->userId, 0);
			}
		} else {
			//勾选或取消某一用户的指定购物车商品
			(new Cart)->changeSelectArray($param['isSelect'], $param['cartIdArray']);
		}
		//获取购物车选中商品的总价以及满减价格
		$res = (new Cart)->getTotal($this->userId);
		returnJson(1, 'success', ['total'=>$res['total'], 'reduceTotal'=>$res['reduceTotal']]);
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'isSelect'  => 'integer|min:0|max:1',
		    'allSelect' => 'integer|min:0|max:1',
		], [
            'required' => ':attribute 为必填项',        
            'integer'  => ':attribute 必须为数字',
            'min'      => ':attribute 最小为0',
            'max'      => ':attribute 最大为1',
		], [
            'isSelect'  => '是否被选中',
            'allSelect' => '全选或反选',
		]);
		return $validator;
	}
}

