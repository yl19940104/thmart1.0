<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class ArticleItem extends Model
{
	protected $table = "articleItem";
	//指定此表主键,find方法直接传入主键值即可调用
	protected $primaryKey = "id";
	//必须指定参数白名单，否则create函数无法执行
	protected $fillable = ['itemId'];
	//为true表示记录添加时间和更新时间
	public $timestamps = false;

    public function addOne($array)
    {
    	return $this->create($array);
    }

    public function deleteList($id)
    {
        return $this->where(['articleId'=>$id])->delete();
    }

    public function insertList($array)
    {
        return $this->insert($array);
    }

    public function getList($id)
    {
        $res =  DB::select("SELECT * FROM 
                        (select articleItem.itemId as id, price, item.title, item.pic 
                        from articleItem 
                        left join item
                        on item.id = articleItem.itemId 
                        left join sku 
                        on articleItem.itemId = sku.itemId 
                        where articleItem.articleId =".$id."
                        and sku.isDelete = 0
                        and item.isDelete = 0
                        and item.audited = 1
                        order by price asc)  
                        as a group by id order by rand() limit 5");
        $res = json_encode($res);
        $res = json_decode($res, true);
        foreach ($res as &$v) {
            $v['price'] *= 0.01;
        }
        unset($v);
        //数组中批量加入最小促销(团购)价
        $res = (new ItemSalePrice)->addArrayMinSalePrice($res);
        return convertUrl($res);
    }
}