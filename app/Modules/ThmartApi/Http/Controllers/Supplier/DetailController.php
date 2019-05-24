<?php
namespace App\Modules\ThmartApi\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Supplier;
use App\Modules\ThmartApi\Models\SupplierPrecentage;
use Illuminate\Http\Request;

class DetailController extends Controller
{ 
    public function __construct(){}
    
    public function index(Request $request)
    {
        $param = $request->input();
        $res = DB::table('supplier')
        	->select('supplier_name', 'contacts_name', 'contacts_phone', 'contacts_email', 'contacts_address', 'param', 'sale', 'remark', 'number', 'id', 'is_effective')->where('id', $param['id'])
        	->get();
        if (isset($res['0'])) {
        	$res['0']->pointList = (new SupplierPrecentage)->getList($res['0']->id);
        	returnJson(1, 'success', $res->toArray());
        } else {
            returnJson(0, 'no point');
        }
    }
}