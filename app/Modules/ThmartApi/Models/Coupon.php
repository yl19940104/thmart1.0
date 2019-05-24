<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
	protected $table = "coupon";

	protected $primaryKey = "id";

	protected $fillable = ['startTime', 'endTime', 'over', 'reduce', 'type', 'isOverlay', 'amount', 'name', 'pic'];

    public $timestamps = false;

    public function addOne($param)
    {
    	return $this->create($param);
    }

    public function getOne($id)
    {
    	$res = $this->select('id', 'reduce', 'type')->where('id', $id)->get();
        if ($res) return $res->toArray();
    }

    public function getOneCoupon($id)
    {
        $res = $this->select('id', 'reduce')->where(['id'=>$id, 'type'=>1, ['startTime', '<=', time()], ['endTime', '>=', time()]])->get();
        if ($res) return $res->toArray();
    }

    public function saveOne($array)
    {
        return $this->where(['id'=>$array['id']])->update($array);
    }
}