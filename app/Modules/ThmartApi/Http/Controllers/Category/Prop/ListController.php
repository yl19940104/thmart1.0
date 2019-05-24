<?php
namespace App\Modules\ThmartApi\Http\Controllers\Category\Prop;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\PropTemplate;
use Illuminate\Http\Request;

class ListController extends Controller
{ 
	public function __construct(){}
	
	public function index(Request $request)
	{
		$res = DB::table('proptemplate')->select('id', 'name')->where('type', 1)->whereNotIn('id', [170])->get()->toArray();
		returnJson(1, 'success', $res);
	}
}

