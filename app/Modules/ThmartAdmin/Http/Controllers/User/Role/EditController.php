<?php
namespace App\Modules\ThmartAdmin\Http\Controllers\User\Role;

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
        if (isset($param['id'])) {
        	$res = objectToArray(json_decode(curl_post_https(apiDomain().'thmartApi/Staff/Role/detail', ['id'=>$param['id']], $_COOKIE)));
        	if ($res['code'] == 1) {
                $authIdArray = DB::table('staffRoleAuth')->select('auth_id')->where('role_id', $param['id'])->get()->toArray();
                $data = [];
                foreach ($authIdArray as $v) {
                    array_push($data, $v->auth_id);
                }
                $res['data']['0']['authIdArray'] = $data;
                $compactData['data'] = $res['data'];
            }
        }
        $compactData['auth'] = DB::table('staffAuth')->where('isDelete', 0)->select('authName', 'id')->get()->toArray();
    	return view('thmartAdmin::User/Role/edit', compact('compactData'));
    }
}

