<?php
namespace App\Modules\ThmartAdmin\Http\Controllers\User\Auth;

use App\Modules\ThmartAdmin\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class EditController extends Controller
{ 

    public function index(Request $request)
    {
    	$param = $request->input();
        $compactData = $this->compactData;
        if (isset($param['id'])) {
        	$res = objectToArray(json_decode(curl_post_https(apiDomain().'thmartApi/Staff/Auth/detail', ['id'=>$param['id']], $_COOKIE)));
        	if ($res['code'] == 1) $compactData['data'] = $res['data'];
        }
    	return view('thmartAdmin::User/Auth/edit', compact('compactData'));
    }
}

