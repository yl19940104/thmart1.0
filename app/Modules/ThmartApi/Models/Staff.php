<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
	protected $table = "staff";
	//指定此表主键,find方法直接传入主键值即可调用
	protected $primaryKey = "id";
	//必须指定参数白名单，否则create函数无法执行
	protected $fillable = ['username', 'password', 'salt'];
	//为true表示记录添加时间和更新时间
	public $timestamps = true;

    public function getOne($username)
    {
    	$res = $this->select('username', 'password', 'salt', 'id')->where(['username'=>$username])->get();
    	if ($res) return $res->toArray();
    }

    public function getOneById($id)
    {
        $res = $this->select('username', 'password', 'salt')->where(['id'=>$id])->get();
        if ($res) return $res->toArray();
    }

    public function getOneByUsername($username)
    {
        $res = $this->select('id')->where(['username'=>$username])->get();
        if ($res) return $res->toArray();
    }

    public function getList($pageSize)
    {
    	$res = $this->select('username', 'id')->where('isDelete', 0)->paginate($pageSize);
    	if ($res) return $res->toArray();
    }

    public function saveOne($array)
    {
        return $this->where('id', $array['id'])->update($array);
    }

    public function addOne($array)
    {
        return $this->create($array);
    }

    //获取当前用户所有权限，存入数组中
    public function getAuthArray($username)
    {
        $array = [];
        $res = $this->select('auth')
            ->where('username', $username)
            ->leftjoin('staffInfoRole', 'staff.id', '=', 'staffInfoRole.staff_id')
            ->leftjoin('staffRoleAuth', 'staffInfoRole.role_id', '=', 'staffRoleAuth.role_id')
            ->leftjoin('staffAuth', 'staffRoleAuth.auth_id', '=', 'staffAuth.id')
            ->get()
            ->toArray();
        foreach ($res as $v) {
            array_push($array, $v['auth']);
        }
        return $array;
    }

    //获取当前用户所有角色，存入数组中
    public function getRoleArray($username)
    {
        $array = [];
        $res = $this->select('staffInfoRole.role_id')
            ->where('username', $username)
            ->leftjoin('staffInfoRole', 'staff.id', '=', 'staffInfoRole.staff_id')
            ->get()
            ->toArray();
        foreach ($res as $v) {
            array_push($array, $v['role_id']);
        }
        return $array;
    }
}