<?php
namespace App\Modules\ThmartApi\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Item;
use App\Modules\ThmartApi\Models\Category;
use Illuminate\Http\Request;

class ChangeAuditedController extends Controller
{ 

	public function __construct(){}

	public function index(Request $request)
	{
        $param = $request->input();
        $res = Item::select('isDelete', 'audited', 'categoryName', 'categoryTwoName')->where('id', $param['id'])->get();
        //上下架修改所属一级二级分类下商品个数
        if ($res[0]->isDelete == 0 && $res[0]->audited == 1 && $param['audited'] == 2) {
            Category::where('name', $res[0]->categoryName)->decrement('itemNumber');
            Category::where('name', $res[0]->categoryTwoName)->decrement('itemNumber');
        }
        if ($res[0]->isDelete == 0 && $res[0]->audited == 2 && $param['audited'] == 1) {
            Category::where('name', $res[0]->categoryName)->increment('itemNumber');
            Category::where('name', $res[0]->categoryTwoName)->increment('itemNumber');
        }
		(new Item)->saveOne(['id'=>$param['id'], 'audited'=>$param['audited']]);
        returnJson(1, 'success');
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'itemId' => 'required|integer',
		], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
		], [
            'itemId' => '商品编号',
		]);
		return $validator;
	}
}

