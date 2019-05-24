<?php
namespace App\Modules\ThmartApi\Http\Controllers\Wx;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\ThmartApi\Models\Orders;
/**
 * Created by PhpStorm.
 * User: yl
 * Date: 2019/3/1
 * Time: 10:49
 */
class MiniProgramParamController extends Controller
{

    public function index(Request $request)
    {
        $validator = $this->validateParam($request);
        if ($validator->fails()) returnJson(0, $validator->getMessageBag());
        $param = $request->input();
        $orders = new Orders;
        $res = $orders->wxPay($param['orderNumber']);
        returnJson(1, 'success', $res);
    }

    public function validateParam(Request $request)
    {
        $validator = \Validator::make($request->input(), [
            'orderNumber' => 'required',
        ], [
            'required' => ':attribute 为必填项',
        ], [
            'orderNumber' => '订单',
        ]);
        return $validator;
    }
}