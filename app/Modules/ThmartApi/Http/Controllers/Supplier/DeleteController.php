<?php
namespace App\Modules\ThmartApi\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Supplier;
use Illuminate\Http\Request;

class DeleteController extends Controller
{ 
    public function __construct(){}
    
    public function index(Request $request)
    {
        $param = $request->input();
        (new Supplier)->saveOne(['id'=>$param['id'], 'is_delete'=>1]);
        returnJson(1, '删除成功');
    }
}