<?php
namespace App\Modules\ThmartAdmin\Http\Controllers\Article;

use App\Modules\ThmartAdmin\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Modules\ThmartApi\Models\Article;

class DetailController extends Controller
{ 

    public function index(Request $request)
    {
        $param = $request->input();
        $compactData = $this->compactData;
        return view('thmartAdmin::Article/detail', compact('compactData'));
    }
}

