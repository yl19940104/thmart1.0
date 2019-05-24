<?php
namespace App\Modules\ThmartApi\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Orders;
use App\Modules\ThmartApi\Models\OrdersSku;
use App\Modules\ThmartApi\Models\MongoComment;
use Illuminate\Http\Request;

class DetailController extends Controller
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
	    if ($res['0']['orderTime']) {
	    	$res['0']['endTime'] = $res['0']['orderTime']+7200;
	    	$res['0']['endTime'] = date('Y-m-d H:i:s', $res['0']['endTime']);
	    	$res['0']['orderTime'] = date('Y-m-d H:i:s', $res['0']['orderTime']);
	    }
	    if ($res['0']['sendTime'])  $res['0']['sendTime'] = date('Y-m-d H:i:s', $res['0']['sendTime']);
	    if ($res['0']['payTime'])  $res['0']['payTime'] = date('Y-m-d H:i:s', $res['0']['payTime']);
	    $data = (new OrdersSku)->getSkuIdList($param['orderNumber']);
	    $res['0']['data'] = [];
	    foreach ($data as &$v) {
	        $select = [
	            'userId'      => intval($this->userId),
                'ordersSkuId' => intval($v['id']),
            ];
	        $comment = MongoComment::where($select)->get()->toArray();
	        if (isset($comment) && $comment) {
	            $v['hasComment'] = '1';
            } else {
                $v['hasComment'] = '0';
            }
	    	$v['price'] = floor($v['price']/$v['number']*100)/100;
	    	$v['prop'] = $v['skuPropName'];
	    	$v['goodsName'] = $v['title'];
	    	if (!isset($res['0']['data']['brand'])) $res['0']['data']['brand'] = [];
	    	$res['0']['data']['brand'][$v['brandId']]['brandName'] = $v['brandName'];
	    	if (!isset($res['0']['data']['brand'][$v['brandId']]['total'])) $res['0']['data']['brand'][$v['brandId']]['total'] = 0;
	    	if (!isset($res['0']['data']['brand'][$v['brandId']]['data'])) $res['0']['data']['brand'][$v['brandId']]['data'] = [];
	    	array_push($res['0']['data']['brand'][$v['brandId']]['data'], $v);
	    	$res['0']['data']['brand'][$v['brandId']]['total'] += $v['price'] * $v['number'];
	    }
	    unset($v);
	    $res['0']['data']['brand'] = array_values($res['0']['data']['brand']);
	    returnJson(1, 'success', $res['0']);
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

  