<?php
namespace App\Modules\ThmartAdmin\Http\Controllers\User;

use App\Modules\ThmartAdmin\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Modules\ThmartApi\Models\Staff;

class ListController extends Controller
{ 

    public function index(Request $request)
    {
    	$param = $request->input();
        $compactData = $this->compactData;
        /*$res = objectToArray(json_decode(curl_post_https(apiDomain().'thmartApi/Staff/list?page='.$param['page'].'&pageSize', null, $_COOKIE)));*/
        if (session()->get('pageSize')) {
        	$pageSize = session()->get('pageSize');
        } else {
        	$pageSize = 10;
        }
        if (!isset($param['page'])) $param['page'] = 1;
        $res = objectToArray(json_decode(https_request(apiDomain().'thmartApi/Staff/list?page='.$param['page'].'&pageSize='.$pageSize, null, $_COOKIE)));
        $compactData['userList'] = $res['data'];
        $compactData['page'] = DB::table('staff')->where('isDelete', 0)->paginate($pageSize);
    	return view('thmartAdmin::User/list', compact('compactData'));
    }
}

