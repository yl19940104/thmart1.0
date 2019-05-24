<?php
namespace App\Modules\ThmartApi\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Item;
use Illuminate\Http\Request;

/*
 * 后台上传文章时获取商品信息
 */
class ItemListController extends Controller
{ 

	public function __construct(){}

	public function index(Request $request)
	{
		$res = (new Item)->getItemIdTitleList();
        returnJson(1, 'success', $res);
	}
}

