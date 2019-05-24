<?php
namespace App\Modules\ThmartApi\Http\Controllers\Ads;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\Ads;
use App\Modules\ThmartApi\Models\AdsPosition;
use App\Modules\ThmartApi\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ListController extends Controller
{ 
    public function __construct(){}
    
	public function index(Request $request)
    {
        $param = $request->input();
        $validator = $this->validateParam($request);
        if ($validator->fails()) returnJson(0, $validator->getMessageBag());
        $ads = new Ads;
        $adsPosition = new AdsPosition;
        if (!$res = $adsPosition->getOneById($param['id'])) returnJson(0, '广告位置id不存在');
        $param['sort'] = ' a.'.str_replace('_', ' ' , $param['sort']);
        if ($search = $request->input('search')) $search = addslashes($search);
        $res = $ads->getList($param['id'], $res['type'], null, $param['sort'], $search);
        $res = pageData($res, $param['page'], $param['pageSize']);
        //deals列表页返回banner图
        if ($param['id'] == 17) {
            if (isset($param['terminal']) && $param['terminal'] == 'PC') $res['banner'] = (new Ads)->getList(38, 3);
        }
        //品牌列表页返回推荐商品
        if ($param['id'] == 5) {
            $res['recommend'] = (new Item)->recommendList();
        }
        //deals列表页返回deal的banner图
        if ($param['id'] == 17) {
            $res['banner'] = (new Ads)->getList(22, 3);
        }
        returnJson(1, 'success', $res);
    }

    public function validateParam(Request $request)
    {
        $validator = \Validator::make($request->input(), [
            'id'       => 'required|integer',
            'page'     => 'required|integer',
            'pageSize' => 'required|integer',
            'sort'     => ['required', Rule::in(['order_asc', 'price_desc', 'price_asc', 'createTime_desc', 'sellNumber_desc'])],
        ], [
            'required' => ':attribute 为必填项',
            'integer'  => ':attribute 必须为数字',
        ], [
            'id'       => '广告位置id',
            'page'     => '当前页',
            'pageSize' => '总页数',
            'sort'     => '顺序',
        ]);
        return $validator;
    }
}

