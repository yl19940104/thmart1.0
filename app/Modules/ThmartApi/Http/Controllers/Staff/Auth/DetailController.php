<?php
namespace App\Modules\ThmartApi\Http\Controllers\Staff\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\StaffAuth;
use Illuminate\Http\Request;

class DetailController extends Controller
{ 
    public function __construct(){}
    
    public function index(Request $request)
    {
        $param = $request->input();
        if (!$res = (new StaffAuth)->getOne($param['id'])) returnJson(0, '该id不存在');
        returnJson(1, 'success', $res);
    }
}