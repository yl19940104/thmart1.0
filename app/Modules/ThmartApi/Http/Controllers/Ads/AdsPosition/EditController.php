<?php
namespace App\Modules\ThmartApi\Http\Controllers\Ads\AdsPosition;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\AdsPosition;
use Illuminate\Http\Request;

class EditController extends Controller
{ 

	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) {
			return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
		}
		$adsPosition = new AdsPosition();
		if (!$request->input('id')) {
			if ($adsPosition->getOne($request->input('name'))) returnJson(0, '该位置名字已存在');
            $adsPosition->addOne($request->input());
		} else {
			if (!$adsPosition->getOneById($request->input('id'))) returnJson(0, '该位置id不存在');
            $adsPosition->saveOne($request->input());
		}
		returnJson(1, 'success');
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'id'     => 'integer',
		    'name'   => 'required',
		    'status' => 'required|integer',
		    'type'   => 'required|integer|min:1|max:4',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
            'min'      => ':attribute 最小为1',
            'max'      => ':attribute 最大为3',
		], [
            'id'     => 'id',
            'name'   => '位置名字',
            'status' => '状态',
            'type'   => '类型',
		]);
		return $validator;
	}
}

