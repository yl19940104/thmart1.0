<?php
namespace App\Modules\ThmartApi\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Category;
use Illuminate\Http\Request;

class EditController extends Controller
{ 

	public function index(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'name'      => 'required',
			'isFinal'   => 'min:0|max:1|integer',
			'allowItem' => 'min:0|max:1|integer',
			'isOnline'  => 'min:0|max:1|integer',
			'isDelete'  => 'min:0|max:1|integer',
			'orderby'   => 'integer',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 只能为整数',
            'min'      => ':attribute 最小为0',
            'max'      => ':attribute 最大为1',
		], [
            'fname' => '父类',
            'title' => '标题',
		]);
		if ($validator->fails()) {
			return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
		}
		$category = new Category;
		if (!$res = $category->getOne($request->input('name'))) return response()->json(['code' => "0", 'message' => '该分类不存在']);
        $category->saveOne($request->input());
        return response()->json(['code' => "1", 'message' => '修改成功']);
	}
}

