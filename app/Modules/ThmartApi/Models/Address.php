<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
	protected $table = "userAddress";
	//指定此表主键,find方法直接传入主键值即可调用
	protected $primaryKey = "id";
	//必须指定参数白名单，否则create函数无法执行
	protected $fillable = ['userId', 'fullName', 'phone', 'isDefault', 'regionDetail', 'isDelete', 'province', 'city', 'email'];
	//为true表示记录添加时间和更新时间
	public $timestamps = true;

    protected function getDateFormat()
    {
        return time();
    }

    public function addOne($array)
    {
        return $this->insert($array);
    }

    public function getDetail($id)
    {
        $res = $this->select('userId', 'fullName', 'phone', 'province', 'city', 'regionDetail', 'email')->find($id);
        if ($res) return $res->toArray();
    }

    public function saveOne($array)
    {
        return $this->where(['id'=>$array['id']])->update($array);
    }

    //查询默认地址
    public function findDefault($userId)
    {
        $res = $this->select('regionDetail', 'province', 'city', 'phone', 'fullname', 'id', 'email')->where(['userId'=>$userId, 'isDefault'=>1])->first();
        if ($res) return $res->toArray();
    }

    //查询用户是否拥有过地址
    public function getOne($userId)
    {
        if ($res = $this->select('id')->where(['userId'=>$userId, 'isDelete'=>0])->first()) return $res->toArray();
    }

    //获取地址列表
    public function getList($userId, $pageSize)
    {
        return $this->select('id', 'fullName', 'phone', 'isDefault', 'regionDetail', 'email', 'province', 'city')->where(['userId'=>$userId, 'isDelete'=>0])->orderBy('isDefault', 'desc')->orderBy('updated_at', 'desc')->paginate($pageSize);
    }

    //获取一条地址信息
    public function getOneAddress($id)
    {
        if ($res = $this->select('id', 'isDefault', 'fullName', 'phone', 'id', 'email', 'regionDetail', 'province', 'city')->find($id)) return $res->toArray();
    }

    //如果删除或取消一条默认地址，则把用户最新更新的地址设为默认地址
      public static function setDefault($userId)
    {
        $res = DB::select("SELECT * FROM (select * from userAddress where userId = {$userId} and isDelete = 0 and isDefault = 0 order by updated_at desc) as a GROUP BY a.userId");
        if ($res) {
            $res = objectToArray($res);
            return Address::where(['id'=>$res['0']['id']])->update(['isDefault'=>1]);
        }
    }
}   