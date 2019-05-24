<?php
namespace App\Modules\ThmartAdmin\Http\Controllers\Item;

use App\Modules\ThmartAdmin\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Modules\ThmartApi\Models\Supplier;
use App\Modules\ThmartApi\Models\staff_supplier;

class FirstController extends Controller
{ 

    public function index(Request $request)
    {
        $supplierArray = [];
        $param = $request->input();
        $compactData = $this->compactData;
        $compactData['supplierList'] = DB::table('supplier')->select('id', 'supplier_name')->where(['is_delete'=>'0','is_effective'=>'1'])->get()->toArray();
        $res = DB::table('staff_supplier')->where('staff_id', session()->get('userInfo')['id'])->get()->toArray();
        foreach ($res as $v) {
            array_push($supplierArray, $v->supplier_id);
        }
        //当前用户绑定的供应商，如果是管理员则默认所有
        if (!in_array(1, session()->get('userInfo')['roleArray'])) {
            foreach ($compactData['supplierList'] as $k => $v) {
                if (!in_array($v->id, $supplierArray)) unset($compactData['supplierList'][$k]);
            }
        }
        /*$data = objectToArray(json_decode(curl_post_https(apiDomain().'thmartApi/Category/list', ['fname'=>0], $_COOKIE)));*/
        $data = DB::table('category')->select('name as id', 'title', 'title_cn')->where(['fname'=>0])->get()->toArray();
        $data = objectToArray($data);
        $compactData['catOneList'] = $data;
        return view('thmartAdmin::Item/first', compact('compactData'));
    }
}

