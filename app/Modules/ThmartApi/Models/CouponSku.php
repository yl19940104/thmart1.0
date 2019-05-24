<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CouponSku extends Model
{
	protected $table = "couponSku";

	protected $primaryKey = "id";

	protected $fillable = ['couponId', 'skuNumber', 'title', 'skuId'];

    public $timestamps = false;

    public function addOne($param)
    {
    	return $this->create($param);
    }

    public function getOne($id)
    {
    	return $this->select('id')->where(['id'=>$id])->get();
    }

    public function saveOne($param)
    {
    	return $this->where(['id'=>$param['id']])->update($param);
    }

    public function getList($couponId)
    {
    	if ($res = $this->where(['couponId'=>$couponId])->get()) return $res->toArray();
    }

    public function getItemIdList($couponId)
    {
        $res = (new CouponSku)->select('item.id', 'item.pic', 'item.title')->distinct('sku.itemId')
            ->leftjoin('sku', 'sku.id', '=', 'couponSku.skuId')
            ->leftjoin('item', 'sku.itemId', '=', 'item.id')
            ->where(['couponId'=>$couponId, 'sku.isDelete'=>0])
            ->get();
        if ($res) return $res->toArray();
    }

    public function getListBySkuNumberAndType($skuList, $type)
    {
        if ($res = $this->select('isOverlay', 'skuNumber', 'couponId')->leftJoin('coupon', 'couponSku.couponId', '=', 'coupon.id')->where(['type'=>$type])->whereIn('skuNumber', $skuList)->get()) return $res->toArray();
    }

    //获取一个skuId的优惠类型(优惠券或满减)，满，减，优惠活动id
    public function getCouponTypeOverReduce($skuId, $type)
    {
        $res = $this->select('coupon.type', 'over', 'reduce', 'coupon.id')
            ->where(['couponSku.skuId'=>$skuId, 'type'=>$type, ['startTime', '<=', time()], ['endTime', '>=', time()]])
            ->leftJoin('coupon', 'couponSku.couponId', '=', 'coupon.id')
            ->orderBy('over', 'desc')
            ->get();
        if ($res) return $res->toArray();
    }

    //获取一组skuId的优惠类型(优惠券或满减)，满，减，优惠活动id
    public function getCouponTypeOverReduceByArray($skuIdArray, $type)
    {
        $res = $this->select('coupon.type', 'over', 'reduce', 'coupon.id', 'skuId', 'name')
            ->where([/*'couponSku.skuId'=>$skuId, */'type'=>$type, ['startTime', '<=', time()], ['endTime', '>=', time()]])
            ->whereIn('couponSku.skuId', $skuIdArray)
            ->leftJoin('coupon', 'couponSku.couponId', '=', 'coupon.id')
            ->orderBy('over', 'desc')
            ->get();
        if ($res) return $res->toArray();
    }

    public function deleteList($couponId)
    {
    	return $this->where(['couponId'=>$couponId])->delete();
    }

    public function deleteSku($idArray)
    {
        foreach ($idArray as $v) {
            $this->where(['id'=>$v])->delete();
        }
    }
}