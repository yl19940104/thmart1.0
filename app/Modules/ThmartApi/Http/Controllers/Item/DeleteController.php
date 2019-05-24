<?php
namespace App\Modules\ThmartApi\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use App\Modules\ThmartApi\Models\Category;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeleteController extends Controller
{ 

    public function __construct(){}

    public function index(Request $request)
    {
        $validator = $this->validateParam($request);
        if ($validator->fails()) {
            return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
        }
        $param = $request->input();
        (new Item)->where(['id'=>$param['id']])->update(['isDelete'=>1]);
        $res = Item::select('categoryName', 'categoryTwoName', 'audited')->where('id', $param['id'])->get();
        //删除商品时如果该商品已上架，那么商品所在的一级二级分类下的上架未删除商品数目分别减一
        if ($res[0]->audited == 1) {
            Category::where('name', $res[0]->categoryName)->decrement('itemNumber');
            Category::where('name', $res[0]->categoryTwoName)->decrement('itemNumber');
        }
        returnJson(1, 'success');
    }

    private function validateParam(Request $request)
    {
        $validator = \Validator::make($request->input(), [
            'id' => 'required|integer',
        ], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
        ], [
            'id' => '商品id',
        ]);
        return $validator;
    }
}

