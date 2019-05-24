<?php
namespace App\Modules\ThmartAdmin\Http\Controllers;

use App\Modules\ThmartAdmin\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Modules\ThmartApi\Models\Staff;

class TestController extends Controller
{ 

    public function index(Request $request)
    {
    	dump(123);
    }
}

