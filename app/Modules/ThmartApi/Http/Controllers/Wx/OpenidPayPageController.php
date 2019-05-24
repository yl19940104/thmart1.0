<?php
namespace App\Modules\ThmartApi\Http\Controllers\Wx;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Orders;
use Illuminate\Http\Request;
use Illuminate\Payment\Wxpaylib\JsApiPay;
use Illuminate\Payment\Wxpaylib\WxPayUnifiedOrder;
use Illuminate\Payment\Wxpaylib\NativePay;
use Illuminate\Payment\Wxpaylib\Phpqrcode;
use Illuminate\Payment\Wxpaylib\WxPayApi;
use Illuminate\Payment\Wxpaylib\WxPayConfig;
use Illuminate\Payment\Wxpaylib\WxPayException;
use Illuminate\Payment\Wxpaylib\WxPayNotify;


class OpenidPayPageController extends Controller
{ 
    public function __construct(){}

    public function index(Request $request)
    {
        $param = $request->input();
        if (adminDomain() == 'http://api.mall.thatsmags.com') {
            $domain = 'http://mob.thmart.com.cn';
        } else {
            $domain = 'http://proj6.thatsmags.com';
        }
        /*file_put_contents("testt.txt", var_export($orderid, TRUE));*/
        $res = (new Orders)->getOne($param['orderNumber']);
        /*$jsApiParameters = wxCardPay('ThMart', $res['0']['priceTotal'], $res['0']['orderNumber']);*/
        $tools = new JsApiPay();
        $openId2 = $tools->GetOpenid();
        $input2 = new WxPayUnifiedOrder();
        $input2->SetBody('ThMart');
        $input2->SetAttach("test");
        $input2->SetOut_trade_no($res['0']['orderNumber']);
        $input2->SetTotal_fee($res['0']['priceTotal']*100);
        /*$input2->SetTotal_fee(1);*/
        $input2->SetTime_start(date("YmdHis"));
        $input2->SetTime_expire(date("YmdHis", time() + 600));
        $input2->SetGoods_tag("tag");
        $url = adminDomain().'/thmartApi/Wx/notify';
        /*$url = 'http://proj7.thatsmags.com/Api/Test/txttest';*/
        $input2->SetNotify_url($url);
        $input2->SetTrade_type("JSAPI");
        $input2->SetOpenid($openId2);
        $api = new WxPayApi();
        $order = $api->unifiedOrder($input2);
        $jsApiParameters = $tools->GetJsApiParameters($order);
        $jsApiParameters = json_decode($jsApiParameters, true);
        $jsApiParameters = http_build_query($jsApiParameters);
        $jsApiParameters = str_ireplace('prepay_id%3D', 'prepay_id', $jsApiParameters);
        /*returnJson(1, $jsApiParameters);*/
        return redirect($param['callbackAddress']."?orderNumber=".$param['orderNumber']."&".$jsApiParameters);
    }
}

 