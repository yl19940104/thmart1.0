<?php
namespace App\Modules\ThmartApi\Http\Controllers\Article;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Article;
use Illuminate\Http\Request;

class DeleteController extends Controller
{ 
    public function __construct(){}
    
	public function index(Request $request)
	{
		$param = $request->input();
		$validator = $this->validateParam($request);
		if ($validator->fails()) returnJson(0, $validator->getMessageBag());
		$article = new Article;
		if (!$res = $article->getDetail($param['id'])) returnJson(0, '文章id不存在');
		(new Article)->saveOne(['id'=>$param['id'], 'is_delete'=>1]);
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
            'id' => '文章id',
		]);
		return $validator;
	}
}

