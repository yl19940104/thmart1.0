<?php

namespace App\Http\Middleware;

use App\Models\Item;
use Closure;
use Illuminate\Support\Facades\DB;

/*
 * 验证token中间件
 */
class CheckSession
{

    public function handle($request, Closure $next)
    {
    	if (empty(session()->get('userInfo'))) return redirect('thmartAdmin/login');
        return $next($request);
    }
}
