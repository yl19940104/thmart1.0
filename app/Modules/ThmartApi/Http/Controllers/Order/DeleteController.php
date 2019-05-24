<?php
namespace App\Modules\ThmartApi\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Orders;
use App\Modules\ThmartApi\Models\OrdersSku;
use Illuminate\Http\Request;

class DeleteController extends Controller
{ 

	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) {
			return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
		}
		$param = $request->input();
	    $res = (new Orders)->getOne($param['orderNumber']);
        if (!$res) returnJson(116, 'wrong orderNumber');
	    if ($res['0']['userId'] != $this->userId) returnJson(0, 'orderNumber is not belong to the user'); 
	    if ($res['0']['status'] != 3 && $res['0']['status'] != 4) returnJson(0, '该订单非关闭或到货状态，不能删除'); 
	    (new Orders)->deleteOne($param['orderNumber']);
	    returnJson(1, 'success');
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'orderNumber' => 'required',
		], [
            'required' => ':attribute 为必填项',
		], [
            'orderNumber' => '订单编号',
		]);
		return $validator;
	}
}

  