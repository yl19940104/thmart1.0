<?php

namespace App\Modules\ThmartAdmin\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use App\Modules\ThmartApi\Models\AdminListCat;
use App\Modules\ThmartApi\Models\Staff;
use Illuminate\Pagination\LengthAwarePaginator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    //渲染数据
    protected $compactData = null;
    protected $userData = [];

    public function __construct()
    {
        //在构造方法执行的时候web中间件还未执行因此无法使用session，所以在构造方法内通过定义中间件来使用session
        $this->middleware(function ($request, $next) {
        	$this->userData['authArray'] = (new Staff)->getAuthArray(session()->get('userInfo')['username']);
            $this->compactData = [
                'authArray' => (new Staff)->getAuthArray(session()->get('userInfo')['username']),
                'roleArray' => (new Staff)->getRoleArray(session()->get('userInfo')['username']),
                'adminListCat' => loop((new AdminListCat)->getOne($this->userData['authArray'])),
            ];
            return $next($request);
        });
    }
}
