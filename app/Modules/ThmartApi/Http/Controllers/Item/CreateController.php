<?php
namespace App\Modules\ThmartApi\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Item;
use App\Modules\ThmartApi\Models\Supplier;
use App\Modules\ThmartApi\Models\Category;
use App\Modules\ThmartApi\Models\Brand;
use App\Modules\ThmartApi\Models\Precentage;
use App\Modules\ThmartApi\Models\ItemCaroPic;
use Illuminate\Http\Request;

class CreateController extends Controller
{ 
	public function __construct(){}

	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) {
			returnFalse($validator->getMessageBag());
		}
		$param = $request->input();
        $supplier = new Supplier();
        $category = new Category();
        $brand = new Brand();
        $item = new Item();
        if (!$supplier->getOne($request->input('shopId'))) return response()->json(['code' => "0", 'message' => '供应商编号不存在']);
        if (!$category->getOne($request->input('categoryName'))) return response()->json(['code' => "0", 'message' => '商品类目名称不存在']);
        if (!$brand->getOne($request->input('brandName'))) return response()->json(['code' => "0", 'message' => '品牌不存在']);
        if ($request->input('offLineTime') && $request->input('onLineTime') && ($request->input('offLineTime') <= $request->input('onLineTime')))
            return response()->json(['code' => "0", 'message' => '下线时间不能小于等于上线时间']);
        if (!isset($param['id']) || !$param['id']) {
        	//保存商品基础信息
	        if ($data = $item->addOne($request->input())) {
	        	//保存轮播图信息
	        	if (isset($param['picList'])) {
	        		foreach ($param['picList'] as $v) {
			        	(new ItemCaroPic)->addOne(['itemId'=>$data['id'], 'pic'=>$v]);
			        }
	        	}
	        	//保存商品编号
	            if ($item->saveOne(['id'=>$data['id'], 'goodsNumber'=>'6'.str_pad($data['id'], 5, "0", STR_PAD_LEFT)])) returnJson(1, '正在提交', $data['id']);
	        };
        } else {
        	unset($param['point']);
        	unset($param['notShowLay']);
        	$data = $item->saveOne($param);
        	(new ItemCaroPic)->deleteItemAllPic($param['id']);
        	//保存轮播图信息
        	if (isset($param['picList'])) {
        		foreach ($param['picList'] as $v) {
		        	(new ItemCaroPic)->addOne(['itemId'=>$param['id'], 'pic'=>$v]);
		        }
        	}
        	returnJson(1, '正在提交', $param['id']);
        }
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'shopId'             => 'required|integer',
		    'type'               => 'required|integer|min:1|max:3',
		    'categoryName'       => 'required|string',
		    'categoryTwoName'    => 'required|string',
		    'brandName'          => 'required|string',
		    'title'              => 'required|string',
		    'subTitle'           => 'required|string',
		    'onLineTime'         => 'integer',
		    'offLineTime'        => 'integer',
		    'pic'                => 'required|string',
		    'detail'             => 'required|string',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
            'min'      => ':attribute 最小为0',
            'max'      => ':attribute 最大为1',
            'string'   => ':attribute 必须为字符串',
		], [
            'shopId'             => '商家编号',
		    'type'               => '商品类型',
		    'categoryName'       => '商品一级分类',
		    'categoryTwoName'    => '商品二级分类',
		    'brandName'          => '品牌',
		    'title'              => '英文名称',
		    'subTitle'           => '中文名称',
		    'onLineTime'         => '上线时间',
		    'offLineTime'        => '下线时间',
		    'pic'                => '主图',
		    'detail'             => '内容',
		]);
		return $validator;
	}
}

