<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Modules\ThmartApi\Models\Sku;
use App\Modules\ThmartApi\Models\ItemSalePrice;
use App\Modules\ThmartApi\Models\CouponSku;

class Cart extends Model
{
	protected $table = "cart";
	
	protected $primaryKey = "id";
	
	protected $fillable = ['userId', 'goodsId', 'skuId', 'goodsName', 'number', 'brandId', 'brandName', 'shopId', 'type'];
	
	public $timestamps = false;

	public function getOne($skuId, $userId)
	{
		if ($res = $this->select('id')->where(['skuId'=>$skuId, 'userId'=>$userId])->get()) return $res->toArray();
	}

	public function getOneCart($id)
	{
		if ($res = $this->select('userId', 'skuId')->where(['id'=>$id])->get()) return $res->toArray();
	}

    //获取某一用户已选中的所有购物车商品
	public function getListSelected($userId)
	{
	    $res = $this->select('goodsId', 'skuId', 'goodsName', 'number', 'brandId', 'cart.brandName', 'cart.shopId', 'cart.type')
            ->leftjoin('item', 'item.id', 'cart.goodsId')
            ->where(['userId'=>$userId, 'isSelect'=>1, 'audited'=>1, 'isDelete'=>0])
            ->get();
		if ($res) return $res->toArray();
	}

	public function addOne($param)
	{
		return $this->create($param);
	}

    //购物车已有此商品,再添此商品，添加数量
	public function saveOne($param)
	{
		return $this->where(['id'=>$param['id']])->increment('number', $param['number']);
	}

    //修改购物车的一条商品数量记录
	public function saveOneNumber($param)
	{
		return $this->where(['id'=>$param['id']])->update($param);
	}

	public function getList($userId)
	{
		if ($res = $this->select('id', 'goodsId', 'skuId', 'goodsName', 'number', 'brandName', 'brandId', 'isSelect')->where(['userId'=>$userId])->get()) return $res->toArray();
	}

    //通过cartid获取skuid列表
	public function getSkuIdList($cartIdArray)
	{
		if ($res = $this->select('skuId')->whereIn('id', $cartIdArray)->get()) return $res->toArray();
	}

	public function deleteList($array)
	{
		return $this->destroy($array);
	}

    //勾选或取消勾选某一用户所有购物车商品
	public function changeSelectAll($userId, $isSelect)
	{
	    $data['isSelect'] = $isSelect;
	    return $this->where(['userId'=>$userId])->update($data);
	}

	//勾选或取消勾选某一用户购物车某一件商品
	public function changeSelectArray($isSelect, $cartIdArray)
	{
	    $data['isSelect'] = $isSelect;
	    return $this->whereIn('id', $cartIdArray)->update($data);
	}

    /*
     * 计算所有购物车选中的sku的价格数组
     * 形式:[[$skuId]=>[$price], [$skuId]=>[$price]]
     */
	public function getSelectPriceArray($skuIdArray)
    {
        $total = null;
        $res = (new Sku)->select('price', 'id')->whereIn('id', $skuIdArray)->get();
        if ($res) $res = $res->toArray();
        foreach ($res as $v) {
        	$minSkuSale = (new ItemSalePrice)->getMinSkuSale($v['id']);
        	if ($minSkuSale) {
        		$array[$v['id']] = $minSkuSale;
        	} else {
                $array[$v['id']] = 0.01 * $v['price'];        		
        	}
        }
        if (isset($array)) return $array;
    }

    //判断购物车每个品牌下的商品是否被全选
    public function getBrandSelect($brandId, $userId)
    {
        if ($res = $this->select('isSelect')->where(['userId'=>$userId, 'brandId'=>$brandId])->get()) $res->toArray();
        $data = true;
        foreach ($res as $v) {
        	if ($v['isSelect'] == 0) $data = false;
        }
        return $data;
    }

    //清除该用户购物车里所有被选中的商品
    public function deleteSelect($userId)
    {
        return $this->where(['userId'=>$userId, 'isSelect'=>1])->delete();
    }

    //获取购物车选中商品的总价以及满减价格
    public function getTotal($userId)
    {
    	//获取用户购物车中所有选中商品
		$res = (new Cart)->getListSelected($userId);
		$skuIdArray = [];
		$skuSelectedNumber = [];
        foreach ($res as $v) {
        	//如果该sku没有被删除,则该sku被记录到数组中进行后续计算
        	if ((new Sku)->getOneUnDelete($v['skuId'])) {
	        	array_push($skuIdArray, $v['skuId']);
	        	//$skuSelectedNumber形式：[[$skuId]=>[$number], [$skuId]=>[$number]]
	        	$skuSelectedNumber[$v['skuId']] = $v['number'];
	        }
        }
        //res形式:[[$skuId]=>[$price], [$skuId]=>[$price]],之后与$skuSelectedNumber遍历相乘获取购物车所有选中商品的总价
        $res = objectToArray((new Cart)->getSelectPriceArray($skuIdArray));
        $total = 0;
        $reduceTotal = 0;
        if (isset($res)) {
            foreach ($res as $key => $value) {
	            $total += $res[$key]*$skuSelectedNumber[$key];
	        }
        }
        foreach ($skuIdArray as $v) {
        	//获取该sku的最低促销(团购)价类型(促销或团购)
            $skuMinPriceType = (new ItemSalePrice)->getSkuMinPriceType($v);
            //如果这个sku没有团购价,那么判断他是否在满减池里,如果在满减池,则记录起来
            if (!$skuMinPriceType || $skuMinPriceType['0']['type'] != 2) {
                $couponList = (new couponSku)->getCouponTypeOverReduce($v, 2);
                if ($couponList) {
                    //$overReduceArray[$couponList['0']['id']]形式为[['over'=>$over, 'totalPrice'=>totalPrice], ['over'=>$over, 'totalPrice'=>totalPrice]], 这个数组记录购物车里商品所在的满减池以及对应的在此满减池里的所有购物车商品的总价，满减池最低满减金额，得出结果与总价相运算
                    if (!isset($overReduceArray[$couponList['0']['id']]['over'])) $overReduceArray[$couponList['0']['id']]['over'] = $couponList['0']['over'];
                    if (!isset($overReduceArray[$couponList['0']['id']]['totalPrice'])) $overReduceArray[$couponList['0']['id']]['totalPrice'] = 0;
                    $overReduceArray[$couponList['0']['id']]['totalPrice'] += $skuSelectedNumber[$v] * $res[$v];
                    if (!isset($overReduceArray[$couponList['0']['id']]['reduce'])) $overReduceArray[$couponList['0']['id']]['reduce'] = $couponList['0']['reduce'];
                }
            }
        }
        //如果满减符合条件，那么便利数组减去满减金额
        if (isset($overReduceArray)) {
            foreach ($overReduceArray as $v) {
                if ($v['over'] <= $v['totalPrice']) {
                	$total -= $v['reduce'];
                	$reduceTotal += $v['reduce'];
                }
            }
        }
        return ['total'=>$total, 'reduceTotal'=>$reduceTotal];
    }
}