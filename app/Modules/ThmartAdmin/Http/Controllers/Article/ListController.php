<?php
namespace App\Modules\ThmartAdmin\Http\Controllers\Article;

use App\Modules\ThmartAdmin\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Modules\ThmartApi\Models\Article;

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
        $compactData['data'] = DB::table('article')->select('id', 'title', 'description')
            ->where('is_delete', 0)
            ->orderby('id', 'desc')
            ->paginate($pageSize);
        return view('thmartAdmin::Article/list', compact('compactData'));
    }
}

