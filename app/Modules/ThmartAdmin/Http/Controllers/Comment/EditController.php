<?php
/**
 * Created by yl.
 * User: yl
 * Date: 2019/3/4
 * Time: 17:05
 */

namespace App\Modules\ThmartAdmin\Http\Controllers\Comment;

use App\Modules\ThmartAdmin\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Modules\ThmartApi\Models\MongoComment;
use App\Modules\ThmartApi\Models\Item;

class EditController extends Controller
{

    public function index(Request $request)
    {
        $compactData = $this->compactData;
        if (session()->get('pageSize')) {
            $pageSize = session()->get('pageSize');
        } else {
            $pageSize = 10;
        }
        $param = $request->input();
        $compactData['item'] = Item::select('id', 'title')->where(['isDelete'=>0, 'audited'=>1])->get()->toArray();
        return view('thmartAdmin::Comment/detail', compact('compactData'));
    }
}