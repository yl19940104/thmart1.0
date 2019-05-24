<?php

namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use App\Modules\ThmartApi\Models\OrdersSku;
use App\Modules\ThmartApi\Models\Item;
class MongoComment extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'comment';
    protected $primaryKey = '_id';
    protected $fillable = ['goodsId', 'userId', 'hasPic', 'info', 'audited', 'isDelete', 'ordersSkuId'];
    public $timestamps = true;

    public function addData($param)
    {
        $sku = Sku::select('propName', 'price')->where('id', $param['skuId'])->get()->toArray();
        $item = Item::select('title', 'item.pic as goodsPic', 'brand.name as brandName')->where('item.id', $param['goodsId'])->leftjoin('brand', 'brand.id', '=', 'item.brandName')->get()->toArray();
        $data = [
            'goodsId' => intval($param['goodsId']),
            'audited' => 0,
            'isDelete' => 0,
        ];
        isset($param['picList']) && $param['picList']? $data['hasPic'] = 1 : $data['hasPic'] = 0;
        $data['info'] = [
            'comment' => $param['comment'],
            'username' => $param['username'],
            'headimgUrl' => config('config.commentHeadimg'),
            'number' => intval($param['number']),
            'propName' => $sku['0']['propName'],
            'price' => $sku['0']['price'],
            'brandName' => $item['0']['brandName'],
            'title' => $item['0']['title'],
            'orderTime' => date('Y-m-d H:i:s', time()),
            'goodsPic' => $item['0']['goodsPic'],
            'reply' => $param['reply'],
            'skuId' => intval($param['skuId']),
        ];
        if (isset($param['picList']) && $param['picList']) $data['info']['pic'] = implode('|', $param['picList']);
        if (isset($param['reply']) && $param['reply']) $data['info']['reply'] = $param['reply'];
        $this->create($data);
    }

    public function updateData($param)
    {
        $sku = Sku::select('propName', 'price')->where('id', $param['skuId'])->get()->toArray();
        $item = Item::select('title', 'item.pic as goodsPic', 'brand.name as brandName')->where('item.id', $param['goodsId'])->leftjoin('brand', 'brand.id', '=', 'item.brandName')->get()->toArray();
        $saveData = [];
        $res = $this->find($param['id'])->toArray();
        $saveData['goodsId'] = intval($param['goodsId']);
        (isset($param['picList']) && $param['picList']) ? $saveData['hasPic'] = 1 : $saveData['hasPic'] = 0;
        $saveData['info'] = $res['info'];
        $saveData['info']['comment'] = $param['comment'];
        $saveData['info']['number'] = intval($param['number']);
        $saveData['info']['propName'] = $sku['0']['propName'];
        $saveData['info']['price'] = intval($sku['0']['price']);
        $saveData['info']['brandName'] = $item['0']['brandName'];
        $saveData['info']['title'] = $item['0']['title'];
        $saveData['info']['orderTime'] = date('Y-m-d H:i:s', time());
        $saveData['info']['goodsPic'] = $item['0']['goodsPic'];
        unset($saveData['info']['reply']);
        if (isset($param['reply']) && $param['reply']) $saveData['info']['reply'] = $param['reply'];
        $saveData['info']['skuId'] = intval($param['skuId']);
        if (isset($saveData['info']['pic'])) unset($saveData['info']['pic']);
        if (isset($param['picList']) && $param['picList']) $saveData['info']['pic'] = implode('|', $param['picList']);
        $this->where('_id', $param['id'])->update($saveData);
    }

    public function getList($id)
    {
        $res = $this->select('info', 'hasPic')->where(['goodsId'=>$id, 'isDelete'=>'0', 'audited'=>'1','created_at'])->get()->toArray();
        return $res;
    }

    public function userAddData($param)
    {
        $data = [
            'ordersSkuId' => intval($param['id']),
            'userId'      => intval($param['userId']),
        ];
        $res = $this->where($data)->get()->toArray();
        if (isset($res) && $res) returnJson(0, 'You have already commented');
        $array = [];
        $res = OrdersSku::select('goodsId', 'number', 'skuPropName as propName', 'skuPrice as price', 'brandName', 'title', 'orderTime', 'pic as goodsPic', 'skuId')->leftjoin('orders', 'orders.id', '=', 'orderssku.orderId')->where('orderssku.id', $param['id'])->get()->toArray();
        if (!$res) returnJson(0, 'wrong ordersSkuId');
        $saveData = [
            'goodsId'       => intval($res['0']['goodsId']),
            'audited'       => 0,
            'isDelete'      => 0,
            'userId'        => intval($param['userId']),
            'ordersSkuId'   => intval($param['id']),
            'info' => [
                'comment'   => $param['comment'],
                'number'    => intval($res['0']['number']),
                'propName'  => $res['0']['propName'],
                'price'     => $res['0']['price'],
                'brandName' => $res['0']['brandName'],
                'title'     => $res['0']['title'],
                'orderTime' => $res['0']['orderTime'],
                'goodsPic'  => $res['0']['goodsPic'],
                'skuId'     => intval($res['0']['skuId']),
            ]
        ];
        if (isset($param['picList'])) {
            foreach ($param['picList'] as $v) {
                $array[] = base64_image_content($v, 'storage/comment/');
            }
        }
        isset($param['picList']) ? $saveData['hasPic'] = 1 : $saveData['hasPic'] = 0;
        if (isset($param['picList'])) {
            $saveData['hasPic'] = 1;
            $saveData['info']['pic'] = implode('|', $array);
        }
        $this->create($saveData);
        return true;
    }
}