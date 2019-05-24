<?php
namespace App\Modules\ThmartAdmin\Http\Controllers\Item;

use App\Modules\ThmartAdmin\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Modules\ThmartApi\Models\Supplier;
use App\Modules\ThmartApi\Models\Item;

class ListController extends Controller
{ 

    public function index(Request $request)
    {
        $param = $request->input();
        $compactData = $this->compactData;
        $compactData['get'] = $param;
        $pageSize = session()->get('pageSize');
        if (isset($compactData['get']['pageSize']) && $compactData['get']['pageSize']) {
            if ($compactData['get']['pageSize'] == 'all') {
                $compactData['get']['pageSize'] = DB::table('item')->where('isDelete', 0)->count();
            }
            session()->put('pageSize', $compactData['get']['pageSize']);
            session()->save();
            $pageSize = session()->get('pageSize');
        } elseif (isset($pageSize) && $pageSize) {
            $pageSize = $pageSize;
        } else {
            $pageSize = 20;
        }
        $supplierArray = [];
        $supplierGroup = DB::table('staff_supplier')->select('supplier_id')->where('staff_id', session()->get('userInfo')['id'])->get()->toArray();
        foreach ($supplierGroup as $v) {
            array_push($supplierArray, $v->supplier_id);
        }
        $whereIn['param'] = 'item.isDelete';
        $whereIn['array'] = [0];
        //如果是管理员，可以看到所有的商品
        $condition = [];
        //如果不是管理员，则只能看到与自己绑定的供应商所对应的商品
        if (!in_array('1', $compactData['roleArray'])) {
            $whereIn['param'] = 'item.shopId';
            $whereIn['array'] = $supplierArray;
        }
        unset($param['page']);
        if (isset($param['title'])) array_push($condition, ['item.title', 'like', "%{$param['title']}%"]);
        if (isset($param['subTitle'])) array_push($condition, ['item.subTitle', 'like', "%{$param['subTitle']}%"]);
        if (isset($param['categoryName'])) $condition['categoryName'] = $param['categoryName'];
        if (isset($param['supplier'])) $condition['supplier.id'] = $param['supplier'];
        if (isset($param['id'])) $condition['item.id'] = $param['id'];
        if (isset($param['audited'])) $condition['item.audited'] = $param['audited'];
        $compactData['itemData'] = DB::table('item')->select('item.id', 'item.title', 'subTitle', 'category.title as categoryTitle', 'brand.name as brandName', 'supplier.supplier_name', 'supplier.id as supplier', 'categoryName as categoryOne', 'categoryTwoName as categoryTwo', 'audited', 'categoryThreeName as categoryThree')
            ->leftjoin('category', 'category.name', '=', 'item.categoryName')
            ->leftjoin('brand', 'brand.id', '=', 'item.brandName')
            ->leftjoin('supplier', 'supplier.id', '=', 'item.shopId')
            ->where('item.isDelete', 0)
            ->where($condition)
            ->whereIn($whereIn['param'], $whereIn['array'])
            ->orderby('id', 'desc')
            ->paginate($pageSize);
        $compactData['get'] = $param;
        $compactData['catOneList'] = DB::table('category')->select('name', 'title')->where(['fname'=>0])->get()->toArray();
        $compactData['supplier'] = DB::table('supplier')->select('supplier_name', 'id')->where(['is_delete'=>0])->get()->toArray();
        $compactData['pageSize'] = $pageSize;
        $compactData['total'] = $compactData['itemData']['total'];
        $result = objectToArray($compactData['itemData'], true);
        $compactData['total'] = $result['total'];
        return view('thmartAdmin::Item/list', compact('compactData'));
    }
}

