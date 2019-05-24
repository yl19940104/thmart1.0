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

class ListController extends Controller
{

    public function index(Request $request)
    {
        if (session()->get('pageSize')) {
            $pageSize = session()->get('pageSize');
            $pageSize = intval($pageSize);
        } else {
            $pageSize = 10;
        }
        $param = $request->input();
        $condition = ['isDelete'=>0];
        if (isset($param['id']) && $param['id']) $condition['goodsId'] = intval($param['id']);
        if (isset($param['audited'])) $condition['audited'] = intval($param['audited']);
        if (isset($param['title'])) array_push($condition, ['info.title', 'like', "%{$param['title']}%"]);
        $compactData = $this->compactData;
        $compactData['data'] = MongoComment::where($condition)->orderBy('created_at', 'desc')->paginate($pageSize);
        foreach ($compactData['data'] as &$v) {
            if (isset($v->info['pic'])) {
                $data = explode('|', $v->info['pic']);
                $v->pic = $data;
            }
        }
        unset($v);
        $compactData['get'] = $request->input();
        $compactData['item'] = Item::select('id', 'title')->where(['isDelete'=>'0', 'audited'=>'1'])->get()->toArray();
        return view('thmartAdmin::Comment/list', compact('compactData'));
    }
}