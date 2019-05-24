<?php
namespace App\Modules\ThmartApi\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Item;
use App\Modules\ThmartApi\Models\Category;
use App\Modules\ThmartApi\Models\Brand;
use App\Modules\ThmartApi\Models\Precentage;
use Illuminate\Http\Request;

class EditController extends Controller
{ 

	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) {
			return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
		}
        $item = new Item();
        if (!$item->getOne($request->input('id'))) return response()->json(['code' => "0", 'message' => '该商品不存在']);
        if ($request->input('offLineTime') && $request->input('onLineTime') && ($request->input('offLineTime') <= $request->input('onLineTime')))
            return response()->json(['code' => "0", 'message' => '下线时间不能小于等于上线时间']);
        if ($item->saveOne($request->input())) return response()->json(['code' => "1", 'message' => '编辑成功']);
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'id'          => 'required|integer',
		    'onLineTime'  => 'integer',
		    'offLineTime' => 'integer',
		    /*'shippingTemplateId' => 'integer',*/
		    'shippingFee' => 'integer',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
		], [
            'id'           => '商品编号',
		    'onLineTime'   => '上线时间',
		    'offLineTime'  => '下线时间',
		    /*'shippingTemplateId' => '运费模板编号',*/
		    'shippingFee'  => '运费',
		]);
		return $validator;
	}
}

