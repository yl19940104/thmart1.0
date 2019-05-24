<?php
namespace App\Modules\ThmartApi\Http\Controllers\OrderSpell;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\OrdersSpell;
use Illuminate\Http\Request;

class CheckTwoController extends Controller
{

    public function index(Request $request)
    {
        $param = $request->input();
        $ordersSpell = new OrdersSpell();
        $res = $ordersSpell->checkSpellOne($this->userId, $param['spellId']);
        returnJson(1, 'success');
    }
}