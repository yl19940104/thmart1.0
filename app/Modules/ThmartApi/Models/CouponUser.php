<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Modules\ThmartApi\Models\Coupon;

class CouponUser extends Model
{
	protected $table = "couponUser";

	protected $primaryKey = "id";

	protected $fillable = ['userId', 'couponId', 'getTime'];

    public $timestamps = false;

    //获取当前时间内有效的用户领取的优惠券列表信息
    public function getList($userId, $isUsed)
    {
    	$res = $this->select('couponId', 'pic')
    		->where(['userId'=>$userId, ['startTime', '<=', time()], ['endTime', '>=', time()], 'isUsed'=>$isUsed])
    		->leftJoin('coupon', 'couponId', '=', 'coupon.id')
    		->get();
        if ($res) return $res->toArray();
    }

    //分页获取当前时间内有效的用户领取的优惠券列表信息
    public function getListPage($userId, $isUsed, $pageSize)
    {
        $res = $this->select('couponId', 'pic')
            ->where(['userId'=>$userId, ['startTime', '<=', time()], ['endTime', '>=', time()], 'isUsed'=>$isUsed])
            ->leftJoin('coupon', 'couponId', '=', 'coupon.id')
            ->paginate($pageSize);
        if ($res) return $res->toArray();
    }

    public function getOne($userId, $couponId)
    {
        return $this->select('id', 'isUsed')
            ->where(['userId'=>$userId, 'couponId'=>$couponId])
            ->get()
            ->toArray();
    }

    //记录用户已使用过此优惠券
    public function saveOne($userId, $couponId)
    {
        return $this->select('id')
            ->where(['userId'=>$userId, 'couponId'=>$couponId])
            ->update(['isUsed'=>1]);
    }

    //记录用户领过此优惠券
    public function saveRecord($array)
    {
        $this->create($array);
        $res = Coupon::select('amount')->where(['id'=>$array['couponId']])->get()->toArray();
        $amount = $res['0']['amount'] - 1;
        if ($amount < 0) returnJson(0, '该优惠券库存为0');
        Coupon::where(['id'=>$array['couponId']])->update(['amount'=>$amount]);
    }

    //优惠券列表里面，表示用户是否领取过这些优惠券，在商品详情页优惠券列表以及独立领优惠券列表页里使用
    public function couponListIsUsed($couponList, $userId)
    {
        foreach ($couponList as &$v) {
            $res = $this->select('isUsed')->where(['couponId'=>$v['couponId'], 'userId'=>$userId])->get()->toArray();
            if ($res) {
                $v['isGet'] = 1;
            } else {
                $v['isGet'] = 0;
            }
        }
        return $couponList;
    }
}