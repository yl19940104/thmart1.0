<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class SupplierPrecentage extends Model
{
	protected $table = "supplierPrecentage";
	//指定此表主键,find方法直接传入主键值即可调用
	protected $primaryKey = "id";
	//必须指定参数白名单，否则create函数无法执行
	protected $fillable = ['catOneId', 'catTwoId', 'supplierId', 'point'];
	//为true表示记录添加时间和更新时间
	public $timestamps = false;

    public function addOne($array)
    {
        return $this->create($array);
    }

    public function getList($supplierId)
    {
    	return $this->select('catOneId', 'catTwoId', 'point')->where('supplierId', $supplierId)->get()->toArray();
    }

    public function getOne($supplierId, $catOneId, $catTwoId)
    {
        return $this->select('point')->where(['supplierId'=>$supplierId,'catOneId'=>$catOneId,'catTwoId'=>$catTwoId])->get()->toArray();
    }
}