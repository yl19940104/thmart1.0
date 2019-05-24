<?php
namespace App\Modules\ThmartApi\Http\Controllers\Wx;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Orders;
use Illuminate\Http\Request;
use Illuminate\Payment\Wxpaylib\JsApiPay;
use Illuminate\Payment\Wxpaylib\WxPayDataBase;
use Illuminate\Payment\Wxpaylib\WxPayOrderQuery;
use Illuminate\Payment\Wxpaylib\WxPayApi;
use Illuminate\Payment\Wxpaylib\QRcode;

class WxController extends Controller
{
    public function __construct(){}
    
    public function qrcode() {
        error_reporting(E_ERROR);
        $url = urldecode($_GET["data"]);
        $qrcode = new QRcode();
        $qrcode->png($url);
    }

    public function notify(Request $request) {
        $xml = $request->getContent();
        $wxdata = new WxPayDataBase();
        $result = $wxdata->FromXml($xml);
        if (array_key_exists('result_code', $result) && $result['result_code'] == 'SUCCESS') {
            $sign2 = $result['sign'];
            unset($result['sign']);  
            $sign = $wxdata->MakeSign($result);
            if ($sign == $sign2) {
                (new Orders)->payNotify($result['out_trade_no'], 1);
            }
        }
    }
    
    public function orderQuery(Request $request) {
        $param = $request->input();
        if(isset($param["trade_no"]) && $param["trade_no"] != ""){
            $trade_no = $param["trade_no"];
            $input = new WxPayOrderQuery();
            $input->SetOut_trade_no($trade_no);
            $api = new WxPayApi();
            $result  = $api->orderQuery($input);
            if (isset($result["trade_state"]) && $result["trade_state"] == "SUCCESS" ) {
                $res = (new Orders)->getOne($param["trade_no"]);
                if ($res['0']['status'] == 6) returnJson(121, 'success');
                returnJson(1, 'success');
            } else {
                returnJson(116, 'order is not paied');
            }
        }
    }

    public function wxShare() {
        /*$post = I('post.');
        $post['url'] = htmlspecialchars_decode($post['url']);*/
        /*str_replace('&from=singlemessage', '', $post['url']);*/
        $post = $_POST;
        if ($post['goods_id']) {
            $res = M('mt_goods')->field('goods_name, coverpic')
                ->where(array('id'=>$post['goods_id']))
                ->find();
            $res['coverpic'] = adminDomain().$res['coverpic'];
            $result = array(
                'title'  => $res['goods_name'],
                'imgUrl' => $res['coverpic'],
                'desc'   => "thMart-That's making your life easier.",
            );
        } elseif ($post['cat_id']) {
            $res = M('mt_goods_cats')->field('name')->where(array('id'=>$post['cat_id']))->find();
            $result = array(
                'title'  => $res['name'],
                'imgUrl' => 'http://api.mall.thatsmags.com/Public/ckfinder/images/thlogo.jpg',
                'desc'   => "thMart-That's making your life easier.",
            );
        } elseif ($post['merchant_id']) {
            $res = M('mt_merchant')->field('merchant_name, coverpic')
                ->where(array('id'=>$post['merchant_id']))
                ->find();
            if ($post['merchant_id'] != 32) {
                $title = $res['merchant_name'];
                $imgUrl = adminDomain().$res['coverpic'];
            } else {
                $title = 'thMart - Hot Deals at Fairmont Peace Hotel';
                $imgUrl = adminDomain().$res['coverpic'];
            }
            $result = array(
                'title'  => $title,
                'imgUrl' => $imgUrl,
                'desc'   => "thMart-That's making your life easier.",
            );
        } else {
            $result = array(
                'title'  => "thMart-That's making your life easier.",
                'imgUrl' => 'http://api.mall.thatsmags.com/Public/ckfinder/images/thlogo.jpg',
                'desc'   => 'Your one-stop online shopping site!',
            );
        }
        import('Vendor.Wxshare.Jssdk');
        $jssdk = new \Vendor\Wxshare\Jssdk($post['url']);
        $signPackage = $jssdk->getSignPackage();
        $wxconfig = wx_share_init();
        $data = array(
            'signPackage' => $signPackage,
            'res'         => $result,
        );
        /*var_dump($data['signPackage']);
        $this->assign('signPackage', $signPackage);
        $this->display('wxShare');*/
        o(1, 'success', $data);
    }
}

