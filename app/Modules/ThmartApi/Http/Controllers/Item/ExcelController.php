<?php
namespace App\Modules\ThmartApi\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Orders;
use App\Modules\ThmartApi\Models\OrdersSku;
use App\Modules\ThmartApi\Models\ItemSalePrice;
use Illuminate\Http\Request;

class ExcelController extends Controller
{ 
	public function __construct(){}

	public function index(Request $request)
	{
		$data = [];
		$param = $request->input();
		foreach ($param['idArray'] as $k => $v) {
			$res = DB::table('sku')
				->select('sku.itemId as 商品id', 'sku.skuNumber as sku', 'item.title as 英文名字', 'item.subTitle as 中文名字', 'sku.propName as 规格', 'brand.name as 品牌', 'sku.price as 原价', 'sku.costPrice as 成本价', 'sku.stock as 库存', 'sku.type as 型号', 'supplier.supplier_name as 供应商', 'item.audited as 上架状态,0:下架,1:上架', 'sku.id as skuId')
				->leftjoin('item', 'item.id', '=', 'sku.itemId')
				->leftjoin('brand', 'brand.id', '=', 'item.brandName')
				->leftjoin('supplier', 'supplier.id', '=', 'item.shopId')
				->where(['sku.itemId'=>$v, 'sku.isDelete'=>0])
				->get()
				->toArray();
			$res = objectToArray($res);
			foreach ($res as &$value) {
				$propName = json_decode($value['规格'], true);
				$value['规格'] = '';
				foreach ($propName as $k => $v) {
					$value['规格'] .= ' '.$propName[$k][0];
				}
				if (!$value['型号']) $value['型号'] = '';
				$value['原价'] *= 0.01;
				$value['成本价'] *= 0.01;
				$result = (new ItemSalePrice)->getSkuMinPrice($value['skuId'], 1);
				if (isset($result) && $result) {
					$value['促销价'] = $result['0']['salePrice'];
					$value['开始时间'] = date('Y-m-d H:i:s', $result['0']['startTime']);
					$value['结束时间'] = date('Y-m-d H:i:s', $result['0']['endTime']);
				} else {
					$value['促销价'] = '';
					$value['开始时间'] = '';
					$value['结束时间'] = '';
				}
				unset($value['skuId']);
				array_push($data, $value);
			}
			$data = json_encode($data);
            $data = json_decode($data); 
		}
	    returnJson(1, 'success', $data);
	}
}

  