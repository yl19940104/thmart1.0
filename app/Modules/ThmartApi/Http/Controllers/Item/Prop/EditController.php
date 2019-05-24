<?php
namespace App\Modules\ThmartApi\Http\Controllers\Item\Prop;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Item;
use App\Modules\ThmartApi\Models\PropTemplate;
use App\Modules\ThmartApi\Models\ItemPropValue;
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
		$propTemplate = new PropTemplate();
		$itemPropValue = new ItemPropValue();
        if (!$resItem = $item->getOne($request->input('itemId'))) return response()->json(['code' => "0", 'message' => '该商品编号不存在']);
        if (!$resPropTemplate = $propTemplate->getOne(null, null, null, $request->input('propTemplateId'))) return response()->json(['code' => "0", 'message' => '该属性模板不存在']);
        if ($resItem['categoryName'] != $resPropTemplate['categoryName']) return response()->json(['code' => "0", 'message' => '商品所属分类与模板属性所属分类不同']);
        if ($resPropTemplate['type'] != 0) return response()->json(['code' => "0", 'message' => '该属性模板不属于商品']);
        $data = $itemPropValue->getOne($request->input('itemId'), $request->input('propTemplateId'));
        if (!$data) {
        	$res = $itemPropValue->addOne($request->input(), $resPropTemplate['name']);
        } else {
            $res = $itemPropValue->saveOne($request->input(), $resPropTemplate['name']);
        }
        return response()->json(['code' => "1", 'message' => '操作成功']);
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'itemId'         => 'required|integer',
		    'propTemplateId' => 'required|integer',
		    'orderby'        => 'integer',
		    'value'          => 'required|string',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
            'min'      => ':attribute 最小为0',
            'max'      => ':attribute 最大为1',
            'string'   => ':attribute 必须为字符串',
		], [
            'itemId'         => '商品编号',
		    'propTemplateId' => '属性模板',
		    'orderby'        => '排序',
		    'value'          => '属性模板值',
		]);
		return $validator;
	}
}

