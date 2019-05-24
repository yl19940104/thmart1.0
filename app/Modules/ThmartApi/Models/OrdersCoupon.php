<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class OrdersCoupon extends Model
{
	protected $table = "ordersCoupon";

	protected $primaryKey = "id";

	protected $fillable = ['couponUserId', 'couponId', 'orderNumber', 'fee'];

    public $timestamps = false;

    public function addOne($param)
    {
    	return $this->create($param);
    }

    public function getOne($orderNumber)
    {
    	$res = $this->select('couponId')->where('orderNumber', $orderNumber)->get();
        if ($res) return $res->toArray();
    }
}