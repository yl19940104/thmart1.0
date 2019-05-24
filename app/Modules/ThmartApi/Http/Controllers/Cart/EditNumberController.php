<?php
namespace App\Modules\ThmartApi\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Cart;
use App\Modules\ThmartApi\Models\Sku;
use Illuminate\Http\Request;

class EditNumberController extends Controller
{ 

	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) {
			returnJson(0, $validator->getMessageBag());
		}
		$param = $request->input();
		if (!$res = ((new Cart)->getOneCart($param['cartId']))) returnJson(0, '该cartId不存在');
		if ($res['0']['userId'] != $this->userId) returnJson(0, 'cartId不属于此用户');
		$res = (new Sku)->getOne($res['0']['skuId']);
		if ($res['stock'] < $param['number']) returnJson(114, '库存不足');
		$data = ['id'=>$param['cartId'], 'number'=>$param['number']];
		(new Cart)->saveOneNumber($data);
		$res = (new Cart)->getTotal($this->userId);
		returnJson(1, 'success', ['total'=>$res['total'], 'reduceTotal'=>$res['reduceTotal']]);
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'cartId'   => 'required|integer',
		    'number'   => 'required|integer',
		], [
            'required' => ':attribute 为必填项',        
            'integer'  => ':attribute 必须为数字',
		], [
            'cartId'   => '购物车id',
            'number'   => '数量',
		]);
		return $validator;
	}
}

