<?php
namespace App\Modules\ThmartApi\Http\Controllers\Staff\Role;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\StaffRole;
use Illuminate\Http\Request;

class DeleteController extends Controller
{ 
    public function __construct(){}
    
    public function index(Request $request)
    {
        $param = $request->input();
        (new StaffRole)->updateOne(['id'=>$param['id'], 'isDelete'=>1]);
        returnJson(1, '删除成功');
    }
}