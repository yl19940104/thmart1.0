<?php
namespace App\Modules\ThmartApi\Http\Controllers\Staff\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\StaffAuth;
use Illuminate\Http\Request;

class DeleteController extends Controller
{ 
    public function __construct(){}
    
    public function index(Request $request)
    {
        $param = $request->input();
        (new StaffAuth)->updateOne(['id'=>$param['id'], 'isDelete'=>1]);
        returnJson(1, '删除成功');
    }
}