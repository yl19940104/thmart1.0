<?php
namespace App\Modules\ThmartAdmin\Http\Controllers\Ads;

use App\Modules\ThmartAdmin\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Modules\ThmartApi\Models\AdsPosition;

class PositionListController extends Controller
{ 

    public function index(Request $request)
    {
        $param = $request->input();
        $compactData = $this->compactData;
        if (session()->get('pageSize')) {
            $pageSize = session()->get('pageSize');
        } else {
            $pageSize = 20;
        }
        $compactData['setPositionData'] = DB::table('adsPosition')
            ->select('name', 'pid', 'status')
            ->where([['pid', '!=', 0]])
            ->paginate($pageSize);
        foreach ($compactData['setPositionData'] as $k => &$v) {
            $data = DB::table('adsPosition')
                ->select('name')
                ->where(['id'=>$v->pid])
                ->get()
                ->toArray();
            $v->modules = $data['0']->name;
        }
        unset($v);
        return view('thmartAdmin::Ads/position', compact('compactData'));
    }
}

