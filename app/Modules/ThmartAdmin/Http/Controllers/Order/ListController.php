<?php
namespace App\Modules\ThmartAdmin\Http\Controllers\Order;

use App\Modules\ThmartAdmin\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Modules\ThmartApi\Models\Order;

class ListController extends Controller
{ 

    public function index(Request $request)
    {
        $param = $request->input();
        $condition = [];
        $whereInTwo['param'] = 'paySource';
        $whereInTwo['array'] = [0,1,2];
        $compactData = $this->compactData;
        $compactData['get'] = $param;
        /*session()->put('pageSize', ['username' => $res['0']['username'], 'id' => $res['0']['id'], 'roleArray' => $array]);
        session()->save();
        if (isset($compactData['get']['number']) && $compactData['get']['number']) {
            $pageSize = $compactData['get']['number'];
        } else {
            $pageSize = 10;
        }*/
        $pageSize = session()->get('pageSize');
        if (isset($compactData['get']['number']) && $compactData['get']['number']) {
            if ($compactData['get']['number'] == 'all') {
                $compactData['get']['number'] = DB::table('item')->where('isDelete', 0)->count();
            }
            session()->put('pageSize', $compactData['get']['number']);
            session()->save();
            $pageSize = session()->get('pageSize');
        } elseif (isset($pageSize) && $pageSize) {
            $pageSize = $pageSize;
        } else {
            $pageSize = 20;
        }
        //供应商id
        $supplierArray = [];
        $supplierGroup = DB::table('staff_supplier')->select('supplier_id')->where('staff_id', session()->get('userInfo')['id'])->get()->toArray();
        foreach ($supplierGroup as $v) {
            array_push($supplierArray, $v->supplier_id);
        }
        $whereIn['param'] = 'orders.isDelete';
        $whereIn['array'] = [0];
        //如果不是管理员或财务，则只能看到与自己绑定的供应商所对应的商品订单
        if (!in_array('1', $compactData['roleArray']) && !in_array('2', $compactData['roleArray'])) {
            $whereIn['param'] = 'item.shopId';
            $whereIn['array'] = $supplierArray;
        }
        $status = session()->get('status');
        if (isset($compactData['get']['status']) && $compactData['get']['status']) {
            session()->put('status', $compactData['get']['status']);
            session()->save();
            $condition['orders.status'] = session()->get('status');
            if (session()->get('status') == 1) {
                unset($condition['orders.status']);
                $whereInTwo['param'] = 'orders.status';
                $whereInTwo['array'] = [1,7];
            };
        } elseif (isset($status) && $status) {
            $condition['orders.status'] = $status;
            if (session()->get('status') == 1) {
                unset($condition['orders.status']);
                $whereInTwo['param'] = 'orders.status';
                $whereInTwo['array'] = [1,7];
            };
        }
        if (isset($condition['orders.status']) && $condition['orders.status'] == 5) unset($condition['orders.status']);
        if (isset($param['orderNumber'])) $condition['orders.orderNumber'] = $param['orderNumber'];
        if (isset($param['startTime'])) {
            $param['startTime'] = strtotime($param['startTime']);
            array_push($condition, ['orders.payTime', '>', $param['startTime']]);
            $compactData['get']['startTime'] = date('Y-m-d', $param['startTime']);
        }
        if (isset($param['endTime'])) {
            $param['endTime'] = strtotime($param['endTime']) + 86400;
            array_push($condition, ['orders.payTime', '<', $param['endTime']]);
            $compactData['get']['endTime'] = date('Y-m-d', $param['endTime']);
        }
        if (isset($param['supplierId'])) $condition['supplier.id'] = $param['supplierId'];
        if (isset($param['phone'])) $condition['orders.phone'] = $param['phone'];
        if (isset($param['categoryOne'])) $condition['category.name'] = $param['categoryOne'];
        if (isset($param['paySource'])) $condition['paySource'] = $param['paySource'];
        if (isset($param['email'])) $condition['email'] = $param['email'];
        if (isset($param['title'])) array_push($condition, ['item.title', 'like', "%{$param['title']}%"]);
        if (isset($param['subTitle'])) array_push($condition, ['item.subTitle', 'like', "%{$param['subTitle']}%"]);
        $compactData['orderList'] = DB::table('orderssku')
            ->select('orders.orderNumber', 'orders.status', 'feeTotal', 'priceTotal', 'category.title as categoryOne', 'item.goodsNumber', 'sku.skuNumber', 'orderssku.title', 'item.subTitle', 'sku.type', 'orderssku.number', 'orderssku.price', 'orderssku.costPrice', 'orderssku.skuPrice', 'orders.payTime', 'paySource', 'fullName', 'orders.phone', 'province', 'orders.email', 'buyerRemark', 'orders.status', 'supplier.supplier_name', 'brand.name as brandName', 'orderssku.logistics', 'orderssku.logisticsTime', 'staff.username', 'orderssku.id', 'item.type', 'orders.code', 'orders.userId')
            ->leftjoin('orders', 'orderssku.orderNumber', '=', 'orders.orderNumber')
            ->leftjoin('item', 'orderssku.goodsId', '=', 'item.id')
            ->leftjoin('category', 'item.categoryName', '=', 'category.name')
            ->leftjoin('sku', 'sku.id', '=', 'orderssku.skuId')
            ->leftjoin('staff', 'staff.id', '=', 'item.staff_id')
            ->leftjoin('supplier', 'supplier.id', '=', 'item.shopId')
            ->leftjoin('brand', 'brand.id', '=', 'item.brandName')
            ->where([['orders.status', '!=', 0], ['orders.id', '>', 3522], ['orders.status', '!=', 4], ['orders.status', '!=', 5]])
            ->where($condition)
            ->whereIn($whereInTwo['param'], $whereInTwo['array'])
            ->whereIn($whereIn['param'], $whereIn['array'])
            /*->whereIn('orders.isDelete', [0])*/
            ->orderby('orders.created_at', 'desc')
            ->paginate($pageSize);
        $result = objectToArray($compactData['orderList'], true);
        $compactData['total'] = $result['total'];
        foreach ($compactData['orderList'] as &$v) {
            $v->payTime = date('Y-m-d H:i:s', $v->payTime);
            $v->logisticsTime = date('Y-m-d H:i:s', $v->logisticsTime);
            $v->price *= 0.01;
            $v->costPrice *= 0.01;
            $v->feeTotal *= 0.01;
        }
        unset($v);
        $compactData['logistics'] = DB::table('logistics')
            ->select('id', 'company')
            ->get()
            ->toArray();
        $compactData['supplier'] = DB::table('supplier')
            ->select('id', 'supplier_name')
            ->where(['is_delete'=>0, 'is_effective'=>1])
            ->get()
            ->toArray();
        $compactData['categoryOne'] = DB::table('category')
            ->select('name', 'title')
            ->where(['fname'=>0])
            ->get()
            ->toArray();
        $compactData['pageSize'] = session()->get('pageSize');
        $compactData['status'] = session()->get('status');
        return view('thmartAdmin::Order/list', compact('compactData'));
    }
}

