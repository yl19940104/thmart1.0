<?php
namespace App\Modules\ThmartApi\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Cart;
use Illuminate\Http\Request;

class DeleteController extends Controller
{ 

	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) {
			returnJson(0, $validator->getMessageBag());
		}
	    $param = $request->input();
    	if (!$res = ((new Cart)->getOneCart($param['cartId']))) returnJson(0, 'cartId不存在');
        if ($res['0']['userId'] != $this->userId) returnJson(0, 'cartId不属于此用户');
        (new Cart)->deleteList([$param['cartId']]);
        //获取购物车选中商品的总价以及满减价格
		$res = (new Cart)->getTotal($this->userId);
		returnJson(1, 'success', ['total'=>$res['total'], 'reduceTotal'=>$res['reduceTotal']]);
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'cartId'   => 'required|integer',
		], [
            'required' => ':attribute 为必填项',        
            'integer'  => ':attribute 必须为数字',
		], [
            'cartId'   => '购物车id',
		]);
		return $validator;
	}
}

