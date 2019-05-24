<?php
namespace App\Modules\ThmartAdmin\Http\Controllers\User\Role;

use App\Modules\ThmartAdmin\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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
        $res = objectToArray(json_decode(https_request(apiDomain().'thmartApi/Staff/Role/list?page='.$param['page'].'&pageSize='.$pageSize, null, $_COOKIE)));
        $compactData['list'] = $res['data'];
        foreach ($compactData['list']['data'] as &$v) {
            $data = DB::table('staffRoleAuth')
                ->select('staffAuth.authName')
                ->where('staffRoleAuth.role_id', $v['id'])
                ->leftjoin('staffAuth', 'staffAuth.id', '=', 'staffRoleAuth.auth_id')
                ->get()
                ->toArray();
            $v['authArray'] = $data;
        }
        unset($v);
        $compactData['page'] = DB::table('staffRole')->where('isDelete', 0)->paginate($pageSize);
    	return view('thmartAdmin::User/Role/list', compact('compactData'));
    }
}

