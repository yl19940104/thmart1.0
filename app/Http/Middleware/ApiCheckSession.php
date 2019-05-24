<?php

namespace App\Http\Middleware;

use App\Models\Item;
use Closure;
use Illuminate\Support\Facades\DB;

/*
 * 验证token中间件
 */
class ApiCheckSession
{

    public function handle($request, Closure $next)
    {
    	if (empty(session()->get('userInfo'))) returnJson(0, 'wrong session');
    	return $next($request);
    }
}
