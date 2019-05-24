<?php
namespace App\Modules\ThmartApi\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Juhe\Juhe;
use Illuminate\Http\Request;

class DetailController extends Controller
{ 

	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) {
			return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
		}
		$param = $request->input();
	    $params = array(
		  'key' => '198f3399d4bc3dafa970d416d8f89bfb', //您申请的快递appkey
		  'com' => $param['company'], //快递公司编码，可以通过$exp->getComs()获取支持的公司列表
		  'no'  => $param['logistics'], //快递编号
		);
		$res = new Juhe($params['key']);
		/*$this->ajaxReturn($res->getComs());*/
        $result = $res->query($params['com'],$params['no']); //执行查询
		if ($result['error_code'] == 0) {//查询成功
		    $list = [
		    	'list' => $result['result']['list'],
		    	'company' => $result['result']['company'],
		    	'com' => $result['result']['com'],
		    	'status' => $result['result']['status'],
		    ];
		    returnJson(1, 'success', $list);
		} else {
		    returnJson(119, 'fail', $result['reason']);
		}
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'logistics' => 'required',
		    'company' => 'required',
		], [
            'required' => ':attribute 为必填项',
		], [
            'logistics' => '物流号',
            'company' => '物流公司',
		]);
		return $validator;
	}
}

  