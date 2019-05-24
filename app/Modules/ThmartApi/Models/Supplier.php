<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
	protected $table = "supplier";
	//指定此表主键,find方法直接传入主键值即可调用
	protected $primaryKey = "id";
	//必须指定参数白名单，否则create函数无法执行
	protected $fillable = ['supplier_name', 'contacts_name', 'contacts_phone', 'contacts_email', 'contacts_address', 'param', 'staff_id', 'number', 'sale', 'remark'];
	//为true表示记录添加时间和更新时间
	public $timestamps = false;

    public function addOne($array)
    {
        return $this->create($array);
    }

    public function saveOne($array)
    {
    	return $this->where('id', $array['id'])->update($array);
    }

    public function getOne($id)
    {
        return $this->select('id')->find($id);
    }
}