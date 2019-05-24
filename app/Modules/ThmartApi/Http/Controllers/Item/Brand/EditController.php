<?php
namespace App\Modules\ThmartApi\Http\Controllers\Item\Brand;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Brand;
use Illuminate\Http\Request;

class EditController extends Controller
{ 

	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) {
			return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
		}
        $brand = new Brand();
        if (!$brand->getOne($request->input('name'))) {
        	if ($brand->addOne($request->input())) return response()->json(['code' => "1", 'message' => '添加成功']);
        } else {
            if ($brand->saveOne($request->input())) return response()->json(['code' => "1", 'message' => '更新成功']);
        }
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'name'        => 'required',
		    'chineseName' => 'required',
		    'englishName' => 'required',
		    'isOffline'   => 'integer|min:0|max:1',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
            'min'      => ':attribute 最小值为0',
            'max'      => ':attribute 最大值为1',
		], [
            'name'        => '名称',
		    'chineseName' => '中文名称',
		    'englishName' => '英文名称',
		    'isOffline'   => '是否下线字段',
		]);
		return $validator;
	}
}

