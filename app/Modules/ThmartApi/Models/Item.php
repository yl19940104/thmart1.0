<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Modules\ThmartApi\Models\Sku;
/*use Illuminate\Contracts\Support\JsonableInterface;*/

class Item extends Model
{
	protected $table = "item";
	//指定此表主键,find方法直接传入主键值即可调用
	protected $primaryKey = "id";
	//必须指定参数白名单，否则create函数无法执行
	protected $fillable = ['shopId', 'type', 'categoryName', 'brandName', 'title', 'subTitle', 'enTitle', 'titleLink', 'detail', 'onLineTime', 'offLineTime', 'pic', 'detail', 'categoryTwoName', 'categoryThreeName', 'staff_id', 'createTime'];
	//为true表示记录添加时间和更新时间
	public $timestamps = false;

    public function getOne($id)
    {
        return $this->select('id', 'categoryName', 'title', 'brandName', 'shopId', 'type', 'pic')->find($id);
    }

    //判断此商品是否已被删除或下架
    public function getOneNotDeleteOrAudited($id)
    {
        return $this->select('id', 'shopId')->where(['isDelete'=>0, 'audited'=>1])->find($id);
    }

    public function getDetail($id)
    {
        $res = $this->select('item.id', 'title', 'detail', 'item.isDelete', 'item.pic', 'brand.id as brandId', 'brand.name as brandName', 'brand.pic as brandPic', 'type', 'categoryName', 'categoryTwoName')
            ->leftjoin('brand', 'item.brandName', '=', 'brand.id')
            ->find($id);
        if ($res) {
            $res = $res->toArray();
            $res['brand'] = ['id'=>$res['brandId'], 'name'=>$res['brandName'], 'pic'=>adminDomain().$res['brandPic']];
            unset($res['brandId']);
            unset($res['brandName']);
            unset($res['brandPic']);
            return $res;
        }
    }
    
    //$search为搜索字段，并利用了参数绑定，防止sql注入
    public function getMinSkulist($where, $order, $search=null, $limit=null)
    {
        $res =  DB::select("SELECT * FROM
                        (select itemId as id, price, sellNumber, item.title, createTime, item.pic
                        from item 
                        left join sku 
                        on item.id = sku.itemId
                        where sku.isDelete = 0 and item.isDelete = 0 and item.audited = 1 {$where} 
                        order by price asc) 
                        as a group by id order by {$order} {$limit}", [$search]);
        $res = json_encode($res);
        $res = json_decode($res, true);
        foreach ($res as &$v) {
            $v['price'] /= 100;
        }
        unset($v);
        //数组中批量加入最小促销(团购)价
        $res = (new ItemSalePrice)->addArrayMinSalePrice($res);
        return convertUrl($res);
    }

    public function addOne($array)
    {

        $array['createTime'] = time();
        $array['staff_id'] = session()->get('userInfo')['id'];
        return $this->create($array);
    }

    public function saveOne($array)
    {
        if (isset($array['picList'])) unset($array['picList']);
        return $this->where(['id'=>$array['id']])->update($array);
    }

    //如果添加完商品信息之后添加sku信息有错，那么删除添加的商品信息
    public function deleteOne($itemId)
    {
        DB::table('itemCaroPic')->where('itemId', $itemId)->delete();
        DB::table('item')->where('id', $itemId)->delete();
    }

    //获取团购商品列表
    public function getGroupBuyingList($order, $limit=null)
    {
        $time = time();
        //列表中的原价可能不是最低价，因为是按团购价排序，可以单独查一次改sku的最低原价并存入数组中
        $res = DB::select("SELECT * from
            (select itemSalePrice.salePrice as price,itemSalePrice.goodsId,item.pic,item.title,sku.price as originalPrice,item.sellNumber, item.id, item.createTime, itemSalePrice.startTime, itemSalePrice.endTime
                from itemSalePrice 
                LEFT join item
                on item.id = itemSalePrice.goodsId
                left join sku on sku.itemId = item.id
                where itemSalePrice.type = '2'
                and sku.isDelete = '0'
                and item.audited = '1'
                and startTime <= {$time}
                and endTime >= {$time}
                order by price asc
            )   
            as a
            group by goodsId "
            ."order by ".$order
        );
        $res = json_encode($res);
        $res = json_decode($res, true);
        foreach ($res as &$v) {
            $v['price'] *= 0.01;
            $v['originalPrice'] *= 0.01;
            $v['currentTime'] = time();
            $v['saleType']['type'] = 'group';
        }
        unset($v);
        return convertUrl($res);
    }

    //推荐商品
    public function recommendList($itemId=null, $categoryName=null, $categoryTwoName=null, $limit=null)
    {
        $where = '';
        $limit = $limit ? $limit : 5;
        if ($itemId) $where = 'and item.id !='.$itemId;
        if ($categoryName) $where .= ' and item.categoryName = '.$categoryName;
        if ($categoryTwoName) $where .= ' and item.categoryTwoName = '.$categoryTwoName;
        $res =  DB::select("SELECT * FROM 
                        (select itemId as id, price, item.title, item.pic 
                        from item 
                        left join sku 
                        on item.id = sku.itemId 
                        where sku.isDelete = 0
                        and item.isDelete = 0
                        and item.audited = 1 {$where}
                        order by price asc)  
                        as a group by id order by rand() limit {$limit}");
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

    //获取未删除且上架的商品id和title
    public function getItemIdTitleList()
    {
        $res = $this->select('id', 'title')->where(['audited'=>1, 'isDelete'=>0])->get()->toArray();
        return $res;
    }

    //判断分类下是否存在商品(未删除且上架)，如果该分类下无商品，则在首页不显示此分类
    public function findOne($id)
    {
        $res = $this->where(['categoryName'=>$id, 'isDelete'=>0, 'audited'=>1])->get()->toArray();
        if (isset($res['0']) && $res['0']) {
            return true;
        } else {
            return false;
        }
    }

    /*//获取最新上架的商品，首页hot products
    public function getHotProducts()
    {
        $res = $this->select('id', 'title')->where(['audited'=>1, 'isDelete'=>0])->get()->toArray();
        return $res;
    }*/
}