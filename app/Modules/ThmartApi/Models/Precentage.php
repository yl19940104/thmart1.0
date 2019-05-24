<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Precentage extends Model
{
	protected $table = "precentage";
	//指定此表主键,find方法直接传入主键值即可调用
	protected $primaryKey = "id";
	//必须指定参数白名单，否则create函数无法执行
	/*protected $fillable = ['fname', 'title', 'isFinal', 'allowItem'];*/
	//为true表示记录添加时间和更新时间
	public $timestamps = false;

    //获取单条数据
    public function getOne($id)
    {
    	return $this->select('shopId')->find($id);
    }
}