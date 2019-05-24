<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
	protected $table = "brand";
	//指定此表主键,find方法直接传入主键值即可调用
	protected $primaryKey = "id";
	//必须指定参数白名单，否则create函数无法执行
	protected $fillable = ['name', 'name_cn', 'pic', 'status'];
    //为true表示记录添加时间和更新时间
    public $timestamps = false;

    public function getOne($id)
    {
    	return $this->select('id')->where(['isDelete'=>'0'])->find($id);
    }

    public function getDetail($id)
    {
        return $this->select('id', 'name', 'pic')->where(['isDelete'=>'0'])->find($id);
    }

    public function getList($pageSize)
    {
        return $this->select('id', 'name', 'pic', 'templet')->where(['isDelete'=>'0'])->orderby('sort', 'asc')->paginate($pageSize)->toArray();
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