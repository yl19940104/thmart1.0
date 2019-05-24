<?php
namespace App\Modules\ThmartApi\Http\Controllers\User\Address;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Address;
use Illuminate\Http\Request;

class ChangeDefaultController extends Controller
{ 

	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) returnJson(0, $validator->getMessageBag());
		$param = $request->input();
		$address = new Address;
		$res = $address->findDefault($this->userId);
		$address->saveOne(['id'=>$res['id'], 'isDefault'=>0]);
		$address->saveOne(['id'=>$param['id'], 'isDefault'=>1]);
		returnJson(1, 'success');
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'id' => 'required|integer',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
		], [
            'id'    => '地址id',
		]);
		return $validator;
	}
}

