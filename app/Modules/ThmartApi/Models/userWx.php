<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class UserWx extends Model
{
	protected $table = "userWx";
	//指定此表主键,find方法直接传入主键值即可调用
	/*protected $primaryKey = "id";*/
	//必须指定参数白名单，否则create函数无法执行
	protected $fillable = ['userId', 'openid', 'nickname', 'sex', 'city', 'province', 'country', 'unionid'];
	//为true表示记录添加时间和更新时间
	public $timestamps = false;


    public function addOne($array)
    {
    	return $this->create($array);
    }

    public function getOne($userId)
    {
    	return $this->select('userId')->where('userId', $userId)->get();
    }
}