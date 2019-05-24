<?php
namespace App\Modules\ThmartApi\Http\Controllers\User\Address;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Address;
use Illuminate\Http\Request;

class DeleteController extends Controller
{ 

	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) returnJson(0, $validator->getMessageBag());
		$param = $request->input();
		if (!$res = ((new Address)->getOneAddress($request->input('id')))) returnJson(0, '地址id不存在');
		//逻辑删除
	    (new Address)->saveOne(['id'=>$param['id'], 'isDelete'=>1, 'isDefault'=>0]);
	    if ($res['isDefault'] == 1) Address::setDefault($this->userId);
	    returnJson(1, 'success'); 
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'id'  => 'required|integer',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
		], [
		    'id'  => '地址id',
		]);
		return $validator;
	}
}

