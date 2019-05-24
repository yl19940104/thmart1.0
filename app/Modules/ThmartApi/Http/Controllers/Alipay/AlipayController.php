<?php
namespace App\Modules\ThmartApi\Http\Controllers\Alipay;
use App\Http\Controllers\Controller;
use Illuminate\Payment\Alipaylib\wappay\buildermodel\AlipayTradeWapPayContentBuilder;
use Illuminate\Payment\Alipaylib\wappay\service\AlipayTradeService;
use Illuminate\Payment\Alipaylib\wappay\buildermodel\AlipayTradeQueryContentBuilder;
use Illuminate\Payment\Alipaylib\Alipaycore;
use Illuminate\Payment\Alipaylib\Alipaymd5;
use Illuminate\Payment\Alipaylib\AlipayNotify;
use Illuminate\Payment\Alipaylib\AlipaySubmit;
use App\Modules\ThmartApi\Models\Orders;
use Illuminate\Http\Request;

class AlipayController extends Controller {
    public function __construct(){}

    public function alipayapi(Request $request) {
        $validator = $this->validateParam($request);
        if ($validator->fails()) {
            return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
        }
        $param = $request->input();
        $res = (new Orders)->getOne($param['orderNumber']);
        /*returnJson(1, 123);*/
        $wapalipay = config('config.wapalipay');
        $timeout_express = "1m";
        $payRequestBuilder = new AlipayTradeWapPayContentBuilder();
        $payRequestBuilder->setSubject('thatsmart');
        $payRequestBuilder->setOutTradeNo($param['orderNumber']);
        $payRequestBuilder->setTotalAmount($res['0']['priceTotal']);
        $payRequestBuilder->setTimeExpress($timeout_express);
        $payResponse = new AlipayTradeService($wapalipay);
        $result = $payResponse->wapPay($payRequestBuilder, $wapalipay['return_url'], $wapalipay['notify_url']);
        return $result;
    }  

    public function alipayapiPc(Request $request) {  
        $validator = $this->validateParam($request);
        if ($validator->fails()) {
            return response()->json(['code' => "0", 'message' => $validator->getMessageBag()]);
        }
        $param = $request->input();
        $res = (new Orders)->getOne($param['orderNumber']);
        $subject = 'thatsmart';
        $total_fee = $res['0']['priceTotal'];
        $parameter = array(
            "service" => config('config.webalipay.service'),  
            "partner" => config('config.webalipay.partner'),    
            "seller_id" => config('config.webalipay.seller_id'),  
            "payment_type"  => config('config.webalipay.payment_type'), 
            "notify_url" => config('config.webalipay.notify_url'), 
            "return_url" => config('config.webalipay.return_url'),
            "anti_phishing_key" => config('config.webalipay.anti_phishing_key'),
            "exter_invoke_ip" => config('config.webalipay.exter_invoke_ip'),
            "out_trade_no" => $param['orderNumber'],  
            "subject" => $subject,  
            "total_fee" => $total_fee,  
            "body" => 'thmart',   
            "_input_charset" => config('config.webalipay.input_charset')  
        ); 
        $parameter['return_url'] = $request->input('url');
        $config['partner'] = config('config.webalipay.partner');
        $config['seller_id'] = config('config.webalipay.seller_id');
        $config['key'] = 'agh36vdil1cwuaecr0bmbpjbxksabafd';
        $config['notify_url'] = config('config.webalipay.notify_url');
        $config['return_url'] = config('config.webalipay.return_url');
        $config['sign_type'] = strtoupper('MD5');
        $config['input_charset'] = config('config.webalipay.input_charset')  ;
        $config['cacert'] = '';
        $config['transport'] = 'http';
        $config['payment_type'] = config('config.webalipay.payment_type');
        $config['service'] = config('config.webalipay.service');
        $config['anti_phishing_key'] = config('config.webalipay.anti_phishing_key');
        $config['exter_invoke_ip'] = config('config.webalipay.exter_invoke_ip');
        $alipaySubmit = new AlipaySubmit($config);  
        $html_text = $alipaySubmit->buildRequestForm($parameter,"post", "确认");  
        echo $html_text;
    }  
    public function notifyurl(Request $request)
    {
        $param = $request->input();
        $wapalipay = config('config.wapalipay');
        $alipaySevice = new AlipayTradeService($wapalipay);
        $alipaySevice->writeLog(var_export($param,true));
        $result = $alipaySevice->check($param);
        if ($result) {
            $out_trade_no = $param['out_trade_no'];
            $trade_no = $param['trade_no'];
            $trade_status = $param['trade_status'];
            if ($param['trade_status'] == 'TRADE_FINISHED') {
            } else if ($param['trade_status'] == 'TRADE_SUCCESS') {
                (new Orders)->payNotify($param['out_trade_no'], 2);
            }
            echo "success";
        } else {
            echo "fail";
        }
    }
    public function returnurl(Request $request)
    {  
        $param = $request->input();
        $wapalipay = config('config.wapalipay');
        $alipaySevice = new AlipayTradeService($wapalipay); 
        $result = $alipaySevice->check($param);
        if($result) {//验证成功
            $out_trade_no = htmlspecialchars($param['out_trade_no']);
            //支付宝交易号
            $trade_no = htmlspecialchars($param['trade_no']);
            echo "验证成功<br />外部订单号：".$out_trade_no;
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
        } else {
            echo "验证失败";
        }
    }
    public function orderQuery(Request $request)
    {  
        $param = $request->input();
        if (!empty($request->input('out_trade_no')) || !empty($request->input('trade_no'))){
            $out_trade_no = trim($request->input('out_trade_no'));
            $trade_no = trim($request->input('trade_no'));
            $RequestBuilder = new AlipayTradeQueryContentBuilder();
            $RequestBuilder->setTradeNo($request->input('trade_no'));
            $RequestBuilder->setOutTradeNo($request->input('out_trade_no'));
            $wapalipay = config('config.wapalipay');
            $Response = new AlipayTradeService($wapalipay);
            $result = $Response->Query($RequestBuilder);
            if ($result->msg == 'Success') {
                $res = (new Orders)->getOne($param['out_trade_no']);
                //状态等于5可能是异步操作还未来得及更新订单状态
                if ($res['0']['status'] == 5 || $res['0']['status'] == 6 || $res['0']['status'] == 7) returnJson(1, 'success', 'spell');
                returnJson(1, 'success', 'paid');
            } else {
                returnJson(116, 'order is not paied');
            }
        }
    }
    public function notifyurlPc(Request $request)
    {
        $param = $request->input();
        $config['partner'] = config('config.webalipay.partner');
        $config['seller_id'] = config('config.webalipay.seller_id');
        $config['key'] = 'agh36vdil1cwuaecr0bmbpjbxksabafd';
        $config['notify_url'] = config('config.webalipay.notify_url');
        $config['return_url'] = config('config.webalipay.return_url');
        $config['sign_type'] = strtoupper('MD5');
        $config['input_charset'] = config('config.webalipay.input_charset')  ;
        $config['cacert'] = '';
        $config['transport'] = 'http';
        $config['payment_type'] = config('config.webalipay.payment_type');
        $config['service'] = config('config.webalipay.service');
        $config['anti_phishing_key'] = config('config.webalipay.anti_phishing_key');
        $config['exter_invoke_ip'] = config('config.webalipay.exter_invoke_ip');
        $alipayNotify = new AlipayNotify($config);
        $verify_result = $alipayNotify->verifyNotify($param);
        file_put_contents('123.txt', var_export($verify_result, true));
        if ($verify_result) {
            file_put_contents('456.txt', var_export($verify_result, true));
            $out_trade_no = $param['out_trade_no'];
            $trade_no = $param['trade_no'];
            $trade_status = $param['trade_status'];
            if ($param['trade_status'] == 'TRADE_FINISHED') {
            } else if ($param['trade_status'] == 'TRADE_SUCCESS') {
                (new Orders)->payNotify($param['out_trade_no'], 2);
            }
            echo "success";     //请不要修改或删除
        }
        else {
            echo "fail";
        }
    }
    public function returnurlPc(Request $request)
    {  
        $param = $request->input();
        $config['partner'] = config('config.webalipay.partner');
        $config['seller_id'] = config('config.webalipay.seller_id');
        $config['key'] = 'agh36vdil1cwuaecr0bmbpjbxksabafd';
        $config['notify_url'] = config('config.webalipay.notify_url');
        $config['return_url'] = config('config.webalipay.return_url');
        $config['sign_type'] = strtoupper('MD5');
        $config['input_charset'] = config('config.webalipay.input_charset')  ;
        $config['cacert'] = '';
        $config['transport'] = 'http';
        $config['payment_type'] = config('config.webalipay.payment_type');
        $config['service'] = config('config.webalipay.service');
        $config['anti_phishing_key'] = config('config.webalipay.anti_phishing_key');
        $config['exter_invoke_ip'] = config('config.webalipay.exter_invoke_ip');
        $alipayNotify = new AlipayNotify($config);
        $verify_result = $alipayNotify->verifyNotify($param);
        if ($verify_result) {
            $url = $request->input('url');
            return redirect($url);     //请不要修改或删除
            /*return redirect('http://www.baidu.com');*/
        } else {
            echo 'false';
        }
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
