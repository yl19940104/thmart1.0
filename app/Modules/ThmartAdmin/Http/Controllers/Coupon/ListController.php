<?php
namespace App\Modules\ThmartAdmin\Http\Controllers\Coupon;

use App\Modules\ThmartAdmin\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Modules\ThmartApi\Models\ItemSalePrice;

class ListController extends Controller
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
        $condition = [];
        /*$condition = [['coupon.startTime', '<=', time()], ['coupon.endTime', '>=', time()]];*/
        /*if (isset($param['id'])) {
            $condition['item.goodsNumber'] = $param['id'];
            $condition2['item.goodsNumber'] = $param['id'];
        }
        if (isset($param['skuId'])) {
            $condition['sku.skuNumber'] = $param['skuId'];
            $condition2['sku.skuNumber'] = $param['skuId'];
        }
        if (isset($param['title'])) {
            $condition['item.title'] = $param['title'];
            $condition2['item.title'] = $param['title'];
        }
        //仅显示上架商品
        if (isset($param['onlySalePrice']) && $param['onlySalePrice'] == 'on') {
            $condition['item.audited'] = 1;
            $condition2['item.autited'] = 1;
        }
        //如果仅显示有促销价的商品，那么去除搜索条件无促销价的情况
        if(isset($param['onlySalePrice']) && $param['onlySalePrice'] == 'on') $condition2 = []; */
        $res = DB::table('coupon')
            ->select('coupon.id', 'coupon.name', 'coupon.type', 'coupon.startTime', 'coupon.endTime', 'coupon.amount', 'coupon.over', 'coupon.reduce', 'coupon.isOverLay', 'coupon.pic')
            ->where($condition)
            ->orderby('coupon.id', 'desc')
            ->paginate($pageSize);
        foreach ($res as $k => &$v) {
            $v->startTime = date('Y-m-d H:i:s', $v->startTime);
            $v->endTime = date('Y-m-d H:i:s', $v->endTime);
        }
        unset($v);
        $compactData['data'] = $res;
        $compactData['get'] = $request->input();
        return view('thmartAdmin::Coupon/list', compact('compactData'));
    }
}

