<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class ItemPropValue extends Model
{
	protected $table = "itempropvalue";
	//必须指定参数白名单，否则create函数无法执行
	protected $fillable = ['itemId', 'propTemplateId', 'orderby', 'value', 'name'];
	//为true表示记录添加时间和更新时间
	public $timestamps = false;

    public function getOne($itemId, $propTemplateId)
    {
        return $this->select('itemId')->where(['itemId'=>$itemId, 'propTemplateId'=>$propTemplateId])->first();
    }

    public function addOne($array, $name)
    {
    	$data = $array;
    	$data['name'] = $name;
        return $this->create($data);
    }

    public function saveOne($array, $name)
    {
    	$data = $array;
    	$data['name'] = $name;
        return $this->where(['itemId'=>$data['itemId'], 'propTemplateId'=>$data['propTemplateId']])->update($data);
    }

    public function deleteOne($itemId, $propTemplateId)
    {
        return $this->where(['itemId'=>$itemId, 'propTemplateId'=>$propTemplateId])->delete();
    }
}