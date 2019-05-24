<?php
namespace App\Modules\ThmartApi\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Supplier;
use Illuminate\Http\Request;

class EffectiveController extends Controller
{ 
    public function __construct(){}
    
    public function index(Request $request)
    {
        $param = $request->input();
        //审核供应商的有效性
        $param['is_effective'] = 1;
        $param['check_staff_id'] = session()->get('userInfo')['id'];
        (new Supplier)->saveOne($param);
        returnJson(1, 'success');
    }
}