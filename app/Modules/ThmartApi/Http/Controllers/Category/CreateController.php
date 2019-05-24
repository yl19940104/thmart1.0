<?php
namespace App\Modules\ThmartApi\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Category;
use Illuminate\Http\Request;

class CreateController extends Controller
{ 

	public function index(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'fname' => 'required|integer',
			'title' => 'required',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
		], [
            'fname' => '父类',
            'title' => '标题',
		]);
		if ($validator->fails()) {
			return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
		}
		$category = new Category;
		if ($request->input('fname') != 0) {
            if (!$res = $category->getOne($request->input('fname'))) return response()->json(['code' => "0", 'message' => '该分类不存在']);
	        if ($res['isFinal'] == 1) return response()->json(['code' => "0", 'message' => '该分类无法添加子分类']);
	        if ($res['isDelete'] == 1) return response()->json(['code' => "0", 'message' => '该分类已被删除']);
		}
        $array = [
        	'fname'   => $request->input('fname'),
        	'title'   => $request->input('title'),
        	'isFinal' => 0,
        	'allow'   => 1,
        ];
        $category->addOne($array);
        return response()->json(['code' => "1", 'message' => '添加成功']);
	}
}

