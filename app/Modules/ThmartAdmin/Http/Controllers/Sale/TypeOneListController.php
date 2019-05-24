<?php
namespace App\Modules\ThmartAdmin\Http\Controllers\Sale;

use App\Modules\ThmartAdmin\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Modules\ThmartApi\Models\ItemSalePrice;

class TypeOneListController extends Controller
{ 

    public function index(Request $request)
    {
        $compactData = $this->compactData;
        $param = $request->input();
        if (session()->get('pageSize')) {
            $pageSize = session()->get('pageSize');
        } else {
            $pageSize = 10;
        }
        if (isset($param) && $param) {
            //搜索条件有促销价的情况,只要未结束的促销价都可以搜索到
            $condition = ['sku.isDelete'=>0, ['endTime', '>=', time()], 'item.isDelete'=>0, 'item.audited'=>1];
            //搜索条件无促销价的情况
            $condition2 = ['sku.isDelete'=>0, 'itemSalePrice.salePrice'=>null, 'item.isDelete'=>0, 'item.audited'=>1];
            if (isset($param['id'])) {
                $condition['item.id'] = $param['id'];
                $condition2['item.id'] = $param['id'];
            }
            if (isset($param['skuId'])) {
                $condition['sku.skuNumber'] = $param['skuId'];
                $condition2['sku.skuNumber'] = $param['skuId'];
            }
            if (isset($param['title'])) {
                array_push($condition, ['item.title', 'like', "%{$param['title']}%"]);
                array_push($condition2, ['item.title', 'like', "%{$param['title']}%"]);
            }
            if (isset($param['brandId'])) {
                $condition['item.brandName'] = $param['brandId'];
                $condition2['item.brandName'] = $param['brandId'];
            }
            if (isset($param['categoryOne'])) {
                $condition['item.categoryName'] = $param['categoryOne'];
                $condition2['item.categoryName'] = $param['categoryOne'];
            }
            //仅显示上架商品
            if (isset($param['onlySalePrice']) && $param['onlySalePrice'] == 'on') {
                $condition['item.audited'] = 1;
                $condition2['item.autited'] = 1;
            }
            //如果仅显示有促销价的商品，那么去除搜索条件无促销价的情况
            if(isset($param['onlySalePrice']) && $param['onlySalePrice'] == 'on') $condition2 = []; 
            $res = DB::table('sku')
                ->select('itemSalePrice.id', 'item.id as goodsNumber', 'sku.skuNumber', 'startTime', 'endTime', 'category.title as categoryTitle', 'item.title', 'item.subTitle', 'sku.price', 'itemSalePrice.salePrice', 'itemSalePrice.type', 'brand.name as brandName')
                ->leftjoin('itemSalePrice', 'sku.id', '=', 'itemSalePrice.skuId')
                ->leftjoin('item', 'item.id', '=', 'sku.itemId')
                ->leftjoin('category', 'category.name', '=', 'item.categoryName')
                ->leftjoin('brand', 'brand.id', '=', 'item.brandName')
                ->where($condition)
                ->orWhere(function ($query) use ($condition2) {
                    $query->where($condition2);
                })
                ->orderby('skuNumber', 'desc')
                ->paginate($pageSize);
            foreach ($res as $k => &$v) {
                $v->startTime = date('Y-m-d H:i:s', $v->startTime);
                $v->endTime = date('Y-m-d H:i:s', $v->endTime);
                $v->price *= 0.01;
                $v->salePrice *= 0.01;
                //如果该sku没有促销价，那么促销价设为空,开始时间为空，结束时间为空
                if ($v->salePrice == 0) {
                    $v->salePrice = '';
                    $v->startTime = '';
                    $v->endTime = '';
                }
                //如果该sku有团购价和拼单价，那么清除团购价和拼单价数据
                if ($v->type != 1) {
                    $v->id = null;
                    $v->startTime = null;
                    $v->endTime = null;
                    $v->endTime = null;
                    $v->salePrice = null;
                }
            }
            unset($v);
            $compactData['data'] = $res;
        }
        $compactData['get'] = $request->input();
        $compactData['brand'] = DB::table('brand')->select('name', 'id')->where(['isDelete'=>0, 'status'=>'1'])->get()->toArray();
        $compactData['categoryOne'] = DB::table('category')->select('name', 'title')->where(['isDelete'=>0])->get()->toArray();
        return view('thmartAdmin::Sale/typeOneList', compact('compactData'));
    }
}

