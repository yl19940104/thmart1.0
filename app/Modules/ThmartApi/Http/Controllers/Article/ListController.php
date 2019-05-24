<?php
namespace App\Modules\ThmartApi\Http\Controllers\Article;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Article;
use App\Modules\ThmartApi\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ListController extends Controller
{ 
    public function __construct(){}
    
	public function index(Request $request)
	{
		$param = $request->input();
		$validator = $this->validateParam($request);
		if ($validator->fails()) returnJson(0, $validator->getMessageBag());
		$article = new Article;
		$param['sort'] = explode('_', $param['sort']);
		if ($search = $request->input('search')) $search = addslashes($search);
		$res = objectToArray($article->getList($param['pageSize'], $param['sort'], $search));
		$res['data'] = convertTime(convertUrl($res['data']));
		$data = [
			'data'      => $res['data'],
			'totalPage' => $res['last_page'], 
			'recommend' => (new Item)->recommendList(),
		];
	    returnJson(1, 'success', $data);
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'page'     => 'required|integer',
		    'pageSize' => 'required|integer',
		    'sort'    => ['required', Rule::in(['createTime_desc', 'click_desc'])],
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
		], [
            'page'     => '当前页',
		    'pageSize' => '每页显示数据量',
		    'sort'     => '排序',
		]);
		return $validator;
	}
}

