<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Modules\ThmartApi\Models\ItemSalePrice;
use App\Modules\ThmartApi\Models\CouponUser;

class Sku extends Model
{
	protected $table = "sku";
	//指定此表主键,find方法直接传入主键值即可调用
	protected $primaryKey = "id";
	//必须指定参数白名单，否则create函数无法执行
	/*protected $fillable = ['fname', 'title', 'isFinal', 'allowItem'];*/
	//为true表示记录添加时间和更新时间
	public $timestamps = false;

    public function getOne($id)
    {
        return $this->select('id', 'itemId', 'stock', 'pic')->find($id);
    }

    //获取某个sku的信息，商品详情页直接下单使用
    public function getOneToOrder($skuId)
    {
        $res = $this->select('sku.id as skuId', 'itemId as goodsId', 'title as goodsName', 'brandName as brandId', 'brand.name as brandName', 'shopId', 'stock', 'item.type')
            ->leftJoin('item', 'item.id', '=', 'sku.itemId')
            ->leftJoin('brand', 'item.brandName', '=', 'brand.id')
            ->where('sku.id', $skuId)
            ->get();
        if ($res) return $res->toArray();
    }

    public function getOneUnDelete($id)
    {
        return $this->select('id', 'itemId')->where(['isDelete'=>0])->find($id);
    }

    //通过skuNumber获取单条信息
    public function getOneBySkuNumber($skuNumber)
    {
        if ($res = $this->select('itemId', 'price', 'id')->where(['skuNumber'=>$skuNumber, 'isDelete'=>0])->get()) return $res->toArray();
    }

    //通过skuNumber获取商品title
    public function getTitleBySkuNumber($skuNumber)
    {
        if ($res = $this->select('item.title', 'sku.id')->leftJoin('item', 'sku.itemId', '=', 'item.id')->where(['skuNumber'=>$skuNumber, 'sku.isDelete'=>0])->get()) return $res->toArray();
    }

    public function getDetail($id, $isSpell=null)
    {
        $res = $this->select('sku.id', 'sku.price', 'sku.pic', 'sku.stock', 'sku.propName', 'sku.itemId', 'sku.costPrice', 'supplierPrecentage.point')
            ->leftjoin('item', 'item.id', '=', 'sku.itemId')
            ->leftjoin('supplierPrecentage', function ($join) {
                $join->on('supplierPrecentage.supplierId', '=', 'item.shopId')
                    ->on('supplierPrecentage.catTwoId', '=', 'item.categoryTwoName')
                    ->on('supplierPrecentage.catOneId', '=', 'item.categoryName');
            })
            ->find($id)
            ->toArray();
        $res['price'] = $res['price']/100;
        $res['costPrice'] = $res['costPrice']/100;
        $sale = (new ItemSalePrice)->getMinSkuSale($id);
        $data =  (new ItemSalePrice)->select('salePrice', 'type')
            ->where(['skuId'=>$id, ['startTime', '<=', time()], ['endTime', '>=', time()]])
            ->get()
            ->toArray();
        /*returnJson(1, ($data['0']['type']));*/
        if ($sale && ($data['0']['type'] != 3 || (isset($isSpell) && $isSpell))) {
            $res['originalPrice'] = $res['price'];
            $res['price'] = $sale;
        } else {
            $res['spellPrice'] = $sale;
        }
        if (!$sale) {
            $res['type'] = 'none';
        } else {
            if ($data = DB::table('itemSalePrice')->select('id', 'endTime')->where(['skuId'=>$id, 'type'=>2, ['endTime', '>=', time()], ['startTime', '<=', time()]])->get()->toArray()) {
                $res['endTime'] = $data['0']->endTime;
                $res['currentTime'] = time();
                $res['type'] = 'group';
            } elseif ($data = DB::table('itemSalePrice')->select('id', 'endTime')->where(['skuId'=>$id, 'type'=>3, ['endTime', '>=', time()], ['startTime', '<=', time()]])->get()->toArray()) {
                $res['endTime'] = $data['0']->endTime;
                $res['currentTime'] = time();
                $res['type'] = 'spell';
            } else {
                $res['type'] = 'sale';
            }
        }
        return $res;
    }

    public function getDetailNotDelete($id)
    {
        $res = $this->select('id', 'price', 'pic', 'stock', 'propName')->where(['isDelete'=>0])->find($id);
        if ($res) {
            $res = $res->toArray();
            $res['price'] = $res['price']/100;
            return $res;
        }
    }

    public function getList($itemId)
    {
        return $this->select('id', 'propName')->where(['itemId'=>$itemId, 'isDelete'=>0])->get()->toArray();
    }
    
    /*
     * 获取某商品所有sku的最低价
     * $withoutTypeThree如果传值,则说明获取排除商品拼单价的最低价
     */
    public function getMinPrice($itemId, $withoutTypeThree=null)
    {
        $res = $this->where(['itemId'=>$itemId, 'isDelete'=>0])->min('price');
        //这里只是简单的返回了最小原价，之后优化的话会加入促销价
        $res /= 100;
        if ($withoutTypeThree) return $res;
        $minGroupPrice = (new ItemSalePrice)->getMinItemSale($itemId);
        //如果有促销或团购价最低价，并且低于原价，那么返回促销（团购价）
        if ($minGroupPrice && $minGroupPrice < $res) $res = $minGroupPrice;
        return $res;
    }

    //获取某商品所有sku的最高价,此处判断商品的sku是否全部有促销价
    public function getMaxPrice($itemId, $withoutTypeThree=null)
    {
        $skuIdArray = [];
        $skuSaleIdArray = [];
        $maxSalePrice = (new ItemSalePrice)->getMaxItemSale($itemId);
        $data2 = DB::table('itemSalePrice')->select('skuId', 'salePrice')->where(['goodsId'=>$itemId, ['startTime', '<=', time()], ['endTime', '>=', time()]])->orderby('salePrice', 'desc')->get()->toArray();
        $data2 = objectToArray($data2);
        foreach ($data2 as $v) {
            array_push($skuSaleIdArray, $v['skuId']);
        }
        $data1 = $this->select('id', 'price')->where(['itemId'=>$itemId, 'isDelete'=>0])->orderby('price', 'desc')->get()->toArray();
        $data1 = objectToArray($data1);
        if (isset($withoutTypeThree) && $withoutTypeThree) {
            return $data1['0']['price'] / 100;
        }
        foreach ($data1 as $k => $v) {
            if (in_array($v['id'], $skuSaleIdArray)) {
                unset($data1[$k]);                
            }
        }
        //如果商品只有原价
        if (isset($data1['0']) && !isset($data2['0']) || (isset($withoutTypeThree) && $withoutTypeThree)) {
            $res = $data1['0']['price'] / 100;
        //如果商品有原价也有促销价
        } elseif (isset($data1['0']) && isset($data2['0'])) {
            $res = $data1['0']['price'] < $data2['0']['salePrice'] ? $res = $data2['0']['salePrice'] / 100 : $res = $data1['0']['price'] / 100;
        //如果商品只有促销价
        } else {
            $res = $data2['0']['salePrice'] / 100;
        }
        return $res;
    }

    //获取某商品所有sku的总库存
    public function getSumStock($itemId)
    {
        return $this->where(['itemId'=>$itemId, 'isDelete'=>0])->sum('stock');
    }

    public function saveOne($array)
    {
        return $this->where(['id'=>$array['id']])->update($array);
    }

    //删除该商品没有提交的sku信息,用于更新商品sku信息
    public function deleteArray($skuIdArray)
    {
        return $this->whereIn('id', $skuIdArray)->update(['isDelete'=>1]);
    }

    //保存该商品提交的sku信息,用于更新商品sku信息
    public function saveAll($updateskus)
    {
        foreach ($updateskus as &$v) {
            $v['price'] *= 100;
            $v['costPrice'] *= 100;
            $this->where('id', $v['id'])->update($v);
        }
    }

    //修改sku库存
    public function editSkuStock($skuId, $number)
    {
        $this->where('id', $skuId)->update(['stock'=>$number]);
    }

    public function getSkuList($itemId)
    {
        return $this->where(['itemId'=>$itemId])->get()->toArray();
    } 

    //获取商品所涉及的所有满减活动信息,商品详情页用
    public function getOverReduceList($itemId)
    {
        $res = $this->select('coupon.over', 'coupon.reduce', 'couponSku.couponId', 'coupon.name')
                ->leftjoin('couponSku', 'sku.id', '=', 'couponSku.skuId') 
                ->leftjoin('coupon', 'couponSku.couponId', '=', 'coupon.id')
                ->where([['coupon.startTime', '<=', time()], ['coupon.endTime', '>=', time()], 'coupon.type'=>2, 'sku.itemId'=>$itemId, 'sku.isDelete'=>0])
                ->orderby('over', 'desc')
                ->get()
                ->toArray();
        return $res;
    }

    //获取商品所涉及的所有优惠券活动信息,商品详情页用
    public function getCouponList($itemId, $userId=null)
    {
        $res = $this->select('coupon.over', 'coupon.reduce', 'couponSku.couponId', 'coupon.pic', 'coupon.name', 'startTime', 'endTime')
                ->leftjoin('couponSku', 'sku.id', '=', 'couponSku.skuId') 
                ->leftjoin('coupon', 'couponSku.couponId', '=', 'coupon.id')
                ->where([['coupon.startTime', '<=', time()], ['coupon.endTime', '>=', time()], 'coupon.type'=>1, 'sku.itemId'=>$itemId, 'sku.isDelete'=>0])
                ->orderby('over', 'desc')
                ->distinct('couponSku.couponId')
                ->get()
                ->toArray();
        $res = convertUrl($res);
        foreach ($res as $key => $value) {
            $res[$key]['isUsed'] = 0;
            $res[$key]['startTime'] = date('Y.m.d', $res[$key]['startTime']);
            $res[$key]['endTime'] = date('Y.m.d', $res[$key]['endTime']);
        }
        if ($userId) $res = (new CouponUser)->couponListIsUsed($res, $userId);
        return $res;
    }

    //订单过期恢复sku库存
    public function recoverStockArray($array)
    {
        foreach ($array as $v) {
            $res = $this->select('stock')->where('id', $v['skuId'])->get()->toArray();
            $number = $res['0']['stock'] + $v['number'];
            $this->where('id', $v['skuId'])->update(['stock'=>$number]);
        }
    }

    //扣除sku库存
    public function reduceStock($array)
    {
        $this->where('id', $array['id'])->update($array);
    }

    //查询某商品是否有sku(用于提交商品编辑时)
    public function selectItemSku($itemId)
    {
        return $this->select('id')->where('itemId', $itemId)->get()->toArray();
    }
}