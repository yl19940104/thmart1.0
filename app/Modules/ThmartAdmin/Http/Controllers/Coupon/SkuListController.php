<?php
namespace App\Modules\ThmartAdmin\Http\Controllers\Coupon;

use App\Modules\ThmartAdmin\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Modules\ThmartApi\Models\CouponSku;

class SkuListController extends Controller
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
        $res = DB::table('couponSku')
            ->select('couponSku.skuNumber', 'couponSku.title', 'sku.price', 'couponSku.id', 'itemSalePrice.salePrice')
            ->leftjoin('sku', 'sku.id', '=', 'couponSku.skuId')
            ->leftjoin('itemSalePrice', 'sku.id', '=', 'itemSalePrice.skuId')
            ->where(['couponSku.couponId'=>$param['couponId']])
            ->orderby('couponSku.id', 'desc')
            ->paginate($pageSize);
        foreach ($res as &$v) {
            $v->price *= 0.01;
            $v->salePrice *= 0.01;
            $data = DB::table('itemSalePrice')
                ->select('salePrice')
                ->where(['skuNumber'=>$v->skuNumber, ['itemSalePrice.startTime', '<=', time()], ['itemSalePrice.endTime', '>=', time()], 'itemSalePrice.type'=>1])
                ->get()
                ->toArray();
            //如果有促销价显示促销价，无促销价则显示空
            if (!$data) {
                $v->salePrice = '';
            } else {
                $v->salePrice = $data['0']->salePrice * 0.01;
            }
        }
        unset($v);
        $compactData['data'] = $res;
        return view('thmartAdmin::Coupon/skuList', compact('compactData'));
    }
}

