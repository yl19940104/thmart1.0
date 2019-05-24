<?php
namespace App\Modules\ThmartApi\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Orders;
use App\Modules\ThmartApi\Models\OrdersSku;
use App\Modules\ThmartApi\Models\OrdersSpell;
use App\Modules\ThmartApi\Models\Sku;
use Illuminate\Http\Request;

class OrderListController extends Controller
{ 

	public function index(Request $request)
	{
		$param = $request->input();
		$validator = $this->validateParam($request);
		if ($validator->fails()) returnJson(0, $validator->getMessageBag());
		$param = $request->input();
		$condition['userId'] = $this->userId;
		/*(new Orders)->closeOrder($this->userId);*/
        /*returnJson(1, $param['status']);*/
		if (isset($param['status'])) {
			if ($param['status'] != 0 && $param['status'] != 1 && $param['status'] != 2 && $param['status'] != 3 && !is_array($param['status'])) returnJson(0, 'wrong status');
			$condition['status'] = $param['status'];
		}
		$condition['isDelete'] = 0;
	    $res = (new Orders)->getList($condition, $param['pageSize']);
	    foreach ($res['data'] as &$v) {
	    	$data = (new OrdersSku)->getSkuIdList($v['orderNumber']);
	    	foreach ($data as &$value) {
	    		$value['priceSingle']  = $value['price']/$value['number'];
	    	}
	    	$v['skuList'] = $data;
	    	$v['number_left'] = '';
	    	if ($v['status'] == 6) {
                $result = OrdersSpell::where('orderNumber', $v['orderNumber'])->first()->toArray();
                if ($result['pid'] == 0) {
                    $v['number_left'] = (new ordersSpell)->getLeft($result['id']);
                } else {
                    $pidData = OrdersSpell::where('id', $result['pid'])->first()->toArray();
                    $v['number_left'] = (new ordersSpell)->getLeft($pidData['id']);
                }
            }
	    	/*if ($v['status'] == 7) {
	    	    $headimg_array = [];
	    	    $res = (new OrdersSpell())->where(['orderNumber'=>$v['orderNumber']])->first()->toArray();
                if ($res['pid'] == 0) {

                }
            }*/
	    }
	    unset($v);
	    unset($value);
	    returnJson(1, 'success', ['data'=>$res['data'], 'totalPage'=>$res['last_page']]);
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'page'     => 'required|integer',
		    'pageSize' => 'required|integer',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
		], [
            'page'     => '当前页',
		    'pageSize' => '每页显示数据量',
		]);
		return $validator;
	}
}

  