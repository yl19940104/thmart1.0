<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Modules\ThmartApi\Models\CrontabChangePrice;
use App\Modules\ThmartApi\Models\SupplierPrecentage;

class OrdersSku extends Model
{
	protected $table = "orderssku";
	protected $primaryKey = "id";
	protected $fillable = ['orderId', 'orderNumber', 'title', 'goodsId', 'skuId', 'skuPropName', 'price', 'costPrice', 'discountFee', 'number', 'skuPrice', 'couponFee', 'type', 'brandId', 'brandName', 'pic'];
	public $timestamps = false;

    public function getOne($id)
    {
        return $this->select('orderNumber')->where('id', $id)->get()->toArray();
    }

    public function saveOne($param)
    {
    	return $this->create($param);
    }

    public function updateOne($param)
    {
        return $this->where('id', $param['id'])->update($param);
    }

    public function getSkuIdList($orderNumber)
    {
    	$res = $this->select('id', 'skuId', 'skuPropName', 'goodsId', 'title', 'type', 'brandId', 'brandName', 'pic', 'price', 'number', 'logistics', 'company')->where('orderNumber', $orderNumber)->get();
    	if ($res) {
            foreach ($res as &$v) {
                $v['pic'] = adminDomain().$v['pic'];
                $v['price'] *= 0.01;
                $v['skuPropName'] = json_decode($v['skuPropName']);
            }
            return $res->toArray();
        }
    }

    public function getSkuListItemId($orderNumber)
    {
        $res = $this->select('skuId')->where('orderNumber', $orderNumber)->get();
        if ($res) return $res->toArray();
    }

    public function TwoLatestLogistics($userId)
    {
        $res = $this->select('logistics', 'company')
            ->where(['userId'=>$userId])
            ->whereNotIn('logistics', ['0'])
            ->whereNotIn('company', ['0'])
            ->leftjoin('orders', 'orders.id', '=', 'ordersSku.orderId')
            ->orderBy('logisticsTime', 'desc')
            ->groupBy('logistics')
            ->limit(3)
            ->get()
            ->toArray();
        return $res;
    }

    //通过订单id数组获取每个下单商品的skuid和数量，用来订单过期时恢复库存
    public function getSkuIdAndNumberByArray($orderIdArray)
    {
        $array = [];
        foreach ($orderIdArray as $v) {
            $data = $this->select('skuId', 'number')->where('orderId', $v)->get()->toArray();
            foreach ($data as $value) {
                array_push($array, $value);
            }
        }
        return $array;   
    }

    //根据当前扣点调整订单商品里的成本价
    public function changePrice($array)
    {
        $res = $this->select('orderssku.id', 'orderssku.price', 'number', 'shopId', 'categoryName', 'categoryTwoName', 'orderssku.skuId', 'sku.costPrice')
            ->leftjoin('item', 'item.id', '=', 'orderssku.goodsId')
            ->leftjoin('sku', 'orderssku.skuId', '=', 'sku.id')
            ->leftjoin('orders', 'orders.id', '=', 'orderssku.orderId')
            ->whereIn('shopId', $array)
            ->orderby('orderssku.id', 'desc')
            ->get()
            ->toArray();
        foreach ($res as $v) {
            $param = [
                'catOneId'   => $v['categoryName'],
                'catTwoId'   => $v['categoryTwoName'],
                'supplierId' => $v['shopId'],
            ];
            $data = SupplierPrecentage::select('point')
                ->where($param)
                ->get()
                ->toArray();
            if (isset($data) && $data) {
                $save = [
                    'costPrice' => $v['price'] * (1 - $data[0]['point'] * 0.0001), 
                ];
            } else {
                $save = [
                    'costPrice' => $v['number'] * $v['costPrice'],
                ];
            }
            $result = $this->where(['id'=>$v['id']])->update($save);
            CrontabChangePrice::truncate();
        }
    }
}