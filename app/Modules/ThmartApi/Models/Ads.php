<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Modules\ThmartApi\Models\ItemSalePrice;

class Ads extends Model
{
	protected $table = "ads";
	//指定此表主键,find方法直接传入主键值即可调用
	protected $primaryKey = "id";
	//必须指定参数白名单，否则create函数无法执行
	protected $fillable = ['adsPositionId', 'contentId', 'status', 'url', 'startTime', 'endTime', 'order', 'pic', 'merchantId'];
	//为true表示记录添加时间和更新时间
	public $timestamps = false;

    public function getOne($id)
    {
    	return $this->select('id')->find($id);
    }

    public function getDetail($id)
    {
        return $this->select('id', 'adsPositionId', 'contentId', 'status', 'url')->find($id);
    }

    public function addOne($array)
    {
    	return $this->create($array);
    }
    public function saveOne($array)
    {
        return $this->where(['id'=>$array['id']])->update($array);
    }
    
    /*
     * param merchantId表示获取品牌详情页配置内容的品牌id
     */
    public function getList($adsPositionId, $type, $merchantId=null, $order=null, $search=null)
    {
        if (!$order) $order = ' a.order desc';       
        //如果是商品广告
        if ($type == 1) {
            $res =  DB::select('SELECT * FROM
                        (select contentId as id, ads.order, price, item.pic, item.title, item.createTime, item.sellNumber
                        from ads 
                        left join item 
                        on item.id = contentId 
                        left join sku 
                        on item.id = sku.itemId 
                        where sku.isDelete = 0 and ads.status = 1 and ads.adsPositionId = '.$adsPositionId.'
                        order by price asc) 
                        as a group by id order by'.$order);
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
        //如果是商户广告列表
        if ($type == 2) {
            if (!$order) {
                $order = "'order', 'asc'";
            } else {
                $order = str_replace(' a.', '', $order);
                $order = explode(' ', $order);
            }
            $condition = [
                ['adsPositionId', '=', $adsPositionId],
                ['ads.status', '=', 1],
            ];
            if ($search) array_push($condition, ['brand.name', 'like', "%{$search}%"]);
            $res = $this->select('contentId as id', 'brand.pic', 'brand.name as title', 'brand.templet')
                ->where($condition)
                ->join('brand', 'brand.id', '=', 'ads.contentId')
                ->orderBy($order['0'], $order['1'])
                ->get()
                ->toArray();
            return convertUrl($res);
        }
        //如果是链接广告列表
        if ($type == 3) {
            $condition = [
                'adsPositionId'=>$adsPositionId, 'ads.status'=>1
            ];
            if ($merchantId) $condition['merchantId'] = $merchantId;
            $res = $this->select('url', 'pic')
                ->where($condition)
                ->orderBy('order', 'asc')
                ->get()
                ->toArray();
            
            return convertUrl($res);
        }
        //如果是文章广告列表
        if ($type == 4) {
            $res = $this->select('contentId as id', 'article.pic', 'title', 'article.createTime')
                ->where(['adsPositionId'=>$adsPositionId, 'ads.status'=>1])
                ->join('article', 'article.id', '=', 'ads.contentId')
                ->orderBy('order', 'asc')
                ->get()
                ->toArray();
            return convertUrl($res);
        }
    }

    //删除一条广告
    public function deleteOne($adsId)
    {
        $this->where('id', $adsId)->delete();
    }
}