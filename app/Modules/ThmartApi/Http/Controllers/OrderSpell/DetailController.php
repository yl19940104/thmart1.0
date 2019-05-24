<?php
namespace App\Modules\ThmartApi\Http\Controllers\OrderSpell;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\OrdersSpell;
use App\Modules\ThmartApi\Models\Item;
use App\Modules\ThmartApi\Models\Sku;
use Illuminate\Http\Request;

class DetailController extends Controller
{

    public function __construct(){}

    public function index(Request $request)
    {
        $data = [
            'currentTime' => time(),
            'headimgurl_array' => [],
        ];
        $param = $request->input();
        $ordersSpell = new OrdersSpell();
        $item = new Item();
        $res = $ordersSpell->select('ordersSpell.created_at', 'user_info.headimg_url', 'pid', 'ordersSpell.id', 'ordersSpell.itemId')->where(['ordersSpell.orderNumber'=>$param['orderNumber']])->leftjoin('user_info', 'ordersSpell.userId', '=', 'user_info.id')->first()->toArray();
        //如果该订单是发起拼单
        if ($res['pid'] == 0) {
            $data['number_left'] = $ordersSpell->getLeft($res['id']);
            $data['headimgurl_array'][] = adminDomain().$res['headimg_url'];
            $data['endTime'] = strtotime($res['created_at']) + 172800;
            if ($data['endTime'] < time()) returnJson(1, 'the spell is expired', 1);
            $result = $ordersSpell->select('user_info.headimg_url')->leftjoin('user_info', 'ordersSpell.userId', '=', 'user_info.id')->where(['ordersSpell.status'=>'1', 'ordersSpell.pid'=>$res['id']])->get()->toArray();
            foreach ($result as $v) {
                $data['headimgurl_array'][] = adminDomain().$v['headimg_url'];
            }
        //如果该订单是拼别人的单
        } else {
            $data['number_left'] = $ordersSpell->getLeft($res['pid']);
            $result_one = $ordersSpell->select('ordersSpell.created_at', 'headimg_url')->leftjoin('user_info', 'ordersSpell.userId', '=', 'user_info.id')->where(['ordersSpell.id'=>$res['pid']])->first()->toArray();
            $data['headimgurl_array'][] = adminDomain().$result_one['headimg_url'];
            $data['endTime'] = strtotime($result_one['created_at']) + 172800;
            if ($data['endTime'] < time()) returnJson(1, 'the spell is expired', 1);
            $result_two = $ordersSpell->select('user_info.headimg_url')->leftjoin('user_info', 'ordersSpell.userId', '=', 'user_info.id')->where(['ordersSpell.status'=>'1', 'ordersSpell.pid'=>$res['pid']])->get()->toArray();
            foreach ($result_two as $v) {
                $data['headimgurl_array'][] = adminDomain().$v['headimg_url'];
            }
        }
        $data['id'] = $res['id'];
        $data['goods_info'] = $item->select('pic', 'title', 'id')->where(['id'=>$res['itemId']])->first()->toArray();
        $data['goods_info']['pic'] = adminDomain().$data['goods_info']['pic'];
        $data['goods_info']['price'] = (new Sku)->getMinPrice($res['itemId']);
        $data['goods_info']['originalPrice'] = (new Sku)->getMaxPrice($res['itemId'], 1);
        $data['spellInfo'] = [
            'total' => (new OrdersSpell())->getTotalNumber($res['itemId']),
            'spellList' => (new OrdersSpell())->getList($res['itemId'], $param['orderNumber']),
        ];
        returnJson(1, 'success', $data);
    }
}

