<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class StaffRole extends Model
{
	protected $table = "staffRole";
	protected $primaryKey = "id";
	protected $fillable = ['roleName'];
	public $timestamps = false;

    public function getOne($id)
    {
    	$res = $this->select('id', 'roleName')->where(['id'=>$id])->get();
    	if ($res) return $res->toArray();
    }

    public function getOneByRoleName($roleName)
    {
        $res = $this->select('roleName')->where(['roleName'=>$roleName])->get();
        if ($res) return $res->toArray();
    }

    public function saveOne($array)
    {
        return $this->create($array);
    }

    public function updateOne($array)
    {
        return $this->where('id', $array['id'])->update($array);
    }

    public function getList($pageSize)
    {
    	$res = $this->select('roleName', 'id')->where('isDelete', 0)->paginate($pageSize);
    	if ($res) return $res->toArray();
    }
}