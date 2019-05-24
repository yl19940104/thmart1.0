<?php
namespace App\Modules\ThmartApi\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Orders;
use Illuminate\Http\Request;
use Illuminate\Payment\Wxpaylib\NativePay;
use Illuminate\Payment\Wxpaylib\WxPayUnifiedOrder;

class PayOrderDetailController extends Controller
{ 

	public function index(Request $request)
	{
		$validator = $this->validateParam($request);
		if ($validator->fails()) {
			return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
		}
		$param = $request->input();
	    $res = (new Orders)->getOne($param['orderNumber']);
	    if ($res['0']['userId'] != $this->userId) returnJson(0, 'wrong userId'); 
	    $res['0']['orderTime'] = date('Y-m-d H:i:s', $res['0']['orderTime']);
	    //微信扫码支付参数
	    $notify = new NativePay();
        $input = new WxPayUnifiedOrder();
        $input->SetBody('Thmart');
        $input->SetAttach("attach");
        $input->SetOut_trade_no($param['orderNumber']);
        $input->SetTotal_fee($res['0']['priceTotal'] * 100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("tag");
        $input->SetNotify_url(config('config.wxNotifyUrl'));
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id($param['orderNumber']);
        $result = $notify->GetPayUrl($input);
        /*returnJson(1, 1, $result);*/
        if (isset($result["code_url"])) $res['0']['jsApiParameters'] = urlencode($result["code_url"]);
	    returnJson(1, 'success', $res['0']);
	}

	public function validateParam(Request $request)
	{
		$validator = \Validator::make($request->input(), [
		    'orderNumber' => 'required',
		], [
            'required' => ':attribute 为必填项',
		], [
            'orderNumber' => '订单编号',
		]);
		return $validator;
	}
}

  