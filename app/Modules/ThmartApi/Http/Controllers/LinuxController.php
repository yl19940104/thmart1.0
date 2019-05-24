<?php
namespace App\Modules\ThmartApi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class LinuxController extends Controller
{ 

    public function __construct(){}

	public function index(Request $request)
	{
		for ($i=0; $i<60; $i++) {
			DB::table('z')->insert(['param'=>$i]);
			sleep(1);
		}
	}
}

