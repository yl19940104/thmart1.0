<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class SkuPropValue extends Model
{
	protected $table = "skupropvalue";
	//指定此表主键,find方法直接传入主键值即可调用了
	/*protected $primaryKey = "id";*/
	//必须指定参数白名单，否则create函数无法执行
	protected $fillable = ['skuId', 'propTemplateId', 'name', 'orderby', 'value'];
	//为true表示记录添加时间和更新时间
	public $timestamps = false;

    public function deleteArray($skuIdArray)
    {
        return $this->whereIn('skuId', $skuIdArray)->delete();
    }

    public function saveArray($array)
    {
        return $this->create($array);
    }
}