<?php
namespace App\Modules\ThmartAdmin\Http\Controllers\Item\Brand;

use App\Modules\ThmartAdmin\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Modules\ThmartApi\Models\Supplier;
use App\Modules\ThmartApi\Models\Item;

class ListController extends Controller
{ 

    public function index(Request $request)
    {
        $param = $request->input();
        $compactData = $this->compactData;
        if (session()->get('pageSize')) {
            $pageSize = session()->get('pageSize');
        } else {
            $pageSize = 10;
        }
        $compactData['data'] = DB::table('brand')->select('id', 'name', 'status')
            ->where('isDelete', 0)
            ->orderby('id', 'desc')
            ->paginate($pageSize);
        return view('thmartAdmin::Item/Brand/list', compact('compactData'));
    }
}

