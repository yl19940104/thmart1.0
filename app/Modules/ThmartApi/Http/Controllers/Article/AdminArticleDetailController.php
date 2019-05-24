<?php
namespace App\Modules\ThmartApi\Http\Controllers\Article;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Article;
use App\Modules\ThmartApi\Models\ArticleItem;
use Illuminate\Http\Request;

class AdminArticleDetailController extends Controller
{ 
    public function __construct(){}
    
	public function index(Request $request)
	{
		$param = $request->input();
		$article = new Article;
		if (!$res = $article->getDetail($param['id'])) returnJson(0, '文章id不存在');
        $res['createTime'] = date('Y-m-d', $res['createTime']);
        //推荐商品
        $res['itemList'] = (new ArticleItem)->getList($param['id']);
	    returnJson(1, 'success', $res);
	}
}

