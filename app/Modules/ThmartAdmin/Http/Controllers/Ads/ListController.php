<?php
namespace App\Modules\ThmartAdmin\Http\Controllers\Ads;

use App\Modules\ThmartAdmin\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Modules\ThmartApi\Models\Ads;

class ListController extends Controller
{ 

    public function index(Request $request)
    {
        $param = $request->input();
        $compactData = $this->compactData;
        if (session()->get('pageSize')) {
            $pageSize = session()->get('pageSize');
        } else {
            $pageSize = 90;
        }
        if (!isset($param['id'])) $param['id'] = 3;
        $compactData['position'] = DB::table('adsPosition')->where('id', $param['id'])->get()->toArray();
        if ($param['id'] != 9 && $param['id'] != 10) {
            $orderOne = 'order';
            $orderTwo = 'desc';
        } else {
            $orderOne = 'merchantId';
            $orderTwo = 'desc';
        }
        $compactData['data'] = DB::table('ads')
            ->select('contentId', 'pic', 'order', 'id', 'pic', 'url', 'merchantId')
            ->where('adsPositionId', $param['id'])
            ->orderby($orderOne, $orderTwo)
            ->paginate($pageSize);
        foreach ($compactData['data'] as &$value) {
            if ($value->merchantId) {
                $res = DB::table('brand')->where('id', $value->merchantId)->select('name as brandName')->get()->toArray();
                $value->brandName = $res['0']->brandName;
            }
        }
        //如果type=3,列表页图片就是配置图片
        if ($compactData['position']['0']->type == 3) {
            foreach ($compactData['data'] as &$v) {
                $v->content = $v->pic;
            }
        }
        //如果type=1,列表页图片是商品主图
        if ($compactData['position']['0']->type == 1) {
            foreach ($compactData['data'] as &$v) {
                $res = DB::table('item')->select('id','pic')->where('id', $v->contentId)->get()->toArray();
                if ($res) $v->content = $res['0']->pic;
            }
        }
        //如果type=2,列表页图片是商户主图
        if ($compactData['position']['0']->type == 2) {
            foreach ($compactData['data'] as &$v) {
                $res = DB::table('brand')->select('id', 'pic')->where('id', $v->contentId)->get()->toArray();
                if ($res) $v->content = $res['0']->pic;
            }
        }
        //如果type=4,列表页图片是文章
        if ($compactData['position']['0']->type == 4) {
            foreach ($compactData['data'] as &$v) {
                $res = DB::table('article')->select('id', 'pic')->where('id', $v->contentId)->get()->toArray();
                if ($res) $v->content = $res['0']->pic;
            }
        }
        unset($v);
        $data = DB::table('adsPosition')
            ->select('id', 'pid as fname', 'name', 'sort')
            ->where('status', 1)
            ->orderby('sort', 'asc')
            ->get()
            ->toArray();
        $data = json_decode(json_encode($data), true);
        $compactData['cat'] = loop($data);
        $compactData['positionList'] = DB::table('adsPosition')->select('name', 'id')->where([['pid', '!=', 0]])->get()->toArray();
        $compactData['itemList'] = DB::table('item')->select('title', 'id')->where(['isDelete'=>0])->get()->toArray();
        $compactData['brandList'] = DB::table('brand')->select('name', 'id')->where(['isDelete'=>0])->get()->toArray();
        $compactData['articleList'] = DB::table('article')->select('title', 'id')->where(['is_delete'=>0])->get()->toArray();
        $compactData['get'] = $request->input();
        return view('thmartAdmin::Ads/list', compact('compactData'));
    }
}

