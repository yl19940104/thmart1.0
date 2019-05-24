<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class UserCollect extends Model
{
	protected $table = "userCollect";
	//指定此表主键,find方法直接传入主键值即可调用
	protected $primaryKey = "id";
	//必须指定参数白名单，否则create函数无法执行
	protected $fillable = ['userId', 'type', 'contentId', 'isCollect'];
	//为true表示记录添加时间和更新时间
	public $timestamps = false;

    public function saveOne($param)
    {
        return $this->create($param);
    }

    public function getOne($type, $contentId, $userId)
    {
        $res = $this->select('id', 'isCollect')->where(['type'=>$type, 'contentId'=>$contentId, 'userId'=>$userId])->get();
        if ($res) return $res->toArray();
    }
 
    //改变收藏状态
    public function updateOne($id, $isCollect)
    {
        return $this->where('id', $id)->update(['isCollect'=>$isCollect]);
    }

    public function getList($userId, $type, $pageSize)
    {
        $res = $this->select('contentId')->where(['userId'=>$userId, 'type'=>$type, 'isCollect'=>1])->orderBy('id', 'desc')->paginate($pageSize);
        if ($res) return $res->toArray();
    }

    public function getItemList($userId, $type)
    {
        $res = $this->select('contentId')->where(['userId'=>$userId, 'type'=>$type, 'isCollect'=>1])->orderBy('id', 'desc')->get();
        if ($res) return $res->toArray();
    }

    //获取用户收藏状态
    public function getStatus($type, $contentId, $userId)
    {
        $res = $this->select('id', 'isCollect')->where(['type'=>$type, 'contentId'=>$contentId, 'userId'=>$userId])->get()->toArray();
        if (!$res) {
            return 0;
        } else {
            return $res['0']['isCollect'];
        }
    }
}