<?php
namespace App\Modules\ThmartAdmin\Http\Controllers\User;

use App\Modules\ThmartAdmin\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Modules\ThmartApi\Models\Staff;

class EditController extends Controller
{ 

    public function index(Request $request)
    {
    	$param = $request->input();
        $compactData = $this->compactData;
        $compactData['roleGroup'] = DB::table('staffRole')->select('id', 'roleName')->where('isDelete', 0)->get()->toArray();
        $compactData['supplierList'] = DB::table('supplier')->select('id', 'supplier_name')->where('is_delete', 0)->orderby('id', 'desc')->get()->toArray();
        if (isset($param['id'])) {
        	$compactData['data'] = DB::table('staff')->select('username', 'id')->where('id', $param['id'])->get()->toArray();
        	$data = DB::table('staffInfoRole')->where('staffInfoRole.staff_id', $param['id'])->get()->toArray();
        	$compactData['staffRoleGroup'] = [];
        	foreach ($data as $v) {
        		array_push($compactData['staffRoleGroup'], $v->role_id);
        	}
        }
    	return view('thmartAdmin::User/edit', compact('compactData'));
    }
}

