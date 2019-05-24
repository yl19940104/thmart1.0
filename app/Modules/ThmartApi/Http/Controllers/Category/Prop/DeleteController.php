<?php
namespace App\Modules\ThmartApi\Http\Controllers\Category\Prop;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\PropTemplate;
use Illuminate\Http\Request;

class DeleteController extends Controller
{ 

	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) {
			return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
		}
        $propTemplate = new propTemplate();
        if (!$propTemplate->getOne(null, null, null, $request->input('id'))) return response()->json(['code' => "0", 'message' => '该属性模板不存在']);
        if ($propTemplate->deleteOne($request->input('id'))) return response()->json(['code' => "0", 'message' => '删除成功']);
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'id' => 'required|integer',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
		], [
            'id' => '模板id',
		]);
		return $validator;
	}
}

