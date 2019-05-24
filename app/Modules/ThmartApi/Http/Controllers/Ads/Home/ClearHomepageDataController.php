<?php
namespace App\Modules\ThmartApi\Http\Controllers\Ads\Home;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Ads;
use App\Modules\ThmartApi\Models\Item;
use App\Modules\ThmartApi\Models\CouponSku;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class ClearHomepageDataController extends Controller
{ 
    public function __construct(){}
    
	public function index(Request $request)
	{
        Cache::forget('homepageDataPc');
        Cache::forget('homepageDataMobile');
        returnJson(1, 'success');
    }
}

