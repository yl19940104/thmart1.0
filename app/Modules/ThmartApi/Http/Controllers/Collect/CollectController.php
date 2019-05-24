<?php
namespace App\Modules\ThmartApi\Http\Controllers\Collect;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\UserCollect;
use App\Modules\ThmartApi\Models\Item;
use App\Modules\ThmartApi\Models\Brand;
use Illuminate\Http\Request;

class CollectController extends Controller
{ 

	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) {
			return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
		}
		$param = $request->input();
		if ($param['type'] == 1) {
			if (!$res = (new Item)->getOne($param['contentId'])) returnJson(0, '商品id不存在');
		}
	    if ($param['type'] == 2) {
			if (!$res = (new Brand)->getOne($param['contentId'])) returnJson(0, '商户id不存在');
		}
		$param['userId'] = $this->userId;
		//如果用户有此内容id的收藏记录
		if (!$res = (new UserCollect)->getOne($param['type'], $param['contentId'], $this->userId)) {
			(new UserCollect)->saveOne($param);
		    returnJson(1, 'success');
		};
		(new UserCollect)->updateOne($res['0']['id'], $param['isCollect']);
		returnJson(1, 'success');
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'type'      => 'required|integer|min:1|max:2',
		    'contentId' => 'required|integer',
		    'isCollect' => 'required|integer|min:0|max:1',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
            'min'      => ':attribute 最小为1',
            'min'      => ':attribute 最大为2',
		], [
            'type'      => '类型id',
            'contentId' => '内容id',
		]);
		return $validator;
	}
}

