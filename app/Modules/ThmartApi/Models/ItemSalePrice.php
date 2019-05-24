<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Modules\ThmartApi\Models\Sku;

class ItemSalePrice extends Model
{
	protected $table = "itemSalePrice";

	protected $fillable = ['goodsId', 'skuId', 'startTime', 'endTime', 'type', 'salePrice', 'goodsNumber', 'skuNumber', 'amount'];

	public $timestamps = false;

    public function addSalePriceList($array, $type)
    {
        //$type=1表示促销价，$type=2表示团购价,$type=3表示拼单价
        if ($type != 1 && $type != 2 && $type != 3) return ['message'=>'saleType is wrong'];
        foreach ($array as $v) {
            //校验参数
            $res = $this->check($v, $type);
            //如果参数无误
            if (!isset($res['message'])) {
                //如果不传id,表示添加促销价,那么查询所有该sku的促销价
                if (!isset($v['id'])) {
                    $data = $this->getList($v['skuNumber'], $type);
                //如果传id,表示修改促销价，那么查询除他自己的所有该sku的促销价
                } else {
                    $data = $this->getListWithOutItSelf($v['skuNumber'], $type, $v['id']);
                }
                $param = $this->clearTime($data, $v['startTime'], $v['endTime'], $v['rule']);
                if (isset($param['message'])) return ['message'=>$param['message']];
                $v['goodsId'] = $res['0']['itemId'];
                $v['goodsNumber'] = '6'.str_pad($v['goodsId'], 5, "0", STR_PAD_LEFT);
                $v['skuId'] = $res['0']['id'];
                $v['type'] = $type;
                $v['startTime'] = $param['startTime'];
                $v['endTime'] = $param['endTime'];
                //如果不传id,表示添加促销价,那么添加数据
                if (!isset($v['id'])) {

                    $this->create($v);

                //如果传id,表示修改促销价,那么修改数据
                } else {
                    unset($v['rule']);
                    $this->saveOne($v);
                }
            } else {
                return ['message'=>$res['message']];
            }
        }
    }
   
    //核对数组参数信息
    private function check($param, $type)
    {
        $this->judgeType($param['skuNumber'], $type);
        if (!isset($param['skuNumber'])) return ['message'=>'skuNumber is missing']; 
        if (!isset($param['startTime'])) return ['message'=>'startTime is missing'];
        if (!isset($param['endTime'])) return ['message'=>'endTime is missing'];
        if (!isset($param['salePrice'])) return ['message'=>'salePrice is missing'];
        if (!isset($param['rule'])) return ['message'=>'rule is missing'];
        //$param['rule'] == 1代表清除老sku的促销价重叠有效时间 , $param['rule'] == 2代表清除新sku的促销价重叠有效时间 $param['rule'] == 3代表清除老sku的所有时间有重叠的促销价   
        if ($param['rule'] != 1 && $param['rule'] != 2 && $param['rule'] != 3) return ['message'=>'rule is wrong'];$data = (new Sku)->getOneBySkuNumber($param['skuNumber']);
        if (!$res = (new Sku)->getOneBySkuNumber($param['skuNumber'])) return ['message'=>'skuNumber '.$param['skuNumber'].' is not exist'];
        if ($param['startTime'] >= $param['endTime']) {
            return ['message'=>'wrong startTime or endTime'];
        }
        if ($param['salePrice'] <= 0) return ['message'=>'wrong salePrice'];
        return $res;
    }

    //清除老sku的促销价重叠有效时间 或 清除新sku的促销价重叠有效时间 或 清除老sku的所有时间有重叠的促销价   
    private function clearTime($array, $startTime, $endTime, $rule)
    {   
        $startTime = $startTime;
        $endTime = $endTime;
        foreach ($array as $v) {
            //清除老sku的促销价重叠有效时间
            if ($rule == 1) {
                if ($startTime >= $v['startTime'] && $startTime <= $v['endTime'] && $endTime >= $v['endTime']) {
                    $param = ['id'=>$v['id'], 'endTime'=>$startTime];
                    $this->saveOne($param);
                } elseif ($endTime <= $v['endTime'] && $endTime >= $v['startTime'] && $startTime <= $v['startTime']) {
                    $param = ['id'=>$v['id'], 'startTime'=>$endTime];
                    $this->saveOne($param);
                } elseif ($startTime <= $v['startTime'] && $endTime >= $v['endTime']) {
                    $this->deleteOne($v['id']);
                } elseif ($startTime >= $v['startTime'] && $endTime <= $v['endTime']) {
                    return ['message'=>'开始时间或结束时间设置错误1'];
                }
            }
            //清除新sku的促销价重叠有效时间
            if ($rule == 2) {
                if ($startTime >= $v['startTime'] && $startTime <= $v['endTime'] && $endTime >= $v['endTime']) {
                    $startTime = $v['endTime'];
                } elseif ($endTime <= $v['endTime'] && $endTime >= $v['startTime'] && $startTime <= $v['startTime']) {
                    $endTime = $v['startTime'];
                } elseif ($startTime <= $v['startTime'] && $endTime >= $v['endTime']) {
                    return ['message'=>'开始时间或结束时间设置错误2'];
                } elseif ($startTime >= $v['startTime'] && $endTime <= $v['endTime']) {
                    return ['message'=>'开始时间或结束时间设置错误3'];
                }
            }
            //清除老sku的所有时间有重叠的促销价
            if ($rule == 3) {
                //时间不重叠 取反
                if (!(($startTime >= $v['endTime']) || ($endTime <= $v['startTime']))) {
                    $this->deleteOne($v['id']);
                }
            }                
        }
        return ['startTime'=>$startTime, 'endTime'=>$endTime];
    }

    //如果该商品有团购价就不能有拼单价，有拼单价就不能有团购价
    private function judgeType($skuNumber, $type){
        /*$type == 2 ? $typeQuery = 3 : $typeQuery = 2;*/
        if ($type == 1) $typeQuery = [2,3];
        if ($type == 2) $typeQuery = [1,3];
        if ($type == 3) $typeQuery = [1,2];
        $res = $this->select('id')->where(['skuNumber'=>$skuNumber, ['endTime', '>=', time()]])->whereIn('type', $typeQuery)->get()->toArray();
        if (isset($res) && $res) returnJson(0, $skuNumber.'已有其他类型的价格');
    }

    public function getList($skuNumber, $type)
    {   
        $res = $this->select('startTime', 'endTime', 'id')->where(['skuNumber'=>$skuNumber, 'type'=>$type])->get(); 
        if ($res) return $res->toArray();
    }

    //获取当前时间某商品的最低促销(团购)价
    public function getMinItemSale($goodsId)
    {   
        $res =  $this->select('salePrice')
            ->leftjoin('sku', 'sku.id', '=', 'itemSalePrice.skuId')
            ->where(['goodsId'=>$goodsId, ['startTime', '<=', time()], ['endTime', '>=', time()], 'sku.isDelete'=>0])
            ->min('salePrice');
        return $res / 100;
    }

    //获取当前时间某商品的最高促销(团购)价
    public function getMaxItemSale($goodsId)
    {   
        $res =  $this->select('salePrice')
            ->leftjoin('sku', 'sku.id', '=', 'itemSalePrice.skuId')
            ->where(['goodsId'=>$goodsId, ['startTime', '<=', time()], ['endTime', '>=', time()], 'sku.isDelete'=>0])
            ->max('salePrice');
        return $res / 100;
    }

    //获取某sku的最低促销(团购价)
    public function getMinSkuSale($skuId)
    {   
        $res =  $this->select('salePrice')
            ->where(['skuId'=>$skuId, ['startTime', '<=', time()], ['endTime', '>=', time()]])
            ->min('salePrice');
        return $res / 100;
    }

    public function getItemSalePrice($skuNumber, $type)
    {   
        $res = $this->select('startTime', 'endTime', 'id')->where(['skuNumber'=>$skuNumber, 'type'=>$type])->get(); 
        if ($res) return $res->toArray();
    }

    //查询除他自己促销价外的所有该sku的促销价
    public function getListWithOutItSelf($skuNumber, $type, $id)
    {   
        $res = $this->select('startTime', 'endTime', 'id')->where(['skuNumber'=>$skuNumber, 'type'=>$type])->whereNotIn('id', [$id])->get(); 
        if ($res) return $res->toArray();
    }

    public function saveOne($array)
    {   
        $this->where('id', $array['id'])->update($array);
    }

    public function deleteOne($id)
    {   
        $this->where('id', $id)->update(['endTime'=>time()]);
    }

    public function deleteList($array)
    {   
        /*return $this->destroy($array);*/
        foreach ($array as $v) {
            $this->where('id', $v)->update(['endTime'=>time()]);
        }
        return true;
    }

    //数组中批量加入最小促销(团购)价
    public function addArrayMinSalePrice($array)
    {   
        foreach ($array as &$v) {
            $minSalePrice = $this->getMinItemSale($v['id']);
            //如果最低促销(团购)价小于最低原价，那么列表页显示最低促销(团购)价
            if ($minSalePrice && $minSalePrice < $v['price']) {
                $param = $v['price'];
                $v['price'] = $minSalePrice;
                $v['originalPrice'] = $param;
            }
            if ($this->hasSalePrice($v['id'], 2)) {
                //saleType为列表页显示商品活动类型，sale:促销活动，group:团购活动，none:原价，无活动
                $v['saleType']['type'] = 'group';
            } elseif ($this->hasSalePrice($v['id'], 1)) {
                $v['saleType']['type'] = 'sale';
            } else {
                $v['saleType']['type'] = 'none';
            }
        }
        unset($v);
        return $array;
    }

    //数组中批量加入最小促销(团购)价(优化方案)
    public function addArrayMinSalePriceTwo($array)
    {
        $itemArray = [];
        foreach ($array as &$v) {
            if (!in_array($v['id'], $itemArray)) {
                array_push($itemArray, $v['id']);
            }
        }
        $itemSalePrice = new ItemSalePrice;
        /*$res = $itemSalePrice->where([['startTime', '<=', time()], ['endTime', '>=', time()]])->whereIn('goodsId', $itemArray)->groupBy('goodsId')->min('salePrice')->get();*/
        returnJson(1, $res);
    }

    //获取某sku最低促销(团购)价以及对应类型(促销或团购)
    public function getSkuMinPriceType($skuId)
    {   
        /*if ($res = $this->select('type', 'salePrice')
            ->where(['skuId'=>$skuId, ['startTime', '<=', time()], ['endTime', '>=', time()]])
            ->orderBy('salePrice', 'asc')
            ->get()) {

        }return $res->toArray();*/
        $res = $this->select('type', 'salePrice')
            ->where(['skuId'=>$skuId, ['startTime', '<=', time()], ['endTime', '>=', time()]])
            ->orderBy('salePrice', 'asc')
            ->get();
        if ($res) {
            $res = $res->toArray();
            foreach ($res as &$v) {
                $v['salePrice'] /= 100;
            }
            unset($v);
            return $res;
        }
    }

    //判断是否有团购价或者促销价或者拼单价
    public function hasSalePrice($goodsId, $type)
    {
        $res = $this->select('id', 'salePrice', 'endTime')
            ->where(['goodsId'=>$goodsId, ['startTime', '<=', time()], ['endTime', '>=', time()], 'type'=>$type])
            ->orderBy('salePrice', 'asc')
            ->get()
            ->toArray();
        if ($res) {
            $res['0']['salePrice'] / 100;
            return $res['0'];
        }
    }

    //获取单条sku信息
    public function getOneSalePrice($skuNumber)
    {
        $res = $this->select('id')
            ->where(['skuNumber'=>$skuNumber])
            ->get()
            ->toArray();
        if ($res) {
            return $res;
        }
    }

    //获取某sku最低促销(团购)价
    public function getSkuMinPrice($skuId, $type)
    {   
        $res = $this->select('salePrice', 'startTime', 'endTime')
            ->where(['skuId'=>$skuId, 'type'=>$type, ['startTime', '<=', time()], ['endTime', '>=', time()]])
            ->orderBy('salePrice', 'asc')
            ->get();
        if ($res) {
            $res = $res->toArray();
            foreach ($res as &$v) {
                $v['salePrice'] / 100;
            }
            unset($v);
            return $res;
        }
    }
}