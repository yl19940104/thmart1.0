<?php
namespace App\Modules\ThmartAdmin\Http\Controllers\User\Auth;

use App\Modules\ThmartAdmin\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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
        $res = objectToArray(json_decode(https_request(apiDomain().'thmartApi/Staff/Auth/list?page='.$param['page'].'&pageSize='.$pageSize, null, $_COOKIE)));
        $compactData['list'] = $res['data'];
        $compactData['page'] = DB::table('staffAuth')->where('isDelete', 0)->paginate($pageSize);
    	return view('thmartAdmin::User/Auth/list', compact('compactData'));
    }
}

