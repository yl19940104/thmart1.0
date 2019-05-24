<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class StaffSupplier extends Model
{
	protected $table = "staff_supplier";
	//指定此表主键,find方法直接传入主键值即可调用
	protected $primaryKey = "id";
	//必须指定参数白名单，否则create函数无法执行
	protected $fillable = ['staff_id', 'supplier_id'];
    //为true表示记录添加时间和更新时间
    public $timestamps = false;

    public function addOne($array)
    {
    	return $this->create($array);
    }

    public function deleteStaffList($staff_id)
    {
        $this->where('staff_id', $staff_id)->delete();
    }

    public function staffSupplierList($staff_id)
    {
        return $this->select('supplier_id as id')->where('staff_id', $staff_id)->get()->toArray();
    }

    public function staffSupplierNameList($staff_id)
    {
        return $this->select('supplier.supplier_name')
            ->where('staff_supplier.staff_id', $staff_id)
            ->leftjoin('supplier', 'supplier.id', '=', 'staff_supplier.supplier_id')
            ->get()
            ->toArray();
    }
}