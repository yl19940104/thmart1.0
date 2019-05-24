<?php
namespace App\Modules\ThmartAdmin\Http\Controllers\Index;

use App\Modules\ThmartAdmin\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class HomepageController extends Controller
{ 

    public function index(Request $request)
    {
    	$compactData = $this->compactData;
        $compactData['host'] = $_SERVER['HTTP_HOST'];
        $compactData['ip'] = $_SERVER['SERVER_ADDR'];
        $compactData['system'] = php_uname('s');
        $compactData['phpVersion'] = PHP_VERSION;
        $compactData['software'] = $_SERVER['SERVER_SOFTWARE'];
        $compactData['upload'] = ini_get("upload_max_filesize");
        $compactData['time'] = date("Y-m-d H:i:s", time());
    	return view('thmartAdmin::Index/homepage', compact('compactData'));
    }
}

