<?php
namespace App\Modules\ThmartApi\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Item;
use Illuminate\Http\Request;

class HotProductsController extends Controller
{ 
    public function __construct(){}

	public function index(Request $request)
	{
		$param = $request->input();
		$data = (new Item)->getMinSkulist('and item.isDelete = 0 and item.audited = 1 and sku.isDelete = 0 and item.categoryName != 164 and item.categoryName != 1', 'id desc', null, 'limit 144');
		$res = pageData($data, $param['page'], $param['pageSize']);
		returnJson(1, 'success', $res);
	}
}

