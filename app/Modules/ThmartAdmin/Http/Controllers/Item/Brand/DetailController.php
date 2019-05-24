<?php
namespace App\Modules\ThmartAdmin\Http\Controllers\Item\Brand;

use App\Modules\ThmartAdmin\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Modules\ThmartApi\Models\Brand;

class DetailController extends Controller
{ 

    public function index(Request $request)
    {
        $param = $request->input();
        $compactData = $this->compactData;
        if (isset($param['id']) && $param['id']) {
        	$res = DB::table('brand')->select('name', 'pic', 'name_cn', 'id')->where('id', $param['id'])->get()->toArray();
        	$compactData['data'] = $res;
        }
        return view('thmartAdmin::Item/Brand/detail', compact('compactData'));
    }
}

