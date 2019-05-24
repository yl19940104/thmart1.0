<?php
namespace App\Modules\ThmartAdmin\Http\Controllers\Supplier;

use App\Modules\ThmartAdmin\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Modules\ThmartApi\Models\Supplier;

class ListController extends Controller
{ 

    public function index(Request $request)
    {
        $param = $request->input();
        $compactData = $this->compactData;
        if (session()->get('pageSize')) {
            $pageSize = session()->get('pageSize');
        } else {
            $pageSize = 10;
        }
        if (!isset($param['page'])) $param['page'] = 1;
        $compactData['data'] = DB::table('supplier')->select('id', 'supplier_name', 'is_effective')->where('is_delete', 0)->orderby('id', 'desc')->paginate($pageSize);
        return view('thmartAdmin::Supplier/list', compact('compactData'));
    }
}

