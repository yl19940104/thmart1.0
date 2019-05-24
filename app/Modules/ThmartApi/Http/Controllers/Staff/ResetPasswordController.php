<?php
namespace App\Modules\ThmartApi\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Staff;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{ 
    public function __construct(){}
    
    public function index(Request $request)
    {
        $param = $request->input();
        $res = (new Staff)->getOneById($param['id']);
        $password = md5Password($param['password'], $res['0']['salt']);
        (new Staff)->saveOne(['id'=>$param['id'], 'password'=>$password]);
        returnJson(1, '修改成功');
    }
}