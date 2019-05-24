<?php
namespace App\Modules\ThmartApi\Http\Controllers\Staff\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\StaffSupplier;
use Illuminate\Http\Request;

class ListController extends Controller
{ 
    public function __construct(){}
    
    public function index(Request $request)
    {
        $param = $request->input();
    	$res = (new StaffSupplier)->staffSupplierList($param['staff_id']);
        returnJson(1, 'success', $res);
    }
}