<?php
namespace App\Modules\ThmartApi\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use foo\Foo;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Category;
use Illuminate\Http\Request;

class LoopListController extends Controller
{ 
    public function __construct()
    {

    }

	public function index(Request $request)
	{
		$res = Category::select('name as id', 'fname', 'title')->where('itemNumber', '>', 0)->get()->toArray();
		$res = loop($res);
		foreach ($res as &$v) {
            $v['isSubShow'] = false;
            $v['isUp'] = false;
            foreach ($v['son'] as &$value) {
                $value['isSubShow'] = false;
                $value['isUp'] = false;
            }
        }
		returnJson(1, 'success', $res);
	}
}

