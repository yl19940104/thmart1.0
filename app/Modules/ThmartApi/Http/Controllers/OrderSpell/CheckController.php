<?php
namespace App\Modules\ThmartApi\Http\Controllers\OrderSpell;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\OrdersSpell;
use Illuminate\Http\Request;

class CheckController extends Controller
{

    public function index(Request $request)
    {
        $param = $request->input();
        $ordersSpell = new OrdersSpell();
        $res = $ordersSpell->where(['orderNumber'=>$param['orderNumber']])->first()->toArray();
        //如果该订单号是发起拼单
        if ($res['pid'] == 0) {
            if ((strtotime($res['created_at']) + 172800) < time()) returnJson(1, 'the spell is expired', 1);
            if ($res['userId'] == $this->userId) returnJson(1, 'already spell', 2);
            $result = $ordersSpell->where(['pid'=>$res['id']])->get()->toArray();
            foreach ($result as $v) {
                if ($v['userId'] == $this->userId) returnJson(1, 'already spell', 2);
            }
        } elseif ($res['pid'] != 0) {
            $result = $ordersSpell->where(['id'=>$res['pid']])->first()->toArray();
            if ((strtotime($result['created_at']) + 172800) < time()) returnJson(1, 'the spell is expired', 1);
            if ($result['userId'] == $this->userId) returnJson(1, 'already spell', 2);
            $res = $ordersSpell->where(['pid'=>$result['id']])->get()->toArray();
            foreach ($res as $v) {
                if ($v['userId'] == $this->userId) returnJson(1, 'already spell', 2);
            }
        }
        returnJson(1, 'success', 3);
    }
}