<?php
namespace App\Modules\ThmartApi\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Modules\ThmartApi\Models\Orders;
use App\Modules\ThmartApi\Models\OrdersSpell;
use App\Modules\ThmartApi\Models\Sku;
use Illuminate\Http\Request;

class PrepareOrderController extends Controller
{

    public function index(Request $request)
    {
        $param = $request->input();
        /*if (isset($param['isSpell'])) {
            $res = (new Sku)->select('itemId')->where(['id'=>$param['skuId']])->first()->toArray();
            (new OrdersSpell)->checkSpellTwo($this->userId, $res['itemId']);
        }*/
        $array = (new Orders)->getInfo($this->userId, $param);
        if (isset($array['message'])) {
            returnJson(0, $array['message']);
        } else {
            returnJson(1, 'success', $array);
        }
    }
}

