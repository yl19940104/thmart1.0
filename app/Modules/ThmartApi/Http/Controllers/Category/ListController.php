<?php
namespace App\Modules\ThmartApi\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Category;
use App\Modules\ThmartApi\Models\Item;
use Illuminate\Http\Request;

class ListController extends Controller
{ 
    public function __construct(){}

	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) {
			return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
		}
		$category = new Category;
		if (!$res = $category->getList($request->input('fname'))) return response()->json(['code' => "0", 'message' => '该父类下不存在子分类']);
		if ($request->input('fname') == 0) {
			foreach ($res as $k => $v) {
				$data = (new Item)->findOne($v['id']);
				if (!$data) unset($res[$k]);
			}
		}
		//后台请求数据时$request->input('byAdmin')值存在，不返回all
		if ($request->input('fname') == 0) array_unshift($res, ['id'=>'0','title'=>'All']);
	    returnJson(1, 'success', $res);
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'fname' => 'required|integer',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
		], [
            'fname' => '父类id',
		]);
		return $validator;
	}
}

