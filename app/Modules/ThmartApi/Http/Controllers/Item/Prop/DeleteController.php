<?php
namespace App\Modules\ThmartApi\Http\Controllers\Item\Prop;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Item;
use App\Modules\ThmartApi\Models\PropTemplate;
use App\Modules\ThmartApi\Models\ItemPropValue;
use Illuminate\Http\Request;

class DeleteController extends Controller
{ 

	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) {
			return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
		}
		$item = new Item();
		$propTemplate = new PropTemplate();
		$itemPropValue = new ItemPropValue();
        if (!$item->getOne($request->input('itemId'))) return response()->json(['code' => "0", 'message' => '该商品编号不存在']);
        if (!$propTemplate->getOne(null, null, null, $request->input('propTemplateId'))) return response()->json(['code' => "0", 'message' => '该属性模板不存在']);
        if (!$itemPropValue->getOne($request->input('itemId'), $request->input('propTemplateId'))) return response()->json(['code' => "0", 'message' => '该商品不存在此模板属性']);
        if ($itemPropValue->deleteOne($request->input('itemId'), $request->input('propTemplateId'))) return response()->json(['code' => "1", 'message' => '删除成功']);
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'itemId'         => 'required|integer',
		    'propTemplateId' => 'required|integer',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
		], [
            'itemId'         => '商品编号',
		    'propTemplateId' => '属性模板',
		]);
		return $validator;
	}
}

