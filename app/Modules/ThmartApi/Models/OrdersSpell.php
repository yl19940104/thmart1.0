<?php
/**
 * Created by yl.
 * User: yl
 * Date: 2019/5/8
 * Time: 15:27
 */
namespace App\Modules\ThmartApi\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Modules\ThmartApi\Models\Sku;
use App\Modules\ThmartApi\Models\OrdersSpell;
use App\Modules\ThmartApi\Models\Orders;

class OrdersSpell extends Model
{
    protected $table = "ordersSpell";
    //必须指定参数白名单，否则create函数无法执行
    protected $fillable = ['orderNumber', 'itemId', 'amount', 'userId', 'pid'];
    //为true表示记录添加时间和更新时间
    public $timestamps = true;

    public function getList($itemId, $orderNumber=null){
        $time = date('Y-m-d H:i:s', time()-172800);
        $res = $this->select('ordersSpell.id', 'amount', 'ordersSpell.created_at', 'headimg_url', 'nickname', 'orderNumber')->where(['itemId'=>$itemId, 'pid'=>0, 'ordersSpell.status'=>1, ['ordersSpell.created_at', '>=', $time]])->leftJoin('user_info', 'user_info.id', '=', 'ordersSpell.userId')->get()->toArray();
        //如果传$orderNumber，则返回的结果要去除自己本身的拼单
        if (isset($orderNumber)) {
            $result = (new OrdersSpell)->where(['orderNumber'=>$orderNumber])->first()->toArray();
            if ($result['pid'] == 0) {
                $id = $result['id'];
            } else {
                $id = $result['pid'];
            }
        }
        foreach ($res as $k => &$v) {
            if (!$v['headimg_url']) $v['headimg_url'] = config('config.headimg');
            $v['endTime'] = strtotime($v['created_at']) + 172800;
            $v['currentTime'] = time();
            $number_left = $this->getLeft($v['id']);
            if ($number_left <= 0 || (isset($id) && $v['id'] == $id)) {
                unset($res[$k]);
            } else {
                $v['number_left'] = $number_left;
                $v['headimg_url'] = adminDomain().$v['headimg_url'];
            }
        }
        $res = array_values($res);
        return $res;
    }

    //拼单还剩多少人
    public function getLeft($id){
        $res = $this->where(['id'=>$id])->first()->toArray();
        $res_second = $this->where(['pid'=>$id, 'status'=>1])->count();
        return $res['amount'] - $res_second - 1;
    }

    //获取某商品总共拼单的人数
    public function getTotalNumber($itemId){
        $data = date('Y-m-d H:i:s', time()-172800);
        $res = $this->where(['itemId'=>$itemId, 'pid'=>0, 'status'=>1, ['created_at', '>=', $data]])->get()->toArray();
        $number = count($res);
        foreach ($res as &$v) {
            $number_two = $this->where(['pid'=>$v['id'], 'status'=>1])->count();
            $number += $number_two;
        }
        return $number;
    }

    //一个用户自己不能拼自己的单
    public function checkSpellOne($userId, $spellId){
        $res = $this->where(['id'=>$spellId])->first()->toArray();
        if ($res['userId'] == $userId) returnJson(123, 'already spelled');
        $result = $this->where(['pid'=>$res['id']])->get()->toArray();
        foreach ($result as $v) {
            if ($v['userId'] == $userId) returnJson(123, 'already spelled');
        }
    }

    //一个用户48小时内不能同时对一个商品发起两个拼单
    public function checkSpellTwo($userId, $itemId){
        $time = date('Y-m-d H:i:s', time()-172800);
        $res = $this->where(['userId'=>$userId, ['ordersSpell.created_at', '>=', $time], 'itemId'=>$itemId, 'pid'=>0, 'status'=>1])->get()->toArray();
        if (isset($res) && $res) {
            returnJson(124, 'cant spell twice in 48 hours');
        }
    }

    //如果该订单号拼单成功，返回true，否则返回false
    public function changeOrderStatus($orderNumber){
        $orderNumberArray = [];
        $res = $this->where(['orderNumber'=>$orderNumber])->first()->toArray();
        if ($res['pid'] == 0) {
            $orderNumberArray[] = $res['orderNumber'];
            $pid = $res['id'];
            $left = $this->getLeft($res['id']);
        } else {
            $result = $this->where(['id'=>$res['pid']])->first()->toArray();
            $orderNumberArray[] = $result['orderNumber'];
            $pid = $result['id'];
            $left = $this->getLeft($res['pid']);
        }
        //如果该订单号拼单成功，则把此拼单号对应所有订单号都改成状态7
        if ($left <= 0) {
            $data = $this->where(['pid'=>$pid, 'status'=>1])->get()->toArray();
            foreach ($data as $v) {
                $orderNumberArray[] = $v['orderNumber'];
            }
            foreach ($orderNumberArray as $v) {
                Orders::whereIn('orderNumber', $orderNumberArray)->update(['status'=>7]);
            }
            return true;
        } else {
            return false;
        }
    }

    //把所有已支付拼单过期未拼满的订单改成拼单成功
    public function changeStatusToSeven(){
        $spellIdArray = [];
        $orderNumberArray = [];
        $data = date('Y-m-d H:i:s', time()-172800);
        $res = $this->where(['status'=>1, 'pid'=>0, 'crontabStatus'=>0, ['created_at', '<', $data]])->get()->toArray();
        foreach ($res as $v) {
            $left = $this->getLeft($v['id']);
            if ($left > 0) {
                $spellIdArray[] = $v['id'];
                $orderNumberArray[] = $v['orderNumber'];
                $result = $this->where(['pid'=>$v['id']])->get()->toArray();
                foreach ($result as $value) {
                    $orderNumberArray[] = $value['orderNumber'];
                }
            }
        }
        foreach ($spellIdArray as $v) {
            $this->where(['id'=>$v])->update(['crontabStatus'=>1]);
        }
        foreach ($orderNumberArray as $v) {
            (new Orders)->where(['orderNumber'=>$v])->update(['status'=>7]);
        }
    }
}