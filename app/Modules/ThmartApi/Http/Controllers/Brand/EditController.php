<?php
namespace App\Modules\ThmartApi\Http\Controllers\Brand;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Brand;
use Illuminate\Http\Request;

class EditController extends Controller
{ 

	public function __construct(){}

	public function index(Request $request)
	{
		$roleArray = session()->get('userInfo');
		$param = $request->input();
		$validator = $this->validateParam($request);
		if ($validator->fails()) returnFalse($validator->getMessageBag());
		$brand = new Brand;
		if (isset($param['id'])) {
			if (!$brand->getOne($param['id'])) returnJson(0, '该品牌不存在');
			$brand->saveOne($param);
			returnJson(1, '更新成功');
		} else {
			//如果是管理员，品牌默认激活
			if (in_array(1, $roleArray)) {
				$param['status'] = 1;
			//如果不是管理员，品牌默认不激活
			} else {
				$param['status'] = 0;
			}
			$brand->addOne($param);
			returnJson(1, '添加成功');
		}
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'id'       => 'integer',
		    'pic'      => 'required',
		    'name'     => 'required',
		    'name_cn'  => 'required',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
		], [
            'id'       => '品牌id',
		    'pic'      => '图片',
		    'name'     => '品牌名',
		    'name_cn'  => '品牌中文名',
		]);
		return $validator;
	}
}

