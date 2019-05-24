<?php
namespace App\Modules\ThmartAdmin\Http\Controllers\Supplier;

use App\Modules\ThmartAdmin\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Modules\ThmartApi\Models\Supplier;

class DetailController extends Controller
{ 

    public function index(Request $request)
    {
        $param = $request->input();
        $compactData = $this->compactData;
        $res = DB::table('supplier')->select('id')->where('staff_id', session()->get('userInfo')['id'])->get()->toArray();
        //如果非超级管理员且已经上传过供应商信息，超级管理员可以无限添加供应商
        /*if (!empty($res) && !in_array(1, $compactData['roleArray'])) $compactData['id'] = $res['0']->id;*/
        $compactData['get'] = $param;
        return view('thmartAdmin::Supplier/detail', compact('compactData'));
    }
}

