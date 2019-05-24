<?php
namespace App\Modules\ThmartApi\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Cart;
use App\Modules\ThmartApi\Models\Item;
use App\Modules\ThmartApi\Models\Sku;
use App\Modules\ThmartApi\Models\Brand;
use Illuminate\Http\Request;

class EditController extends Controller
{ 

	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) {
			returnJson(0, $validator->getMessageBag());
		}
		$param = $request->input();
        if (!$res = (new Item)->getOne($param['goodsId'])) returnJson(0, '该商品id不存在');
        if (!$data = ((new Sku)->getOneUnDelete($param['skuId']))) returnJson(0, '该sku不存在');
        if ($data['itemId'] != $param['goodsId']) returnJson(0, '商品与sku不对应');
        $getBrand = (new Item)->getOne($param['goodsId']);
        $getBrandName = (new Brand)->getDetail($getBrand['brandName']);
        if (!$data = (new Cart)->getOne($param['skuId'], $this->userId)) {
            //添加购物车
            $param = [
	        	'userId'    => $this->userId,
	        	'goodsName' => $res['title'],
	        	'goodsId'   => $res['id'],
	        	'skuId'     => $param['skuId'],
	        	'number'    => $param['number'],
	        	'brandId'   => $getBrand['brandName'],
	        	'brandName' => $getBrandName['name'],
	        	'pic'       => $getBrandName['pic'],
	        	'shopId'    => $res['shopId'],
	        	'type'      => $res['type'],
	        	'pic'       => $res['pic'],
	        ];
            $this->addCart($param);
        } else {
            //编辑购物车数量
            $this->editCart($data['0']['id'], $param['number']);
        };
        returnJson(1, 'success');
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'goodsId'     => 'required|integer',
		    'skuId'       => 'required|integer',
		    'number'      => 'required|integer',
		], [
            'required' => ':attribute 为必填项',        
            'integer'  => ':attribute 必须为数字',
		], [
            'goodsId'  => '商品编号',
            'skuId'    => 'sku编号',
            'number'   => '添加数量',
		]);
		return $validator;
	}

	public function addCart($param)
	{
        return (new Cart)->addOne($param);
	}

	public function editCart($id, $number)
	{
		$data = ['id'=>$id, 'number'=>$number];
        return (new Cart)->saveOne($data);      
	}
}

