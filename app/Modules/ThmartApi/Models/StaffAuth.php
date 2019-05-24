<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class StaffAuth extends Model
{
	protected $table = "staffAuth";
	protected $primaryKey = "id";
	protected $fillable = ['authName', 'auth'];
	public $timestamps = false;

    public function getOne($id)
    {
    	$res = $this->select('id', 'authName', 'auth')->where(['id'=>$id])->get();
    	if ($res) return $res->toArray();
    }

    public function getOneByAuth($auth)
    {
        $res = $this->select('id')->where(['auth'=>$auth])->get();
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
    	$res = $this->select('authName', 'auth', 'id')->where('isDelete', 0)->paginate($pageSize);
    	if ($res) return $res->toArray();
    }
}