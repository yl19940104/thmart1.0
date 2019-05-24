<?php
namespace App\Modules\ThmartApi\Http\Controllers\Ads\AdsPosition;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\AdsPosition;
use Illuminate\Http\Request;

class DetailController extends Controller
{ 
	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) {
			return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
		}
		$adsPosition = new AdsPosition();
		if (!$res = $adsPosition->getOneById($request->input('id'))) returnJson(0, 'id不存在');
		returnJson(1, 'success', $res);
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'id'     => 'required|integer',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
		], [
            'id'     => 'id',
		]);
		return $validator;
	}
}

