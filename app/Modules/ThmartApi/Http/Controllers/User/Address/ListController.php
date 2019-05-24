<?php
namespace App\Modules\ThmartApi\Http\Controllers\User\Address;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Address;
use Illuminate\Http\Request;

class ListController extends Controller
{ 

	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) returnJson(0, $validator->getMessageBag());
		$param = $request->input();
		if ($res = (new Address)->getList($this->userId, $param['pageSize'])) {
			$res= objectToArray($res);
			$data = [
				'data'      => $res['data'],
				'totalPage' => $res['last_page'],
			];
			returnJson(1, 'success', $data);
		}
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'pageSize' => 'required|integer',
		    'page'     => 'required|integer',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
		], [
            'pageSize'    => '每页显示数据量',
            'page'        => '当前页',
		]);
		return $validator;
	}
}

