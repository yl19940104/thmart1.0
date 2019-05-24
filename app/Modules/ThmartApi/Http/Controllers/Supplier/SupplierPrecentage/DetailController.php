<?php
namespace App\Modules\ThmartApi\Http\Controllers\Supplier\SupplierPrecentage;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\SupplierPrecentage;
use Illuminate\Http\Request;

class DetailController extends Controller
{ 
    public function __construct(){}
    
    public function index(Request $request)
    {
        $param = $request->input();
        if (isset($param['supplierId']) && isset($param['catOneId']) && isset($param['catTwoId'])) {
            $res = (new SupplierPrecentage)->getOne($param['supplierId'], $param['catOneId'], $param['catTwoId']);
            if (!empty($res)) {
                returnJson(1, 'success', $res);
            } else {
                returnJson(0, 'not found');
            }
        }
    }
}