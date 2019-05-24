<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class AdsPosition extends Model
{
	protected $table = "adsPosition";
	//指定此表主键,find方法直接传入主键值即可调用
	protected $primaryKey = "id";
	//必须指定参数白名单，否则create函数无法执行
	protected $fillable = ['name', 'status', 'type'];
	//为true表示记录添加时间和更新时间
	public $timestamps = false;

    public function getOne($name)
    {
    	return $this->select('id')->where(['name'=>$name])->get()->toArray();
    }

    public function getOneById($id)
    {
        return $this->select('id', 'status', 'name', 'type')->find($id);
    }

    public function addOne($array)
    {
    	return $this->create($array);
    }

    public function saveOne($array)
    {
    	return $this->where(['id'=>$array['id']])->update($array);
    }
}