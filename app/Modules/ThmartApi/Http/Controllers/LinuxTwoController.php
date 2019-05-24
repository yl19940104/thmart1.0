<?php
namespace App\Modules\ThmartApi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Juhe\Juhe;

class LinuxTwoController extends Controller
{ 

    public function __construct(){}

	public function index(Request $request)
	{
		$ordersArray = [];
		//将要修改的订单号以及它的订单状态
		$saveData = [];
		$res = DB::table('orderssku')
			->select('logistics', 'company', 'orders.id', 'item.type', 'orderssku.logisticsTime')
			->leftjoin('orders', 'orders.id', '=', 'orderssku.orderId')
			->leftjoin('item', 'item.id', '=', 'orderssku.goodsId')
			->where('orders.status', '2')
			->get()
			->toArray();
		foreach ($res as $v) {
			if (!isset($ordersArray[$v->id])) $ordersArray[$v->id] = [];
			array_push($ordersArray[$v->id], $v);       
			if (!isset($saveData[$v->id])) $saveData[$v->id] = ['id'=>$v->id, 'status'=>3];
		}
		foreach ($ordersArray as $v) {
			foreach ($v as $value) {
				$params = array(
				   'key' => '198f3399d4bc3dafa970d416d8f89bfb',
				   'com' => $value->company,
				   'no'  => $value->logistics,
				);
				$res = new Juhe($params['key']);
		        $result = $res->query($params['com'],$params['no']);
		        //如果订单里的商品存在物流查询返回错误或物流未签收且非电子票且订单非自由物流或填物流时间未达到7天，则订单状态不改变
		        if (($result['error_code'] != 0 || $result['result']['status'] != 1) && $value->type != 2 && ($value->logistics != 11 || $value->company != 'ziyouwuliu' || ($value->logisticsTime + 604800 > time()))) unset($saveData[$value->id]); 
			}
		}
		//改变订单状态为已到货
		foreach ($saveData as $v) {
			DB::table('orders')->where('id', $v['id'])->update($v);
		}
		returnJson(1, 'success');
	}
}

