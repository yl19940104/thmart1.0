<?php
namespace App\Modules\ThmartApi\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Orders;
use App\Modules\ThmartApi\Models\OrdersSku;
use App\Modules\ThmartApi\Models\Sku;
use Illuminate\Http\Request;

class OrdersSkuDetailController extends Controller
{

    public function index(Request $request)
    {
        $param = $request->input();
        $validator = $this->validateParam($request);
        if ($validator->fails()) returnJson(0, $validator->getMessageBag());
        $res = OrdersSku::select('id', 'pic', 'title as goodsName', 'skuPropName as prop', 'price', 'number', 'skuPrice', 'brandName')->where('id', $param['id'])->get()->toArray();
        foreach ($res as &$v) {
            $v['pic'] = adminDomain().$v['pic'];
            $v['price'] *= 0.01;
            $v['skuPrice'] *= 0.01;
            $v['prop'] = json_decode($v['prop'], true);
        }
        returnJson(1, 'success', ['brandName'=>$res['0']['brandName'], 'data'=>[$res['0']]]);
    }

    public function validateParam(Request $request)
    {
        $validator = \Validator::make($request->input(), [
            'id'       => 'required|integer',
        ], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
        ], [
            'id'       => '订单skuId',
        ]);
        return $validator;
    }
}

