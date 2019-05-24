<?php
namespace App\Modules\ThmartApi\Http\Controllers\Ads;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Ads;
use Illuminate\Http\Request;

class DeleteController extends Controller
{ 

	public function __construct(){}
	
	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		$param = $request->input();
		if ($validator->fails()) returnJson(0, $validator->getMessageBag());
		$ads = new Ads();
        if (!$res = $ads->getDetail($request->input('id'))) returnJson(0, '该广告id不存在');
        $ads->deleteOne($param['id']);
        returnJson(1, 'success', $res);
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'id'           => 'required|integer',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
		], [
            'id'           => '广告id',
		]);
		return $validator;
	}
}

