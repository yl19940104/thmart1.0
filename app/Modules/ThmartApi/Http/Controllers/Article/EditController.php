<?php
namespace App\Modules\ThmartApi\Http\Controllers\Article;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Article;
use App\Modules\ThmartApi\Models\ArticleContent;
use App\Modules\ThmartApi\Models\ArticleItem;
use Illuminate\Http\Request;

class EditController extends Controller
{ 
	public function __construct(){}
	
	public function index(Request $request)
	{
		$param = $request->input();
		$validator = $this->validateParam($request);
		if ($validator->fails()) returnJson(0, $validator->getMessageBag());
		$article = new Article();
		$articleContent = new ArticleContent();
		$content = $param['article_content'];
		unset($param['article_content']);
	    if (!isset($param['id'])) {
	    	$param['createTime'] = time();
	    	$res = $article->addOne($param); 
            $articleContent->addOne(['article_id'=>$res['id'], 'article_content'=>$content]);
            if (isset($param['itemIdList']) && $param['itemIdList']) {
            	$this->addArticleItem($res['id'], $param['itemIdList']);
            }
            returnJson(1, '添加成功');
	    } elseif(!$article->getOne($param['id'])) {
            returnJson(0, '该文章不存在');
	    } else {
	    	$data = $param['itemIdList'];
	    	unset($param['itemIdList']);
            $article->saveOne($param);
            $articleContent->saveOne(['article_id'=>$param['id'], 'article_content'=>$content]);
            (new ArticleItem)->deleteList($param['id']);
            if (isset($data) && $data) {
            	$this->addArticleItem($param['id'], $request->input('itemIdList'));
            }
            returnJson(1, '更新成功');
	    }
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'id'              => 'integer',
		    'title'           => 'required',
		    'pic'             => 'required',
            'description'     => 'required',
            'article_content' => 'required',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
		], [
            'id'              => '文章id',
		    'title'           => '标题',
		    'pic'             => '文章封面图',
		    'description'     => '描述',
		    'article_content' => '文章内容',
		]);
		return $validator;
	}

	public function addArticleItem($articleId, $itemIdList)
	{
		$data = [];
    	foreach ($itemIdList as $v) {
    		array_push($data, ['articleId'=>$articleId, 'itemId'=>$v]);            		
    	}
    	(new ArticleItem)->insertList($data);
	}
}

