<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
	protected $table = "article";
	//指定此表主键,find方法直接传入主键值即可调用
	protected $primaryKey = "id";
	//必须指定参数白名单，否则create函数无法执行
	protected $fillable = ['title', 'pic', 'cat_id', 'description', 'createTime'];
	//为true表示记录添加时间和更新时间
	public $timestamps = false;

    public function getOne($id)
    {
    	return $this->select('id')->find($id);
    }

    public function getDetail($id)
    {
        return $this->select('article.id as id', 'title', 'pic', 'description', 'article_content', 'createTime')
            ->join('articleContent', 'article.id', '=', 'article_id')
            ->where(['is_delete'=>'0'])
            ->find($id);
    }

    public function getList($pageSize, $order, $search=null)
    {
        $condition = [['is_delete', '=', '0']];
        if ($search) array_push($condition, ['title' , 'like', '%'.$search.'%']);
        return $this->select('id', 'title', 'pic', 'description', 'createTime')->where($condition)->orderBy($order['0'], $order['1'])->paginate($pageSize);
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