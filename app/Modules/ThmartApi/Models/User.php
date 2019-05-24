<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
	protected $table = "user";
	//指定此表主键,find方法直接传入主键值即可调用
	protected $primaryKey = "id";
	//必须指定参数白名单，否则create函数无法执行
	protected $fillable = ['password', 'salt', 'createTime', 'headimgurl'];
	//为true表示记录添加时间和更新时间
	public $timestamps = false;

    public function getOne($id)
    {
    	return $this->select('id', 'password', 'salt', 'headimgurl')->find($id);
    }

    public function addOne($param)
    {
        return $this->create($param);
    }

    //更新登录信息
    public function updateLogin($array)
    {
    	return $this->where(['id'=>$array['id']])->update($array);
    }

    public function resetPassword($array)
    {
        return $this->where(['id'=>$array['id']])->update($array);
    }

    public function saveOne($array)
    {
        return $this->where(['id'=>$array['id']])->update($array);
    }
}