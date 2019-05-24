<?php
namespace App\Modules\ThmartApi\Http\Controllers\Brand;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Brand;
use Illuminate\Http\Request;

class ListController extends Controller
{ 
    public function __construct(){}

	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		$param = $request->input();
		if ($validator->fails()) returnJson(0, $validator->getMessageBag());
        $res = (new Brand)->getList($param['pageSize']);
        foreach ($res['data'] as &$v) {
        	$v['pic'] = adminDomain().$v['pic'];
        }
        unset($v);
        $data = [
			'data'      => $res['data'],
			'totalPage' => $res['last_page'], 
		];
        returnJson(1, 'success', $data);
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'page'     => 'required|integer',
		    'pageSize' => 'required|integer',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
		], [
            'page'     => '当前页数',
		    'pageSize' => '每页显示数据量',
		]);
		return $validator;
	}
}

