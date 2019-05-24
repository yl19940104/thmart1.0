<?php
namespace App\Modules\ThmartApi\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Orders;
use App\Modules\ThmartApi\Models\OrdersSku;
use Illuminate\Http\Request;

class ExcelController extends Controller
{ 
	public function __construct(){}

	public function index(Request $request)
	{
		$data = [];
		//订单号数组,用来记录一个订单只有一条运费记录
		$orderNumberArray = [];
		$param = $request->input();
		if (!isset($param['idArray'])) returnJson(0, '请选择你要导出的数据');
		foreach ($param['idArray'] as $k => $v) {
			$res = DB::table('orderssku')
				->select('orderssku.orderNumber as 订单号', 'category.title as 一级分类', 'orderssku.brandName as 品牌', 'orderssku.goodsId as 商品id', 'orderssku.skuId as SKU', 'orderssku.title as 商品名称英文全称', 'item.subTitle as 商品名称中文', 'orderssku.skuPropName as 规格', 'sku.type as 型号', 'orderssku.number as 数量', 'orderssku.price as 成交价', 'orderssku.costPrice as 成本价', 'orderssku.skuPrice as 商品价格(包含促销)', 'orders.payTime as 支付时间', 'orders.paySource as 货款来源', 'orders.userId as 用户id', 'orders.fullName as 收件人', 'orders.phone as 收件人手机', 'orders.province as 中文收货地址', 'orders.regionDetail as 英文收货地址', 'orders.email as 邮箱', 'orders.buyerRemark as 留言', 'orders.status as 物流状态', 'logistics as 快递单号', 'company as 快递公司', 'supplier.supplier_name as 供应商', 'user_info.mobile as 注册手机号', 'couponFee as 优惠券分摊金额', 'discountFee as 满减分摊金额', 'feeTotal as 运费', 'orders.code as code', 'staff.username as 上传者', 'item.type as 类型')
				->leftjoin('item', 'item.id', '=', 'orderssku.goodsId')
				/*->leftjoin('sku', 'sku.itemId', '=', 'item.id')*/
                ->leftjoin('sku', 'orderssku.skuId', '=', 'sku.id')
				->leftjoin('category', 'category.name', '=', 'item.categoryName')
				->leftjoin('orders', 'orders.id', '=', 'orderssku.orderId')
				->leftjoin('supplier', 'item.shopId', '=', 'supplier.id')
				/*->leftjoin('userLogin', 'userLogin.userId', '=', 'orders.userId')*/
				->leftjoin('user_info', 'user_info.id', '=', 'orders.userId')
				->leftjoin('staff', 'staff.id', '=', 'item.staff_id')
				->where('orderssku.id', $v)
				/*->where(['userLogin.type'=>'mobile'])*/
				->get()
				->toArray();
			$res = objectToArray($res);
			$res['0']['商品id'] = $res['0']['商品id'];
			if ($res['0']['类型'] != 2) $res['0']['code'] = '无';
			unset($res['0']['类型']);
			if ($res['0']['货款来源'] == 1) $res['0']['货款来源'] = '微信';
			if ($res['0']['货款来源'] == 2) $res['0']['货款来源'] = '支付宝';
			if ($res['0']['物流状态'] == 3) {
				$res['0']['物流状态'] = '已到货';
			} else {
				$res['0']['物流状态'] = '未到货';
			}
			if (!$res['0']['型号']) $res['0']['型号'] = '';
			$res['0']['支付时间'] = date('Y-m-d H:i:s', $res['0']['支付时间']);
			$res['0']['成交价'] *= 0.01;
			$res['0']['成本价'] *= 0.01;
			$res['0']['优惠券分摊金额'] *= 0.01;
			$res['0']['满减分摊金额'] *= 0.01;
			$res['0']['商品价格(包含促销)'] *= 0.01;
			$res['0']['运费'] *= 0.01;
			$res['0']['SKU'] += 818000000;
			$propName = json_decode($res['0']['规格'], true);
			$res['0']['规格'] = '';
			foreach ($propName as $k => $v) {
				$res['0']['规格'] .= ' '.$propName[$k][0];
			}
			array_push($data, $res['0']);
			$data = json_encode($data);
            $data = json_decode($data); 
		}
		foreach ($data as $v) {
			if (!in_array($v->订单号, $orderNumberArray)) {
				array_push($orderNumberArray, $v->订单号);
			} else {
				$v->运费 = 0;
			}
		}
	    returnJson(1, 'success', $data);
	}
}

  