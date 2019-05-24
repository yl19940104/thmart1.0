<?php
namespace App\Modules\ThmartApi\Http\Controllers\Article;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Article;
use App\Modules\ThmartApi\Models\ArticleItem;
use Illuminate\Http\Request;

class DetailController extends Controller
{ 
    public function __construct(){}
    
	public function index(Request $request)
	{
		$param = $request->input();
		$validator = $this->validateParam($request);
		if ($validator->fails()) returnJson(0, $validator->getMessageBag());
		$article = new Article;
		if (!$res = $article->getDetail($param['id'])) returnJson(0, '文章id不存在');
		$res['pic'] = adminDomain().$res['pic'];
        $res['article_content'] = contentConvertUrl($res['article_content']);
        $res['createTime'] = date('Y-m-d', $res['createTime']);
        //推荐商品
        $res['itemList'] = (new ArticleItem)->getList($param['id']);
        //推荐文章
	    $res['recommend'] = convertUrl(objectToArray(DB::select("SELECT id, title, pic from article where id <> {$param['id']} order by rand() limit 5")));
	    returnJson(1, 'success', $res);
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

