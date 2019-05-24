<?php

namespace App\Http\Middleware;

use App\Models\Item;
use Closure;
use Illuminate\Support\Facades\DB;

/*
 * 验证token中间件
 */
class CheckToken
{

    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}
